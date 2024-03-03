<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_plan_id',
        'user_id',
        'status',
        'interval',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'app',
        'note'
    ];

    const STATUS_CREATED = 'created';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAST_DUE = 'past_due'; //a payment that has not been made at the end of its due date.
    const STATUS_UNPAID = 'unpaid';
    const STATUS_CANCELED = 'canceled'; //user plan has been canceled by admin
    const STATUS_EXPIRED = 'expired';  //after a certain number of days in past_due/unpaid status

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Subscription plan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

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
     * Cheks if subscription is active
     *
     * @return boolean
     */
    public function isActive()
    {
        $r = (
                ( 
                    Carbon::parse($this->end_date)->gt(Carbon::now()) && 
                    Carbon::parse($this->start_date)->lt(Carbon::now())
                ) && 
                $this->status !== Subscription::STATUS_EXPIRED &&
                $this->status !== Subscription::STATUS_CANCELED
            ) ? 1 : 0; 

        return $r;
    }
    
    /**
     * Make the isAvtive() check available outside of the model. 
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $status
     * @return boolean
     */
    public static function isActiveStaticCheck(string $start_date, string $end_date, string $status)
    {
        $tmpModel = new Subscription();
        $tmpModel->start_date = $start_date;
        $tmpModel->end_date = $end_date;
        $tmpModel->status = $status;
        return $tmpModel->isActive();
    }

    /**
     * Filter only active subscriptions
     *
     * @param $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', '!=', self::STATUS_EXPIRED)
            ->where('start_date', '<', date('Y-m-d H:i:s'))
            ->where('end_date', '>', date('Y-m-d H:i:s'));
    }

    public function getEndDateFormated(): string|null
    {
        if (!empty($this->end_date)){
            return Carbon::parse($this->end_date)->format("d.m.Y");
        }
        return null;
    }
}
