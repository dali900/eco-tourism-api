<?php
namespace App\Traits;

use Illuminate\Http\Request;

/**
 * Searche, sort and filter models
 */
trait Filterable
{
    public $limit = 20;

    /**
     * Apply query filters on model
     *
     * @param array $params
     * @return self
     */
    public function filter($params = [])
    {
        $model = new self();
        $originalModel = $model;

        //Global search
        if (!empty($params['global'])) 
        {
            $fields = $model->sortable();
            $matchMode = $params['global_MatchMode'] ?? null;
            $searchValue = "%" . $params['global'] . "%";
            if ($matchMode === "startsWith") {
                $searchValue = $params['global'] . "%";
            }
            $model = $model->where(function($q) use ($fields, $searchValue, $matchMode, $originalModel) {
                foreach ($fields as $field) {
                    if(is_array($field)) {
                        //Search relation type belongsTo
                        if($field['type'] == 'belongsTo'){
                            $q->orWhereHas($field['relation'], function ($query) use ($field, $searchValue, $matchMode) {
                                $searchField = $field['search_column'];
                                self::searchText($searchField, $searchValue, $matchMode, $query);
                            });
                        }
                    } else {
                        $q->orWhere($field, "LIKE", $searchValue);
                    }
                    $originalModel->customFilter($field, $q);
                }
            });
        }
        //Column search
        else
        {
            $fields = $model->sortable();
            foreach ($fields as $field) {
                if(is_array($field)) {
                    if (!empty($params[$field['column']])) {
                        //Search relation type belongsTo
                        if($field['type'] == 'belongsTo'){
                            $model = $model->whereHas($field['relation'], function ($query) use ($params, $field) {
                                $searchField = $field['search_column'];
                                $searchText = $params[$field['column']];
                                $matchMode = $params[$field['column'] . "_MatchMode"] ?? null;
                                self::searchText($searchField, $searchText, $matchMode, $query);
                            });
                        }
                    }
                } else {
                    //Search text column
                    if (!empty($params[$field])) {
                        $searchText = $params[$field];
                        $matchMode = $params[$field . "_MatchMode"] ?? null;
                        $model = self::searchText($field, $searchText, $matchMode, $model);
                    }
                }
                $originalModel->customFilter($field, $model);

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

            foreach ($fields as $field) {
                if(is_array($field)) {
                    if ($params['sortField'] == $field['column']){
                        //Search relation type belongsTo
                        if($field['type'] == 'belongsTo'){
                            $modelTableName = $originalModel->getTable();
                            $relationTableName = $originalModel->{$field['relation']}()->getRelated()->getTable();
                            $foreignKeyName = $originalModel->{$field['relation']}()->getRelated()->getForeignKey();
                            $model = $model->join($relationTableName, "$modelTableName.$foreignKeyName", "=", "$relationTableName.id");
                            $sortField = "$relationTableName.".$field['search_column'];
                        }
                    } 
                }
            }
            //dd($sortField);
            $model = $model->orderBy($sortField, $direction);
        }

        return $model;
    }

    /**
     * Determine operator
     *
     * @param string $searchField
     * @param string $searchText
     * @param string $matchMode
     * @param Model $model
     * @return Model
     */
    private static function searchText($searchField, $searchText, $matchMode, &$model)
    {
        $operator = "LIKE";
        $searchValue = "%" . $searchText . "%"; //contains
        if ($matchMode === "startsWith") {
            $searchValue = $searchText . "%";
        } else if ($matchMode === "endsWith") {
            $searchValue = "%" . $searchText;
        } else if ($matchMode === "equals") {
            $operator = "=";
            $searchValue = $searchText;
        } else if ($matchMode === "notEquals") {
            $operator = "!=";
            $searchValue = $searchText;
        } else if ($matchMode === "contains") {
            $searchValue = "%" . $searchText . "%";
        } else if ($matchMode === "notContains") {
            $operator = "NOT LIKE";
            $searchValue = "%" . $searchText . "%";
        }
        return $model->where($searchField, $operator, $searchValue);
    }

    /**
     * Custom model filter
     *
     * @param array $field
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function customFilter($field, $query)
    {
        
    }

    public function joinRelation()
    {
        throw new \Exception(__METHOD__ . ' method must be implemented.');
    }
}
