<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTypeProductPrice extends Model
{
    protected $table = 'business_type_product_prices';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'product_id' => 'int',
        'price' => 'float',
        'qty' => 'float',
        'price_in' => 'int',
    ];

    protected $fillable = [
        'price',
        'product_id',
        'qty',
        'price_in'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
