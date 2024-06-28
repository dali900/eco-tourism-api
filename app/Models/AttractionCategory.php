<?php

namespace App\Models;

use App\Models\Translations\AttractionCategoryTranslation;
use App\Traits\Translates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class AttractionCategory extends Model
{
    use HasFactory, HasRecursiveRelationships, Translates;

    protected $fillable = [
        'name',
        'approved',
        'created_by',
        'updated_by',
        'parent_id'
    ];

    /**
     * Attraction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attractions()
    {
        return $this->hasMany(Attraction::class, 'category_id');
    }

    /**
     * Returns all descendants (attractions) of this category
     *
     * @return void
     */
    public function recursiveAttractions()
    {
        return $this->hasManyOfDescendantsAndSelf(Attraction::class, 'category_id');
    }

    /**
     * Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(AttractionCategoryTranslation::class);
    }

    /**
     * Translation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasOne(AttractionCategoryTranslation::class);
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
