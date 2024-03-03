<?php

namespace App\Http\Resources\Plan;

use App\Http\Resources\Admin\AdminUserResource;
use App\Models\Plan\FreeTrial;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FreeTrialResource extends JsonResource
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
            'type' => 'free trial',
            'user_id' => $this->user_id,
            'user' => $this->when($this->relationLoaded('user'), function() {
                return [
                    'id' => $this->user_id,
                    'name' => $this->user->name
                ];
            }),
            'plan' => FreeTrialPlanResource::make($this->whenLoaded('plan')),
            'free_trial_plan_id' => $this->free_trial_plan_id,
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
            'human_diff' => Carbon::parse($this->end_date)->diffForHumans(Carbon::now()),
            'remaining_days' => $remainingDays < 0 ? 0 : $remainingDays,
            'created_by_user' => AdminUserResource::make($this->whenLoaded('createdByUser')),
            'updated_by_user' => AdminUserResource::make($this->whenLoaded('updatedByUser')),
        ];
    }
}
