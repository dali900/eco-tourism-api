<?php

namespace App\Http\Resources\Question;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;

class QuestionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'title' => $this->title,
			'question' => $this->question,
			'answer' => $this->answer,
            'author' => $this->author,
			'user' => UserResource::make($this->whenLoaded('user')),
			'publish_date' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
            'file_path' => $this->file_path,
            'file_url' => Storage::url($this->file_path),
            'question_type' => $this->questionType,
			'question_type_id' => $this->question_type_id,
            'approved' => $this->approved
		];
    }
}
