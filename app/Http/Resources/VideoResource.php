<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
			'video_link' => $this->video_link,
            'title' => $this->title,
            'publish_date' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
            'description' => $this->description,
            'files' => $this->videoFiles
		];
    }
}
