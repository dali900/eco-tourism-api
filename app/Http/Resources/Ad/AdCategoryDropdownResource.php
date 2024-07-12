<?php

namespace App\Http\Resources\Ad;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdCategoryDropdownResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $name = $this->name;
		if ($this->relationLoaded('translation') && $this->translation) {
			$name = $this->translation->name;
		}
        return [
			'id' => $this->id,
            'key' => $this->id,
            'name' => $name,
            'order_num' => $this->order_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("F d, Y") : null,
            'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("F d, Y") : null,
        ];
    }
}
