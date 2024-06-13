<?php

namespace App\Models\Translations;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'attraction_id',
        'language_id',
        'lang_code',
        'title',
        'subtitle',
        'summary',
        'content',
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
