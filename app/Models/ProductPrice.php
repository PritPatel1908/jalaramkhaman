<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'product_prices';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'product_id' => 'int',
        'price' => 'float',
    ];

    protected $fillable = [
        'price',
        'type',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
