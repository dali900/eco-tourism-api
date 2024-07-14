<?php

namespace App\Http\Resources\Ad;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdTranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
			'title' => $this->title,
			'created_at' => $this->created_at,
			'ad_id' => $this->ad_id,
			'language_id' => $this->language_id,
			'lang_code' => $this->lang_code,
			'description' => $this->description,
			'approved' => $this->approved == 1 ? true : false,
			'updated_at' => $this->updated_at,
			'created_by_user' => UserResource::make($this->whenLoaded('createdByUser')),
			'updated_by_user' => UserResource::make($this->whenLoaded('updatedByUser')),
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
		];
    }
}
