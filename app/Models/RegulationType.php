<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Resources\Regulation\RegulationTypeResource;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class RegulationType extends Model
{
    use HasFactory, HasRecursiveRelationships;

    protected $fillable = [
        'name',
        'user_id',
        'app',
        'parent_id'
    ];

    /**
     * Regulation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regulations()
    {
        return $this->hasMany(Regulation::class);
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

    // Recursive relationship to load all children
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    } 
}
