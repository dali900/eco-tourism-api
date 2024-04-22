<?php

namespace App\Models;

use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Place extends Model
{
    use HasFactory, HasRecursiveRelationships, HandleFiles;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'user_id',
        'latitude',
        'longitude',
        'map_link',
        'visible',
        'order_num',
    ];

    /**
     * Attraction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attractions()
    {
        return $this->hasMany(Attraction::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Image Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_FILE);
    }

    /**
     * Default Image Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function defaultImage()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_FILE);
    }
}
