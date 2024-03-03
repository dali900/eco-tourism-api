<?php

namespace App\Http\Resources\Plan;

use App\Http\Resources\Admin\AdminUserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $remainingDays = Carbon::now()->diffInDays(Carbon::parse($this->end_date), false);

        return [
            'id' => $this->id,
            'app' => $this->app,
            'type' => 'subscription',
            'user_id' => $this->user_id,
            'plan' => SubscriptionPlanResource::make($this->whenLoaded('plan')),
            'user' => $this->when($this->relationLoaded('user'), function() {
                return [
                    'id' => $this->user_id,
                    'name' => $this->user->name
                ];
            }),
            'subscription_plan_id' => $this->subscription_plan_id,
            'human_diff' => Carbon::parse($this->end_date)->diffForHumans(Carbon::now()),
            'remaining_days' => $remainingDays < 0 ? 0 : $remainingDays,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'end_date_formated' => Carbon::parse($this->end_date)->format("d.m.Y."),
            'end_datetime_formated' => Carbon::parse($this->end_date)->format("d.m.Y. H:i:s"),
            'start_date_formated' => Carbon::parse($this->start_date)->format("d.m.Y."),
            'start_datetime_formated' => Carbon::parse($this->start_date)->format("d.m.Y. H:i:s"),
            'created_at_formated' => Carbon::parse($this->created_at)->format("d.m.Y. H:i:s"),
            'created_at_date_formated' => Carbon::parse($this->created_at)->format("d.m.Y."),
            'active' => $this->isActive(),
            'status' => $this->status,
            'note' => $this->note,
            'created_by_user' => AdminUserResource::make($this->whenLoaded('createdByUser')),
            'updated_by_user' => AdminUserResource::make($this->whenLoaded('updatedByUser')),
        ];
    }
}
