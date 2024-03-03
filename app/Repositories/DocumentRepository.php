<?php

namespace App\Repositories;

use App\Contracts\DocumentRepositoryInterface;
use App\Models\Document;
use App\Models\User;

class DocumentRepository extends ModelRepository implements DocumentRepositoryInterface
{
    protected $model;

    public function __construct(Document $model)
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
                $q->orWhereHas('documentType', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("title", "LIKE", $searchValue);
                $q->orWhere("author", "LIKE", $searchValue);
                $q->orWhere("text", "LIKE", $searchValue);
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
            //Search text column
            if (!empty($params['author'])) {
                $fieldName = 'author';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            //Search text column
            if (!empty($params['text'])) {
                $fieldName = 'text';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            //Search text column
            if (!empty($params['comment'])) {
                $fieldName = 'comment';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['document_type_id'])) {
                $fieldName = 'document_type_id';
                $value = $params[$fieldName];
                $matchMode = ModelRepository::MATCH_MODE_EQUALS;
                $model = $model->where('document_type_id', $value)
                    ->orWhereHas('documentType',  function ($query) use ($value) {
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

        $model->orderBy("documents.title", "asc");

        return $model;
    }
}
