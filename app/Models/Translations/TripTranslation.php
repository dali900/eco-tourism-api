<?php

namespace App\Models\Translations;

use App\Models\Attraction;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'language_id',
        'title',
        'subtitle',
        'text',
        'summary',
        'approved',
        'created_by',
        'updated_by',
    ];

    public $translateFields = [
        'name',
        'title',
        'subtitle',
        'summary',
        'content',
    ];

    /**
     * Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
