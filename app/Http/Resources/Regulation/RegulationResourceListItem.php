<?php

namespace App\Http\Resources\Regulation;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RegulationResourceListItem extends JsonResource
{
    /**
	 * This resource should be used in lists
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
			'id' => $this->id,
            'user_id' => $this->user_id,
			'name' => $this->name,
			'type' => RegulationTypeResource::make($this->whenLoaded('regulationType')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'note' => $this->note,
			'messenger' => $this->messenger,
			'messenger_note' => $this->messenger_note,
            'approved' => $this->approved
		];
    }
}
