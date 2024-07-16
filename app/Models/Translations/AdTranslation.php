<?php

namespace App\Models\Translations;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ad_id',
        'language_id',
        'lang_code',
        'description',
        'approved',
        'created_by',
        'updated_by',
    ];

    public $translateFields = [
        'title',
        'description',
        'first_name',
        'last_name',
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
