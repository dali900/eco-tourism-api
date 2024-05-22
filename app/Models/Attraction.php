<?php

namespace App\Models;

use App\Models\Scopes\AttractionScope;
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
        'created_by',
        'updated_by',
        'place_id',
        'latitude',
        'longitude',
        'map_link',
        'approved',
        'visible',
        'suggested',
        'order_num',
        'note',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new AttractionScope);
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

    /**
     * thumbnail
     *
     * 
     */
    public function thumbnail()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_THUMBNAIL);
    }

    /**
     * Filter only suggested
     *
     * @param $query
     * @return void
     */
    public function scopeSuggested($query)
    {
        $query->where('suggested', '!=', 0);
    }
    
    public function scopeNotSuggested($query)
    {
        $query->where('suggested', '=', 0);
    }
}
