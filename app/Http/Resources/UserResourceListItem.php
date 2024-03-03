<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceListItem extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'active' => $this->active,
            'created_at' => $this->created_at,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y. H:i:s") : null,
			'updated_at' => $this->updated_at,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y. H:i:s") : null,
        ];
    }
}
