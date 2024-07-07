<?php

namespace App\Models\Translations;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ad_category_id',
        'language_id',
        'lang_code',
        'approved',
        'created_by',
        'updated_by',
    ];

    public $translateFields = [
        'name',
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
