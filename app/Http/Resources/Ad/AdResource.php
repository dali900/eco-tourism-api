<?php

namespace App\Http\Resources\Ad;

use App\Http\Resources\FileResource;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = [
            'name' => $this->name,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'summary' => $this->summary,
            'content' => $this->content,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
        if ($this->relationLoaded('translation') && $this->translation) {
            $translation = AdTranslationResource::make($this->translation);
        }
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'category' => AdCategoryResource::make($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'phone_number_formated' => $this->phone_number ? preg_replace('/^(\d{3})(\d{2})(\d{2})(\d{3,})$/', '$1 $2 $3 $4', $this->phone_number) : null,
            'email' => $this->email,
            'description' => $this->description,
            'summary' => $this->summary,
            'slug' => $this->slug,
            'place_id' => $this->place_id,
            'place' => PlaceResource::make($this->whenLoaded('place')),
            'price' => intval($this->price),
            'price_formated' => formatMoney($this->price),
            'currency' => $this->currency,
            'map_link' => $this->map_link,
            'content' => $this->content,
            'order_num' => $this->order_num,
            'note' => $this->note,
            'images' => FileResource::collection($this->whenLoaded('images')),
            'default_image' => FileResource::make($this->whenLoaded('defaultImage')),
            'thumbnail' => FileResource::make($this->whenLoaded('thumbnail')),
            'suggested' => $this->suggested == 1 ? true : false,
            'approved' => $this->approved == 1 ? true : false,
            't' => $translation,
            'translations' => AdTranslationResource::collection($this->whenLoaded('translations')),
            'published_at' => $this->published_at,
            'expires_at' => $this->expires_at,
            'published_at_formated' => $this->published_at ? Carbon::parse($this->published_at)->format("d.m.Y.") : null,
            'expires_at_formated' => $this->expires_at ? Carbon::parse($this->expires_at)->format("d.m.Y.") : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
            'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
        ];
    }
}