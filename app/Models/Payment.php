<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'oderabel_id' => 'int',
        'price' => 'float',
        'status' => 'int',
        'payment_date' => 'datetime',
        'payment_type' => 'int',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'oderabel_type',
        'oderabel_id',
        'price',
        'status',
        'payment_date',
        'payment_type',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
