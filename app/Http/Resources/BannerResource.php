<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
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
			'slug' => $this->slug,
			'content' => $this->content,
			'user' => UserResource::make($this->whenLoaded('user')),
			'position' => $this->position,
            'button_title' => $this->button_title,
            'question_label' => $this->question_label,
            'message' => $this->message,
		];
    }
}
