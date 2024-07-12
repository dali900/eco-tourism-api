<?php

namespace App\Http\Resources\Ad;

use Illuminate\Http\Resources\Json\JsonResource;

class AdCategoryDropdownResourcePaginated extends JsonResource
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
            'results' => AdCategoryDropdownResource::collection($this->getCollection()),
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}
