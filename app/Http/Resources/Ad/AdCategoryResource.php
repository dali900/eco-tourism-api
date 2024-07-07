<?php

namespace App\Http\Resources\Ad;

use App\Http\Resources\FileResource;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = [
			'title' => $this->title,
			'description' => $this->description,
		];
		if ($this->relationLoaded('translation') && $this->translation) {
			$translation = AdTranslationResource::make($this->translation);
		}
        return [
			'id' => $this->id,
			'name' => $this->name,
			'user' => UserResource::make($this->whenLoaded('user')),
			'category' => AdCategoryResource::make($this->whenLoaded('category')),
			'category_id' => $this->category_id,
			'user_id' => $this->user_id,
			'title' => $this->title,
			'slug' => $this->slug,
			'description' => $this->description,
			'place_id' => $this->place_id,
			'place' => PlaceResource::make($this->whenLoaded('place')),
			'currency' => $this->currency,
			'price' => formatMoney($this->price),
			'suggested' => $this->suggested == 1 ? true : false,
			'order_num' => $this->order_num,
			'note' => $this->note,
			'images' => FileResource::collection($this->whenLoaded('images')),
			'default_image' => FileResource::make($this->whenLoaded('defaultImage')),
			'thumbnail' => FileResource::make($this->whenLoaded('thumbnail')),
			'approved' => $this->approved == 1 ? true : false,
			'translations' => AdTranslationResource::collection($this->whenLoaded('translations')),
			't' => $translation,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'expires_at' => $this->expires_at,
			'expires_at_formated' => $this->updated_at ? Carbon::parse($this->expires_at)->format("d.m.Y.") : null,
			'published_at_formated' => $this->updated_at ? Carbon::parse($this->published_at)->format("d.m.Y.") : null,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
        ];
    }
}
