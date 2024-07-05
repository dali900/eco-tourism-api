<?php

namespace App\Models;

use App\Models\Translations\NewsCategoryTranslation;
use App\Traits\Translates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class NewsCategory extends Model
{
    use HasFactory, HasRecursiveRelationships, Translates;

    protected $fillable = [
        'parent_id',
        'name',
        'order_num',
        'created_by',
        'updated_by',
        'visible',
    ];

    /**
     * News
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    /**
     * Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(NewsCategoryTranslation::class);
    }

    /**
     * Translation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasOne(NewsCategoryTranslation::class);
    }
    
    /**
     * Translation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function t()
    {
        return $this->hasOne(NewsCategoryTranslation::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
