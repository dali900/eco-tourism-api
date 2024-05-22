<?php

namespace App\Repositories;

use App\Models\Place;

class PlaceRepository extends ModelRepository
{
    protected $model;
    private $tableName = 'places';

    public function __construct(Place $model)
    {
        $this->model = $model;
    }

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @return Place
     */
    public function getAllFiltered($params = [])
    {
        $model = $this->model;

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
                $q->orWhereHas('parent_id', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("name", "LIKE", $searchValue);
                $q->orWhere("description", "LIKE", $searchValue);
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
            if (!empty($params['description'])) {
                $fieldName = 'description';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['parent_id'])) {
                $fieldName = 'parent_id';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('parent_id', $value)
                    ->orWhereHas('parent',  function ($query) use ($value) {
                        $query->where('parent_id', $value);
                    });
            }
            if (!empty($params['parent_ids'])) {
                $value = $params['parent_ids'];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->whereIn('parent_ids', $value);
            }
            if (isset($params['visible'])) {
                $fieldName = 'visible';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('visible', $value === "false" ? 0 : 1);
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
                $model = $model->join('users', $this->tableName.".user_id", "=", "users.id")->select($this->tableName.'.*');
                $sortField = "users.name";
            }
            $model = $model->orderBy($sortField, $direction);
        }

        $model->orderBy($this->tableName.".name", "asc");

        return $model;
    }
}
