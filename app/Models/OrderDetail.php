<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'qty' => 'float',
        'unit_in' => 'int',
        'product_id' => 'int',
        'order_id' => 'int',
    ];

    protected $fillable = [
        'qty',
        'unit_in',
        'product_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
