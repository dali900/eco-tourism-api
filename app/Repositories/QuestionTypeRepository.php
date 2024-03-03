<?php
namespace App\Repositories;

use App\Models\QuestionType;
use App\Contracts\QuestionTypeRepositoryInterface;

class QuestionTypeRepository extends ModelRepository implements QuestionTypeRepositoryInterface
{
    protected $model;
    
    public function __construct(QuestionType $model) {
        $this->model = $model;
    }

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @return void
     */
    public function getAllFiltered($params = [], $app)
    {
        $tableName = "question_types";
        $model = $this->model;
        $model = $model->where('app', '=', $app);

        //Global search
        if (!empty($params['global'])) {
            $matchMode = $params['global_MatchMode'] ?? null;
            $searchValue = "%" . $params['global'] . "%";
            if ($matchMode === "startsWith") {
                $searchValue = $params['global'] . "%";
            }
            $model = $model->where(function ($q) use ($searchValue, $matchMode) {
                $q->orWhere("name", "LIKE", $searchValue);
            });
        }
        //Column search
        else {
            //Search text column
            if (!empty($params['name'])) {
                $fieldName = 'name';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            //Search primitive
            if (!empty($params['parent_id'])) {
                $model = $model->where('parent_id', $params['parent_id']);
            }
        }

        //Sorting
        if (isset($params['sortField'])) {
            $direction = 'asc';
            if (isset($params['sortOrder'])) {
                if ($params['sortOrder'] == -1) $direction = 'desc';
            }
            $sortField = $params['sortField'];
            if ($params['sortField'] == 'created_at_formated') $sortField = 'id';
            if ($params['sortField'] == 'updated_at_formated') $sortField = 'updated_at';

            $model = $model->orderBy($sortField, $direction);
        }

        $model->orderBy("$tableName.name", "asc");

        return $model;
    }
    
}
