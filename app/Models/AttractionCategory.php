<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class AttractionCategory extends Model
{
    use HasFactory, HasRecursiveRelationships;

    protected $fillable = [
        'name',
        'subtitle',
        'parent_id',
        'slug',
        'summary',
        'text',
        'publish_date',
        'approved',
        'created_by',
        'updated_by',
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
