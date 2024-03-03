<?php

namespace App\Models\Plan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeTrialPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'days',
        'created_by',
        'updated_by'
    ];

    /**
     * Admin User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Admin User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get standard free trial plan
     *
     * @return FreeTrialPlan
     */
    public static function getStandardPlan() : FreeTrialPlan {
        return self::where('key', 'free-trial-standard')->first();
    }
}
