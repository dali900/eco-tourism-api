<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    const SR_CODE = 'sr-latin';
    const SR_CYRL_CODE = 'sr-cyrillic';

    protected $fillable = [
        'name',
        'translated_name',
        'lang_code',
        'note',
        'translated_note',
        'visible',
        'created_by',
        'updated_by',
    ];

    /**
     * Find language by code
     *
     * @param string $langCode
     * @return Model|null
     */
    public static function findByCode(string $langCode)
    {
        return self::where('lang_code', $langCode)->first();
    }
}
