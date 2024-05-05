<?php

namespace App\Http\Resources\News;

use App\Http\Resources\FileResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsResource extends JsonResource
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
			'title' => $this->title,
            'url_title' => Str::slug($this->title, "-"),
			'subtitle' => $this->subtitle,
			'user' => UserResource::make($this->whenLoaded('user')),
			'text' => $this->text,
			'summary' => $this->summary,
			'publish_date_formated' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
            'publish_date_m_format' => $this->publish_date ? Carbon::parse($this->publish_date)->translatedFormat("d M y") : null,
            'publish_date_day' => $this->publish_date ? Carbon::parse($this->publish_date)->translatedFormat("d") : null,
            'publish_date_month' => $this->publish_date ? Carbon::parse($this->publish_date)->translatedFormat("M") : null,
            'publish_date_year' => $this->publish_date ? Carbon::parse($this->publish_date)->translatedFormat("y") : null,
            'images' => FileResource::collection($this->whenLoaded('images')),
            'default_image' => FileResource::make($this->whenLoaded('defaultImage')),
		];
    }
}
