<?php
namespace App\Repositories;

use App\Contracts\VideoRepositoryInterface;
use App\Models\Video;

class VideoRepository extends ModelRepository implements VideoRepositoryInterface 
{
    protected $model;
    
    public function __construct(Video $model) {
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

        //Global search
        if (!empty($params['global'])) {
            $matchMode = $params['global_MatchMode'] ?? null;
            $searchValue = "%" . $params['global'] . "%";
            if ($matchMode === "startsWith") {
                $searchValue = $params['global'] . "%";
            }
            $model = $model->where(function ($q) use ($searchValue, $matchMode) {
                $q->orWhere("video_link", "LIKE", $searchValue);
                $q->orWhere("title", "LIKE", $searchValue);
            });
        }
        //Column search
        else {
            //Search text column
            if (!empty($params['video_link'])) {
                $fieldName = 'video_link';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['title'])) {
                $fieldName = 'title';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchText($fieldName, $searchText, $matchMode, $model);
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

        $model->orderBy("videos.publish_date", "desc");

        return $model;
	}
}
