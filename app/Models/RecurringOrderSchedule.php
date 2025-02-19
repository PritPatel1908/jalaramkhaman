<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use Illuminate\Database\Eloquent\Model;

class RecurringOrderSchedule extends Model
{
    protected $table = 'recurring_order_schedules';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'order_period' => 'int',
        'payment_cycle' => 'int',
        'created_date' => 'datetime',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'order_period',
        'created_date',
        'payment_cycle',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recurring_order_detail_schedules()
    {
        return $this->hasMany(RecurringOrderDetailSchedule::class, 'order_schedule_id');
    }
}
