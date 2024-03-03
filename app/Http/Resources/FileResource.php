<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
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
            'file_path' => $this->file_path,
            'file_tag' => $this->file_tag,
            'original_name' => $this->original_name,
            'file_url' => $this->is_public ? Storage::url($this->file_path) : null,
            'is_tmp' => $this->is_tmp == 0 ? false : true,
            'is_public' => empty($this->is_public) ? null : ($this->is_public == 1 ? true : false),
        ];
    }
}
