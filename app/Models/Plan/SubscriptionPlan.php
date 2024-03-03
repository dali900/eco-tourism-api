<?php

namespace App\Models\Plan;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'interval',
        'amount',
        'created_by',
        'updated_by'
    ];

    const INTERVAL_PER_MONTH = 'm';
    const INTERVAL_PER_QUARTER = '4m';
    const INTERVAL_PER_6_MONTHS = '6m';
    const INTERVAL_PER_YEAR = 'y';
    

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
}
