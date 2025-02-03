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
    ];

    protected $fillable = [
        'price',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
