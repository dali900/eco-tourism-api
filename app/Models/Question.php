<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'title',
        'answer',
        'user_id',
        'author',
        'publish_date',
        'file_path',
        'question_type_id',
        'approved',
        'app'
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
     * questionType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }
}
