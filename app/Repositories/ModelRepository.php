<?php

namespace App\Repositories;

use Transliterator;

class ModelRepository
{
    /**
     * Match modes
        STARTS_WITH: "startsWith",
        CONTAINS: "contains",
        NOT_CONTAINS: "notContains",
        ENDS_WITH: "endsWith",
        EQUALS: "equals",
        NOT_EQUALS: "notEquals",
        IN: "in",
        LESS_THAN: "lt",
        LESS_THAN_OR_EQUAL_TO: "lte",
        GREATER_THAN: "gt",
        GREATER_THAN_OR_EQUAL_TO: "gte",
        BETWEEN: "between",
        DATE_IS: "dateIs",
        DATE_IS_NOT: "dateIsNot",
        DATE_BEFORE: "dateBefore",
        DATE_AFTER: "dateAfter"
     */
    const MATCH_MODE_EQUALS = "equals";
    const MATCH_MODE_IN = "in";
    /**
     * Construct search query for text column
     *
     * @param string $searchField
     * @param string $searchText
     * @param string $matchMode
     * @param Model $model
     * @return Model
     */
    protected function searchText($searchField, $searchText, $matchMode, &$model)
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
        } else if ($matchMode === "lt") {
            $operator = "<";
        } else if ($matchMode === "lte") {
            $operator = "<=";
        } else if ($matchMode === "equals") {
            $operator = "=";
        }
        return $model->where($searchField, $operator, $searchValue);
    }
    
    /**
     * Construct search query for date column
     *
     * @param string $searchField
     * @param string $date
     * @param string $matchMode
     * @param Model $model
     * @return Model
     */
    protected function searchDate($searchField, $date, $matchMode, &$model)
    {
        $operator = "LIKE";
        $searchValue = $date; //contains
        if ($matchMode === "lt") {
            $operator = "<";
        } else if ($matchMode === "lte") {
            $operator = "<=";
        } else if ($matchMode === "equals") {
            $operator = "=";
        } else if ($matchMode === "gt") {
            $operator = ">";
        } else if ($matchMode === "gte") {
            $operator = ">=";
        } else if ($matchMode === "dateIsNot" || $matchMode === "notEquals") {
            $operator = "!=";
        }
        return $model->where($searchField, $operator, $searchValue);
    }

    /**
     * Translate content to cyrilic
     *
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function transleCyrilica(array $data, array $fields): array
    {
        $translations = [];
        foreach ($fields as $fieldName) {
            if (!empty($data[$fieldName])) {
                $translations[$fieldName] = Transliterator::toCyrillic($data[$fieldName]);
            }
        }
        
        return $translations;
    }
}
