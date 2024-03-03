<?php

namespace App\Http\Resources\Regulation;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RegulationGuestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return RegulationResourceListItem::make($this)->toArray($request);
    }
}
