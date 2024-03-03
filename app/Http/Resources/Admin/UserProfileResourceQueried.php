<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Plan\FreeTrialResource;
use App\Http\Resources\Plan\SubscriptionResource;
use App\Models\Plan\FreeTrial;
use App\Models\Plan\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResourceQueried extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userCreatedAtCarbon = Carbon::parse($this->user_created_at);
        return [
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'role' => $this->role,
            'user_status' => $this->user_status,
            'user_active' => $this->user_active,
            'user_created_at' => $this->user_created_at,
			'user_created_at_formated' => $this->user_created_at ? $userCreatedAtCarbon->format("d.m.Y. H:i:s") : null,
			'created_at_date_formated' => $this->user_created_at ? $userCreatedAtCarbon->format("d.m.Y.") : null,
            //last subscription
            'subscription_plan_name' => $this->subscription_plan_name,
            'subscription_start_date' => $this->subscription_start_date,
            'subscription_start_date_formated' => $this->subscription_start_date ? 
                Carbon::parse($this->subscription_start_date)->format("d.m.Y.") : null,
            'subscription_end_date' => $this->subscription_end_date,
            'subscription_end_date_formated' => $this->subscription_end_date ? 
                Carbon::parse($this->subscription_end_date)->format("d.m.Y.") : null,
            'subscription_status' => $this->subscription_status,
            'free_trial_plan_name' => $this->free_trial_plan_name,
            'free_trial_start_date' => $this->free_trial_start_date,
            'free_trial_start_date_formated' => $this->free_trial_start_date ? 
                Carbon::parse($this->subscription_start_date)->format("d.m.Y.") : null,
            'free_trial_end_date' => $this->free_trial_end_date,
            'free_trial_end_date_formated' => $this->free_trial_end_date ? 
                Carbon::parse($this->free_trial_end_date)->format("d.m.Y.") : null,
            'free_trial_status' => $this->free_trial_status,
            'active_subscription' => $this->hasActiveSubscription(),
            'active_free_trial' => $this->hasActiveFreeTrial()
        ];
    }

    private function hasActiveSubscription()
    {
        $activeSubscription = false;
        if (!empty($this->subscription_start_date)) {
            $activeSubscription = Subscription::isActiveStaticCheck(
                $this->subscription_start_date, 
                $this->subscription_end_date, 
                $this->subscription_status
            );
        } 
        return $activeSubscription ? 1 : 0;
    }

    private function hasActiveFreeTrial()
    {
        $activeFreeTrial = false;
        if (!empty($this->free_trial_start_date)) {
            $activeFreeTrial = FreeTrial::isActiveStaticCheck(
                $this->free_trial_start_date, 
                $this->free_trial_end_date, 
                $this->free_trial_status
            );
        }
        return $activeFreeTrial ? 1 : 0;
    }
}
