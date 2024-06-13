<?php

namespace App\Models\Translations;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'language_id',
        'name',
        'description',
        'approved',
        'created_by',
        'updated_by',
    ];

    public $translateFields = [
        'name',
        'description',
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
