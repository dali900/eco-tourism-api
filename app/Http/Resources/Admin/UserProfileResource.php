<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Plan\FreeTrialResource;
use App\Http\Resources\Plan\SubscriptionResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $createdAtCarbon = Carbon::parse($this->created_at);
        $updatedAtCarbon = Carbon::parse($this->updated_at);
        $lastLoginCarbon = Carbon::parse($this->last_login);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'role_name' => $this->roleName,
            'phone_number' => $this->phone_number,
            'position' => $this->position,
            'access' => $this->getAccessLevel(),
            'status' => $this->status,
            'active' => $this->active,
            'note' => $this->note,
            //'subscriptions' => SubscriptionResource::collection($this->subscriptions),
            'subscriptions_count' => $this->subscriptions_count,
            'subscriptions' => $this->when($this->relationLoaded('subscriptions'), function() {
                return SubscriptionResource::collection($this->subscriptions);
            }),
            'free_trials_count' => $this->free_trials_count,
            'free_trials' => $this->when($this->relationLoaded('freeTrials'), function() {
                return FreeTrialResource::collection($this->freeTrials);
            }),
            'last_subscription' => SubscriptionResource::make($this->lastSubscription),
            'last_free_trial' => FreeTrialResource::make($this->lastFreeTrial),
            'company_name' => $this->company_name,
            'company_id' => null,
            'last_login_formated' => $this->last_login ? $lastLoginCarbon->format("d.m.Y. H:i:s") : null,
            'created_at' => $this->created_at,
			'created_at_formated' => $this->created_at ? $createdAtCarbon->format("d.m.Y. H:i:s") : null,
			'created_at_date_formated' => $this->created_at ? $createdAtCarbon->format("d.m.Y.") : null,
			'updated_at' => $this->updated_at,
			'updated_at_formated' => $this->updated_at ? $updatedAtCarbon->format("d.m.Y. H:i:s") : null,
			'updated_at_date_formated' => $this->updated_at ? $updatedAtCarbon->format("d.m.Y.") : null,
        ];
    }
}
