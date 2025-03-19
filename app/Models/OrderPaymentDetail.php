<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentDetail extends Model
{
    protected $table = 'order_payment_details';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'oderabel_id' => 'int',
        'paymentabel_id' => 'int',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'oderabel_type',
        'paymentabel_type',
        'oderabel_id',
        'paymentabel_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
