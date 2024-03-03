<?php
namespace App\Repositories;

use App\Contracts\QuestionRepositoryInterface;
use App\Models\Question;
use App\Models\User;

class QuestionRepository extends ModelRepository implements QuestionRepositoryInterface 
{
    protected $model;
    
    public function __construct(Question $model) {
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
        $model = $this->model;
        $model = $model->where('app', '=', $app);
        $authUser = auth()->user();
        if (empty($authUser) || !$authUser->hasAuthorAccess()) { 
            $model = $model->where('approved', '=', 1);
        }

        //Global search
        if (!empty($params['global'])) {
            $matchMode = $params['global_MatchMode'] ?? null;
            $searchValue = "%" . $params['global'] . "%";
            if ($matchMode === "startsWith") {
                $searchValue = $params['global'] . "%";
            }
            $model = $model->where(function ($q) use ($searchValue, $matchMode) {
                $q->orWhere("title", "LIKE", $searchValue);
                $q->orWhere("question", "LIKE", $searchValue);
                $q->orWhere("answer", "LIKE", $searchValue);
                $q->orWhereHas('questionType', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
            });
        }
        //Column search
        else {
            //Search text column
            if (!empty($params['title'])) {
                $fieldName = 'title';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['question'])) {
                $fieldName = 'question';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['answer'])) {
                $fieldName = 'answer';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['author'])) {
                $fieldName = 'author';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['question_type_id'])) {
                $fieldName = 'question_type_id';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('question_type_id', $value)
                    ->orWhereHas('questionType',  function ($query) use ($value) {
                        $query->where('parent_id', $value);
                    });
            }
            if (!empty($params['approved'])) {
                $fieldName = 'approved';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('approved', $value === "false" ? 0 : 1);
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

        $model->orderBy("questions.publish_date", "desc");

        return $model;
	}
}
