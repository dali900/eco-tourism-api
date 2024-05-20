<?php

namespace App\Http\Resources;

use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
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
			'user_id' => $this->user_id,
			'description' => $this->description,
			'latitude' => $this->latitude,
			'longitude' => $this->longitude,
			'map_link' => $this->map_link,
			'order_num' => $this->order_num,
			'visible' => $this->visible,
			'user' => UserResource::make($this->whenLoaded('user')),
			'parent_id' => $this->parent_id,
			'parent' => PlaceResource::make($this->whenLoaded('parent')),
			'images' => FileResource::collection($this->whenLoaded('images')),
			'thumbnail' => FileResource::make($this->whenLoaded('thumbnail')),
			'attractions' => AttractionResource::collection($this->whenLoaded('attractions')),
			'default_image' => FileResource::make($this->whenLoaded('defaultImage')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
		];
    }

}
