<?php

namespace App\Http\Resources\Document;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentGuestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return DocumentResourceListItem::make($this)->toArray($request);
    }
}
