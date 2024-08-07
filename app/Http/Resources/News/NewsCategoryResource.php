<?php

namespace App\Http\Resources\News;

use Carbon\Carbon;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\News\NewsCategoryTranslationResource;

class NewsCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $translation = [
			'name' => $this->name,
		];
		if ($this->relationLoaded('translation') && $this->translation) {
			$translation = NewsCategoryTranslationResource::make($this->translation);
		}
        return [
            'id' => $this->id,
            'key' => $this->id,
            'name' => $this->name,
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
            'news' => $this->when($this->relationLoaded('news') && $this->news, function () {
                return NewsResource::collection($this->news);
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
                return NewsCategoryResource::collection($this->ancestorsAndSelf);
            }),
            'children' => $this->when($this->relationLoaded('children'), function () {
                return NewsCategoryResource::collection($this->children);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("F d, Y") : null,
            'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("F d, Y") : null,
            't' => $translation,
            'translations' => NewsCategoryTranslationResource::collection($this->whenLoaded('translations')),
        ];
    }
}
