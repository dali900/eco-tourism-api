<?php

namespace App\Http\Resources;

use App\Contracts\VideoRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResourcePaginated extends JsonResource implements VideoRepositoryInterface
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
            'results' => VideoResource::collection($this->getCollection()),
            'pagination' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}
