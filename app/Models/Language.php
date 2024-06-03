<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

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
}
