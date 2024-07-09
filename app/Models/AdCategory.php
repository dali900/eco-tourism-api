<?php

namespace App\Models;

use App\Models\Translations\AdCategoryTranslation;
use App\Traits\Translates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class AdCategory extends Model
{
    use HasFactory, HasRecursiveRelationships, Translates;

    protected $fillable = [
        'name',
        'visible',
        'created_by',
        'updated_by',
        'parent_id'
    ];

    /**
     * Ad
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ads()
    {
        return $this->hasMany(Ad::class, 'category_id');
    }

    /**
     * Returns all descendants (ads) of this category
     *
     * @return void
     */
    public function recursiveAds()
    {
        return $this->hasManyOfDescendantsAndSelf(Ad::class, 'category_id');
    }

    /**
     * Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(AdCategoryTranslation::class);
    }

    /**
     * Translation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasOne(AdCategoryTranslation::class);
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
