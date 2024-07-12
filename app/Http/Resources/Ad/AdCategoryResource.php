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
			'name' => $this->name,
		];
		if ($this->relationLoaded('translation') && $this->translation) {
			$translation = AdTranslationResource::make($this->translation);
		}
        return [
			'id' => $this->id,
            'key' => $this->id,
            'name' => $this->name,
            't_name' => $translation['name'],
            'order_num' => $this->order_num,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->when($this->relationLoaded('parent'), function () {
                if($this->parent){
                    return $this->parent->name;
                }
            }),
            'name_parent_name' => $this->when($this->relationLoaded('parent'), function () {
                if ($this->parent) {
                    return $this->name . ' (' . $this->parent?->name . ')';
                }
                return $this->name;
            }),
            'user_id' => $this->user_id,
            'user' => $this->when($this->relationLoaded('user') && $this->user, function () {
                return UserResource::make($this->user);
            }),
            'parent' => $this->when($this->relationLoaded('parent'), function () {
                if($this->parent){
                    return [
                        'id' => $this->parent_id,
                        'name' => $this->parent->name,
                    ];
                }
            }),
            'ancestorsAndSelf' => $this->when($this->relationLoaded('ancestorsAndSelf'), function () {
                return AdCategoryResource::collection($this->ancestorsAndSelf);
            }),
            'children' => $this->when($this->relationLoaded('allChildren'), function () {
                return AdCategoryResource::collection($this->allChildren);
            }),
            'ads' => $this->when($this->relationLoaded('ads'), function () {
                return AdResource::collection($this->ads);
            }),
            't' => $translation,
            'translations' => AdCategoryTranslationResource::collection($this->whenLoaded('translations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("F d, Y") : null,
            'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("F d, Y") : null,
        ];
    }
}
