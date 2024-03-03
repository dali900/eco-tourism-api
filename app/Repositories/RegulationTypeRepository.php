<?php
namespace App\Repositories;

use App\Models\RegulationType;
use App\Contracts\RegulationTypeRepositoryInterface;

class RegulationTypeRepository extends ModelRepository implements RegulationTypeRepositoryInterface
{
    protected $model;
    
    public function __construct(RegulationType $model) {
        $this->model = $model;
    }
    
    /**
     * Create new model
     *
     * @param array $data
     * @return Model
     */
	public function create($data = [])
	{
		$this->model->fill($data);
		$this->model->save();
		return $this->model;
	}

	/**
	 * Update model
	 *
	 * @param array $data
	 * @return Menu|null
	 */
	public function update($model, array $data = []){
		if($model->update($data)){
			return $model;
		}
		return null;
	}

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @return void
     */
    public function getAllFiltered($params = [], $app)
    {
        $tableName = "regulation_types";
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
                //Search relation type belongsTo
                $q->orWhereHas('user', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("name", "LIKE", $searchValue);
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

            if ($params['sortField'] == 'user_name') {
                //Search relation type belongsTo
                $model = $model->join('users', "$tableName.user_id", "=", "users.id")->select("$tableName.*");
                $sortField = "users.name";
            }
            $model = $model->orderBy($sortField, $direction);
        }

        $model->orderBy("$tableName.name", "asc");

        return $model;
    }
    
}
