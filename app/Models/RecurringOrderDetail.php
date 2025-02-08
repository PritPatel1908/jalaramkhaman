<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringOrderDetail extends Model
{
    protected $table = 'recurring_order_details';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'qty' => 'float',
        'product_id' => 'int',
        'recurring_order_id' => 'int',
    ];

    protected $fillable = [
        'qty',
        'product_id',
        'recurring_order_id',
    ];

    public function recurring_order()
    {
        return $this->belongsTo(RecurringOrder::class);
    }
}
