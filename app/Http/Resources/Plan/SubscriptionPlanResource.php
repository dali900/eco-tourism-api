<?php

namespace App\Http\Resources\Plan;

use App\Http\Resources\Admin\AdminUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'key' => $this->key,
            'interval' => $this->interval,
            'amount' => $this->amount,
            'created_by_user' => AdminUserResource::make($this->whenLoaded('createdByUser')),
            'updated_by_user' => AdminUserResource::make($this->whenLoaded('updatedByUser')),
        ];
    }
}
