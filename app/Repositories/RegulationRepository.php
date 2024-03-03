<?php

namespace App\Repositories;

use App\Contracts\RegulationRepositoryInterface;
use App\Models\Regulation;
use App\Models\User;

class RegulationRepository extends ModelRepository implements RegulationRepositoryInterface
{
    protected $model;

    public function __construct(Regulation $model)
    {
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
                //Search relation type belongsTo
                /* $q->orWhereHas('user', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                }); */
                $q->orWhereHas('regulationType', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("name", "LIKE", $searchValue);
                $q->orWhere("messenger", "LIKE", $searchValue);
            });
        }
        //Column search
        else {
            //Search relation type belongsTo
            if (!empty($params['user_name'])) {
                $model = $model->whereHas('user', function ($query) use ($params) {
                    $searchText = $params['user_name'];
                    $matchMode = $params["user_name_MatchMode"] ?? null;
                    $this->searchText('name', $searchText, $matchMode, $query);
                });
            }
            //Search text column
            if (!empty($params['name'])) {
                $fieldName = 'name';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['messenger'])) {
                $fieldName = 'messenger';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['regulation_type_id'])) {
                $fieldName = 'regulation_type_id';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('regulation_type_id', $value)
                    ->orWhereHas('regulationType',  function ($query) use ($value) {
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

            if ($params['sortField'] == 'user_name') {
                //Search relation type belongsTo
                /* $modelTableName = $model->getTable();
                $relationTableName = $model->{$field['relation']}()->getRelated()->getTable();
                $foreignKeyName = $model->{$field['relation']}()->getRelated()->getForeignKey(); */
                $model = $model->join('users', "regulations.user_id", "=", "users.id")->select('regulations.*');
                $sortField = "users.name";
            }
            $model = $model->orderBy($sortField, $direction);
        }

        $model->orderBy("regulations.name", "asc");

        return $model;
    }
}
