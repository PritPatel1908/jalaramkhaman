<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTypeProductPrice extends Model
{
    protected $table = 'customer_type_product_prices';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'product_id' => 'int',
        'price' => 'float',
        'per' => 'int',
        'unit_in' => 'int',
    ];

    protected $fillable = [
        'price',
        'product_id',
        'per',
        'unit_in'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
