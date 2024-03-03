<?php

namespace App\Models;

use App\Http\Resources\Document\DocumentTypeResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class DocumentType extends Model
{
    use HasFactory, HasRecursiveRelationships;

    protected $fillable = [
        'name',
        'user_id',
        'app',
        'parent_id'
    ];

    /**
     * Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
