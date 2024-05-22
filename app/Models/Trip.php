<?php

namespace App\Models;

use App\Models\Scopes\TripScope;
use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory, HandleFiles;

    protected $fillable = [
        'title',
        'subtitle',
        'created_by',
        'updated_by',
        'text',
        'summary',
        'publish_date',
        'file_path',
        'approved',
        'slug'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TripScope);
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
     * Image file
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_FILE);
    }

    /**
     * Default Image Files
     */
    public function defaultImage()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_FILE);
    }

    /**
     * thumbnail
     */
    public function thumbnail()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_IMAGE_THUMBNAIL);
    }

    /**
     * Attractions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attractions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Attraction::class, 'trip_attraction', 'trip_id', 'attraction_id');
    }
}
