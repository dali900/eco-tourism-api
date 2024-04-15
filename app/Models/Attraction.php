<?php

namespace App\Models;

use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    use HasFactory, HandleFiles;

    protected $fillable = [
        'name',
        'category_id',
        'title',
        'subtitle',
        'summary',
        'content',
        'user_id',
        'place_id',
        'latitude',
        'longitude',
        'map_link',
        'approved',
        'visible',
        'stand_out',
        'note',
    ];

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
     * Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AttractionCategory::class, 'category_id');
    }
    
    /**
     * Place
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
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
     * 
     */
    public function defaultImage()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_FILE);
    }
}
