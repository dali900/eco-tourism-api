<?php

namespace App\Http\Resources\Question;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionTypeResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'parent_name' => $this->when($this->relationLoaded('parent'), $this->parent?->name),
            'name_parent_name' => $this->when($this->relationLoaded('parent'), function(){
                if ($this->parent){
                    return $this->name.' ('.$this->parent?->name.')';
                }
                return $this->name;
            }),
            'children_data' => QuestionTypeResource::collection($this->whenLoaded('children')),
			'user' => UserResource::make($this->whenLoaded('user')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
		];
    }
}
