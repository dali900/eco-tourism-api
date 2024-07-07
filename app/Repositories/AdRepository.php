<?php

namespace App\Repositories;

use App\Models\Ad;
use App\Models\User;

class AdRepository extends ModelRepository
{
    protected $model;
    private $tableName = 'ads';

    public function __construct(Ad $model)
    {
        $this->model = $model;
    }

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @return Attraction
     */
    public function getAllFiltered($params = [], $queryBuilder = null)
    {
        $model = $queryBuilder ?? $this->model;

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
                $q->orWhereHas('category', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("id", "LIKE", $searchValue);
                $q->orWhere("title", "LIKE", $searchValue);
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
            if (!empty($params['title'])) {
                $fieldName = 'title';
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
            if (!empty($params['category_id'])) {
                $fieldName = 'category_id';
                $value = $params[$fieldName];
                $model = $model->where('category_id', $value)
                    ->orWhereHas('category',  function ($query) use ($value) {
                        $query->where('parent_id', $value);
                    });
            }
            if (!empty($params['category_ids'])) {
                $value = $params['category_ids'];
                $model = $model->whereIn('category_id', $value);
            }
            if (isset($params['approved'])) {
                $fieldName = 'approved';
                $value = $params[$fieldName];
                $model = $model->where('approved', '!=', 0);
            }
            if (!empty($params['active'])) {
                $fieldName = 'active';
                $searchText = $params[$fieldName];
                $model = $model->where('expires_at', '>', date("Y-m-d"));
                $model = $model->where('approved', '!=', 0);
            }
            if (!empty($params['created_at'])) {
                $fieldName = 'created_at';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchDate($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['expires_at'])) {
                $fieldName = 'expires_at';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchDate($fieldName, $searchText, $matchMode, $model);
            }
            if (isset($params['price_min'])) {
                $fieldName = 'price_min';
                $value = $params[$fieldName];
                $model = $model->where('price', '>=', $value);
            }
            if (isset($params['price_max'])) {
                $fieldName = 'price_max';
                $value = $params[$fieldName];
                $model = $model->where('price', '<=', $value);
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
                $model = $model->join('users', $this->tableName.".user_id", "=", "users.id")->select($this->tableName.'.*');
                $sortField = "users.name";
            }
            $model = $model->orderBy($sortField, $direction);
        }

        $model->orderBy($this->tableName.".id", "desc");

        return $model;
    }
}
