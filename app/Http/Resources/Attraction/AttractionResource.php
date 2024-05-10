<?php

namespace App\Http\Resources\Attraction;

use App\Http\Resources\FileResource;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttractionResource extends JsonResource
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
			'user' => UserResource::make($this->whenLoaded('user')),
			'category' => AttractionCategoryResource::make($this->whenLoaded('category')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
			'category_id' => $this->category_id,
			'user_id' => $this->user_id,
			'title' => $this->title,
			'subtitle' => $this->subtitle,
			'summary' => $this->summary,
			'slug' => $this->slug,
			'place_id' => $this->place_id,
			'place' => PlaceResource::make($this->whenLoaded('place')),
			'latitude' => $this->latitude,
			'longitude' => $this->longitude,
			'map_link' => $this->map_link,
			'content' => $this->content,
			'suggested' => $this->suggested == 1 ? true : false,
			'order_num' => $this->order_num,
			'note' => $this->note,
			'images' => FileResource::collection($this->whenLoaded('images')),
			'default_image' => FileResource::make($this->whenLoaded('defaultImage')),
			'thumbnail_image' => [
				'file_url' => $this->getDefaultThumbnailImage()
			],
			/* 'download_file' => FileResource::make($this->whenLoaded('downloadFile')),
			'pdf_file' => FileResource::make($this->whenLoaded('pdfFile')),
			'html_file' => FileResource::make($this->whenLoaded('htmlFile')),
			'html_file_content' => $this->whenLoaded('htmlFile') ? file_get_contents(storage_path().'/app/'.$this->htmlFile->file_path) : null, */
			'visible' => $this->visible == 1 ? true : false,
			'approved' => $this->approved == 1 ? true : false,
		];
    }

	public function getDefaultThumbnailImage()
	{
		return '/storage/defaults/'.rand(1, 32).'.jpg';
	}

}
