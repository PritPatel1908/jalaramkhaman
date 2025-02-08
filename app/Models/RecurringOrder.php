<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use Illuminate\Database\Eloquent\Model;

class RecurringOrder extends Model
{
    protected $table = 'recurring_orders';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'order_period' => OrderPeriod::class,
        'payment_cycle' => PaymentCycle::class,
        'last_created_date' => 'datetime',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'order_period',
        'last_created_date',
        'payment_cycle',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recurring_order_details()
    {
        return $this->hasMany(RecurringOrderDetail::class);
    }
}
