<?php

namespace App\Http\Resources\News;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsTranslationResource extends JsonResource
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
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'attraction_id' => $this->attraction_id,
			'language_id' => $this->language_id,
			'lang_code' => $this->lang_code,
			'title' => $this->title,
			'subtitle' => $this->subtitle,
			'summary' => $this->summary,
			'slug' => $this->slug,
			'text' => $this->text,
			'approved' => $this->approved == 1 ? true : false,
			'created_by_user' => UserResource::make($this->whenLoaded('createdByUser')),
			'updated_by_user' => UserResource::make($this->whenLoaded('updatedByUser')),
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
		];
    }

}
