<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'created_date' => 'datetime',
        'user_id' => 'int',
        'status' => 'int',
    ];

    protected $fillable = [
        'created_date',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
