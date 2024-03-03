<?php

namespace App\Repositories\Plan;

use App\Contracts\SubscriptionRepositoryInterface;
use App\Models\Plan\Subscription;
use App\Models\Plan\SubscriptionPlan;
use App\Repositories\ModelRepository;
use Carbon\Carbon;

class SubscriptionRepository extends ModelRepository implements SubscriptionRepositoryInterface
{
    protected $model;

    public function __construct(Subscription $model)
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
                $q->orWhereHas('updatedBy', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhereHas('user', function ($query) use ($searchValue, $matchMode) {
                    $this->searchText('name', $searchValue, $matchMode, $query);
                });
                $q->orWhere("start_date", "LIKE", $searchValue);
                $q->orWhere("end_date", "LIKE", $searchValue);
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
            if (!empty($params['start_date'])) {
                $fieldName = 'start_date';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchDate($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['end_date'])) {
                $fieldName = 'end_date';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $this->searchDate($fieldName, $searchText, $matchMode, $model);
            }
            if (!empty($params['active'])) {
                $fieldName = 'active';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $model = $model->where('end_date', '>', date("Y-m-d"));
                $model = $model->where('status', '!=', Subscription::STATUS_EXPIRED);
                $model = $model->where('status', '!=', Subscription::STATUS_CANCELED);
            }
            if (!empty($params['status'])) {
                $fieldName = 'status';
                $searchText = $params[$fieldName];
                $model->where('status', $searchText);
            }
            if (!empty($params['user_id'])) {
                $fieldName = 'user_id';
                $searchText = $params[$fieldName];
                $model->where('user_id', $searchText);
            }
            if (!empty($params['app'])) {
                $fieldName = 'app';
                $searchText = $params[$fieldName];
                $model->where('app', $searchText);
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
                $model = $model->join('users', "subscriptions.user_id", "=", "users.id")->select('subscriptions.*');
                $sortField = "users.name";
            }
            $model = $model->orderBy($sortField, $direction);
        }

        $model = $model->orderBy("subscriptions.id", "desc");

        return $model;
    }

    /**
     * Prepare data for updating a new model
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        $days = null;
        $dateNow = Carbon::now();
        $plan = SubscriptionPlan::find($data['subscription_plan_id']);
        $data['interval'] = $plan->interval;
        if (empty($data['start_date'])) {
            $data['start_date'] = date('Y-m-d H:i:s');
        } else {
            $data['start_date'] = Carbon::parse($data['start_date'])->startOfDay()->format("Y-m-d H:i:s");
        }
        if (empty($data['end_date'])) {
            if ($plan->interval == SubscriptionPlan::INTERVAL_PER_MONTH) {
                $endDate = Carbon::parse($data['start_date'])->addMonth()->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_QUARTER) {
                $endDate = Carbon::parse($data['start_date'])->addMonths(4)->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_6_MONTHS) {
                $endDate = Carbon::parse($data['start_date'])->addMonths(6)->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_YEAR) {
                $endDate = Carbon::parse($data['start_date'])->addYear()->endOfDay()->format("Y-m-d H:i:s");
            } else {
                $endDate = Carbon::parse($data['start_date'])->addDays(30)->endOfDay()->format("Y-m-d H:i:s");
            }
            $data['end_date'] = $endDate;
        } else {
            $data['end_date'] = Carbon::parse($data['end_date'])->endOfDay()->format("Y-m-d H:i:s");
        }
        if (empty($data['status'])) {
            $data['status'] = Subscription::STATUS_ACTIVE;
        }

        if (isset($data['active'])) {
            if ($data['active'] == 1) {
                $data['status'] = Subscription::STATUS_ACTIVE;
            } else {
                $data['status'] = Subscription::STATUS_CANCELED;
            }
        }

        //set status
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        if ($startDate->gt($dateNow) && $endDate->gt($startDate)) {
            $data['status'] = Subscription::STATUS_CREATED;
        } else if ($endDate->lt($dateNow)) {
            $data['status'] = Subscription::STATUS_EXPIRED;
        } else {
            $data['status'] = Subscription::STATUS_ACTIVE;
        }

        return $data;
    }

    /**
     * Prepare data for updating a new model
     *
     * @param array $data
     * @return array
     */
    public function prepareDataForModel($model, array $data): array
    {
        $plan = $model->plan;
        $endDateFromPlan = null;
        $userEndDate = null;
        $dateNow = Carbon::now();

        if (empty($data['start_date'])) {
            $data['start_date'] = $model->start_date;
        } else {
            $data['start_date'] = Carbon::parse($data['start_date'])->startOfDay()->format("Y-m-d H:i:s");
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = Carbon::parse($data['end_date'])->endOfDay()->format("Y-m-d H:i:s");
        } 
        //plan has changed
        if (!empty($data['subscription_plan_id'])) {
            $plan = SubscriptionPlan::find($data['subscription_plan_id']);
            $data['interval'] = $plan->interval;
            if ($plan->interval == SubscriptionPlan::INTERVAL_PER_MONTH) {
                $endDateFromPlan = Carbon::parse($data['start_date'])->addMonth()->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_QUARTER) {
                $endDateFromPlan = Carbon::parse($data['start_date'])->addMonths(4)->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_6_MONTHS) {
                $endDateFromPlan = Carbon::parse($data['start_date'])->addMonths(6)->endOfDay()->format("Y-m-d H:i:s");
            } else if ($plan->interval == SubscriptionPlan::INTERVAL_PER_YEAR) {
                $endDateFromPlan = Carbon::parse($data['start_date'])->addYear()->endOfDay()->format("Y-m-d H:i:s");
            } else {
                $endDateFromPlan = Carbon::parse($data['start_date'])->addDays(30)->endOfDay()->format("Y-m-d H:i:s");
            }
        }
        //subscription end date has changed
        if (!empty($data['end_date']) && $data['end_date'] != $model->end_date) {
            $userEndDate = $userEndDate = Carbon::parse($data['end_date'])->endOfDay()->format("Y-m-d H:i:s");
        } 
        
        $data['end_date'] = $userEndDate ?? $data['end_date'] ?? $endDateFromPlan;
        
        //set status
        $endDateHasExpired = false;
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        if ($startDate->gt($dateNow) && $endDate->gt($startDate)) {
            $data['status'] = Subscription::STATUS_CREATED;
        } else if ($endDate->lt($dateNow)) {
            $endDateHasExpired = true;
            $data['status'] = Subscription::STATUS_EXPIRED;
        } else {
            $data['status'] = Subscription::STATUS_ACTIVE;
        }
        
        if (isset($data['active']) && !$endDateHasExpired) {
            if ($data['active'] == 1) {
                $data['status'] = Subscription::STATUS_ACTIVE;
            } else {
                $data['status'] = Subscription::STATUS_CANCELED;
            }
        }

        return $data;
    }
}
