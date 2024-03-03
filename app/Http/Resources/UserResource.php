<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'access' => $this->getAccessLevel($this->role),
            'has_active_plan' => $this->hasActivePlan(request()->route()->parameter('app')),
            'status' => $this->status,
            'active' => $this->active,
            'company_id' => null,
            'created_at' => $this->created_at,
			'created_at_formated' => $this->created_at ? $createdAtCarbon->format("d.m.Y. H:i:s") : null,
			'created_at_date_formated' => $this->created_at ? $createdAtCarbon->format("d.m.Y.") : null,
			'updated_at' => $this->updated_at,
			'updated_at_formated' => $this->updated_at ? $updatedAtCarbon->format("d.m.Y. H:i:s") : null,
			'updated_at_date_formated' => $this->updated_at ? $updatedAtCarbon->format("d.m.Y.") : null,
            'company_name' => $this->company_name,
            'phone_number' => $this->phone_number,
            'position' => $this->position
        ];
    }
}
