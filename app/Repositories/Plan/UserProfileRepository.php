<?php

namespace App\Repositories\Plan;

use App\Contracts\UserProfileRepositoryInterface;
use App\Models\Plan\Subscription;
use App\Models\Plan\SubscriptionPlan;
use App\Repositories\ModelRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserProfileRepository extends ModelRepository implements UserProfileRepositoryInterface
{
    /**
     * Construct user profile query with subscription and free trial data
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery()
    {
        $query = DB::table('users')
            ->select([
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'users.email_verified_at',
                'users.role',
                'users.last_login',
                'users.ip',
                'users.status as user_status',
                'users.active as user_active',
                'users.created_at as user_created_at',
                'subscription_plans.name as subscription_plan_name',
                'subscriptions.start_date as subscription_start_date',
                'subscriptions.end_date as subscription_end_date',
                'subscriptions.status as subscription_status',
                'free_trial_plans.name as free_trial_plan_name',
                'free_trials.start_date as free_trial_start_date',
                'free_trials.end_date as free_trial_end_date',
                'free_trials.status as free_trial_status',

            ])
            //->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            //join last subscription
            ->leftJoin('subscriptions', function($query) {
                $query->on('subscriptions.user_id','=','users.id')
                    ->whereRaw('subscriptions.id IN (select MAX(s2.id) from subscriptions as s2 join users as u2 on u2.id = s2.user_id group by u2.id)');
            })
            ->leftJoin('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            //->leftJoin('free_trials', 'users.id', '=', 'free_trials.user_id')
            ->leftJoin('free_trials', function($query) {
                $query->on('free_trials.user_id','=','users.id')
                    ->whereRaw('free_trials.id IN (select MAX(ft2.id) from free_trials as ft2 join users as u2 on u2.id = ft2.user_id group by u2.id)');
            })
            ->leftJoin('free_trial_plans', 'free_trials.free_trial_plan_id', '=', 'free_trial_plans.id');
        
        return $query;
    }

    /**
     * Get user profile by id
     *
     * @param integer $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByUserId($userId)
    {
        $query = $this->getQuery();
        $query->where('users.id', $userId);
        return $query->first();
    }

    /**
     * Get user profile by app
     *
     * @param string $mail
     * @param string $app
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByMailAndApp($mail, $app)
    {
        $query = $this->getQuery();
        $query->where('users.email', $mail);
        $query->where('users.app', 'LIKE', '%' . $app . '%');
        return $query->first();
    }

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @param \Illuminate\Database\Query\Builder $customModel
     * @return void
     */
    public function getAllFiltered($params = [], $customModel = null)
    {
        $query = $customModel ? $customModel : $this->getQuery();
        $query = $query->where(function ($query) use ($params){
            $query->where('users.app', 'LIKE', '%' . $params['app'] . '%');
            $query->orWhere('subscriptions.app', $params['app']);
            $query->orWhere('free_trials.app', $params['app']);
        });
        $direction = 'desc';
        $sortField = 'users.id';
        //Global search
        if (!empty($params['global'])) {
            $matchMode = $params['global_MatchMode'] ?? "LIKE";
            $searchValue = "%" . $params['global'] . "%";
            if ($matchMode === "startsWith") {
                $searchValue = $params['global'] . "%";
            }
            else if ($matchMode === "contains") {
                $matchMode = "LIKE";
                $searchValue = "%" . $params['global'] . "%";
            }
            $query = $query->where(function ($q) use ($searchValue, $matchMode) {
                $q->orWhere("users.name", $matchMode, $searchValue);
                $q->orWhere("users.email", $matchMode, $searchValue);
                $q->orWhere("users.role", $matchMode, $searchValue);
                $q->orWhere("subscriptions.status", $matchMode, $searchValue);
                $q->orWhere("subscription_plans.name", $matchMode, $searchValue);
            });
        }
        //Column search
        else {
            //Search relation type belongsTo
            if (!empty($params['user_name'])) {
                $query = $query->whereHas('user', function ($query) use ($params) {
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
                $query = $this->searchDate($fieldName, $searchText, $matchMode, $query);
            }
            if (!empty($params['end_date'])) {
                $fieldName = 'end_date';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $query = $this->searchDate($fieldName, $searchText, $matchMode, $query);
            }
            if (!empty($params['active'])) {
                $fieldName = 'active';
                $searchText = $params[$fieldName];
                $matchMode = $params[$fieldName . "_MatchMode"] ?? null;
                $query = $query->where('end_date', '>', date("Y-m-d"));
                $query = $query->where('status', '!=', Subscription::STATUS_EXPIRED);
                $query = $query->where('status', '!=', Subscription::STATUS_CANCELED);
            }
            if (!empty($params['status'])) {
                $fieldName = 'status';
                $searchText = $params[$fieldName];
                $query->where('status', $searchText);
            }
        }

        //Sorting
        if (isset($params['sortField'])) {
            $direction = 'asc';
            if (isset($params['sortOrder'])) {
                if ($params['sortOrder'] == -1) $direction = 'desc';
            }
            $sortField = $params['sortField'];
            if ($params['sortField'] == 'id') $sortField = 'users.id';
            if ($params['sortField'] == 'created_at_formated') $sortField = 'users.id';
            if ($params['sortField'] == 'updated_at_formated') $sortField = 'users.updated_at';

            $query = $query->orderByRaw("$sortField IS NULL, $sortField $direction");
        }

        return $query;
    }
}
