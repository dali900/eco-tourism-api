<?php

namespace App\Http\Resources\Article;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\FileResource;

class ArticleResourceListItem extends JsonResource
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
			'user' => UserResource::make($this->whenLoaded('user')),
			'publish_date' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
            'pdf_file' => FileResource::make($this->whenLoaded('pdfFile')),
			'html_files' => FileResource::collection($this->whenLoaded('htmlFiles')),
			'html_file' => FileResource::make($this->whenLoaded('htmlFile')),
            'download_file' => FileResource::make($this->whenLoaded('downloadFile')),
            'article_type_id' => $this->article_type_id,
            'article_type' => $this->articleType,
            'approved' => $this->approved
		];
    }
}
