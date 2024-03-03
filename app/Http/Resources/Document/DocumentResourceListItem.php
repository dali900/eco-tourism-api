<?php

namespace App\Http\Resources\Document;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DocumentResourceListItem extends JsonResource
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
			'title' => $this->title,
			'author' => $this->author,
            'comment' => $this->comment,
			'publish_date' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
            'document_type' => DocumentTypeResource::make($this->whenLoaded('documentType')),
            'document_type_id' => $this->document_type_id,
            'file_path' => $this->file_path,
            'approved' => $this->approved
		];
    }
}
