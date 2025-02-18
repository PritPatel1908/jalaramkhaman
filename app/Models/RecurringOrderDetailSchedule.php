<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringOrderDetailSchedule extends Model
{
    protected $table = 'recurring_order_detail_schedules';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'qty' => 'float',
        'unit_in' => 'int',
        'product_id' => 'int',
        'order_schedule_id' => 'int',
    ];

    protected $fillable = [
        'qty',
        'unit_in',
        'product_id',
        'order_schedule_id',
    ];

    public function recurring_order_schedule()
    {
        return $this->belongsTo(RecurringOrderSchedule::class, 'order_schedule_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
