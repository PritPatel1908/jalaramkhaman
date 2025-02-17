<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Enums\Status;
use App\Models\Payment;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderSchedule;
use App\Models\RecurringOrderDetailSchedule;

class RecurringOrderScheduleGenerator
{
    protected RecurringOrder $recurringOrder;
    protected $total;

    public function __construct(RecurringOrder $recurringOrder)
    {
        $this->recurringOrder = $recurringOrder;
    }

    public function generateRecurringOrderSchedule(): void
    {
        $recurring_order_schedule = RecurringOrderSchedule::create([
            'order_period' => $this->recurringOrder->order_period,
            'created_date' => Carbon::today(),
            'payment_cycle' => $this->recurringOrder->payment_cycle,
            'user_id' => $this->recurringOrder->user_id,
            'status' => 5
        ]);

        foreach ($this->recurringOrder->recurring_order_details as $recurring_order_detail) {
            RecurringOrderDetailSchedule::create([
                'qty' => $recurring_order_detail->qty,
                'qty_in' => $recurring_order_detail->qty_in,
                'product_id' => $recurring_order_detail->product_id,
                'order_schedule_id' => $recurring_order_schedule->id,
            ]);
        }

        $this->recurringOrder->last_created_date = $recurring_order_schedule->created_date;
        if ($this->recurringOrder->order_period === 1) {
            $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addDay();
        } elseif ($this->recurringOrder->order_period === 1) {
            $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addWeek();
        } else {
            $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addMonth();
        }
        $this->recurringOrder->save();

        dd($recurring_order_schedule);
        foreach ($recurring_order_schedule->recurring_order_detail_schedules as $schedule) {
            if ($this->recurringOrder->user->user_type === 'business') {
                $product_type = $schedule->products->business_type_product_price->first();

                $base_price = $product_type->price;
                $base_unit = $product_type->price_in;

                $purchase_qty = $schedule->qty;
                $purchase_unit = $schedule->qty_in;
                dd($base_unit, $purchase_unit);

                $converted_price = $this->convertPrice($base_price, $base_unit, $purchase_unit);

                if ($converted_price !== null) {
                    $this->total += $converted_price * $purchase_qty;
                } else {
                    $this->total += $base_price * $purchase_qty;
                }
            }
        }
        Payment::create([]);
    }

    public function convertPrice($price, $unit, $toUnit)
    {
        $conversionRates = [
            'g' => ['kg' => 0.001],
            'kg' => ['g' => 1000],
            'ml' => ['ltr' => 0.001],
            'ltr' => ['ml' => 1000],
        ];

        if ($unit === $toUnit) {
            return $price; // No conversion needed
        }

        if (isset($conversionRates[$unit][$toUnit])) {
            return $price * $conversionRates[$unit][$toUnit];
        }

        return null; // Conversion not found
    }
}
