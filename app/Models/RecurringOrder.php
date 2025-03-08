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
        'last_created_date' => 'datetime',
        'next_created_date' => 'datetime',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'last_created_date',
        'next_created_date',
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
