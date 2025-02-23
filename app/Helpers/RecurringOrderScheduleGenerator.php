<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UnitIn;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderSchedule;
use App\Models\RecurringOrderDetailSchedule;

class RecurringOrderScheduleGenerator
{
    protected RecurringOrder $recurringOrder;
    protected $total = 0;

    public function __construct(RecurringOrder $recurringOrder)
    {
        $this->recurringOrder = $recurringOrder;
    }

    public function generateRecurringOrderSchedule(): void
    {
        $recurring_order_schedule = RecurringOrderSchedule::create([
            'order_period' => $this->recurringOrder->order_period,
            'created_date' => Carbon::today()->format('Y-m-d'),
            'payment_cycle' => $this->recurringOrder->payment_cycle,
            'user_id' => $this->recurringOrder->user_id,
            'status' => 5
        ]);

        foreach ($this->recurringOrder->recurring_order_details as $recurring_order_detail) {
            RecurringOrderDetailSchedule::create([
                'qty' => $recurring_order_detail->qty,
                'unit_in' => $recurring_order_detail->unit_in,
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

        // $recurring_order_detail_schedules = RecurringOrderDetailSchedule::where('order_schedule_id', $recurring_order_schedule->id)->get();
        foreach ($recurring_order_schedule->recurring_order_detail_schedules as $schedule) {
            if ($this->recurringOrder->user->user_type === 'business') {
                $product_type = $schedule->products->business_type_product_price->first();

                $base_price = $product_type->price;
                $per_unit_qty = $product_type->per;
                $base_unit = UnitIn::from($product_type->unit_in)->getLabel();

                $purchase_qty = $schedule->qty;
                $purchase_unit = UnitIn::from($schedule->unit_in)->getLabel();

                $converted_price = $this->convertPrice($base_price, $per_unit_qty, $base_unit, $purchase_unit);

                if ($converted_price !== null) {
                    $this->total += $converted_price * $purchase_qty;
                } else {
                    $this->total += $base_price * $purchase_qty;
                }
            }
        }

        Payment::create([
            'oderabel_type' => $recurring_order_schedule::class,
            'oderabel_id' => $recurring_order_schedule->id,
            'total_amount' => $this->total,
            'payment_status' => PaymentStatus::Pending,
            'payment_date' => Carbon::today()->format('Y-m-d'),
            'user_id' => $this->recurringOrder->user_id
        ]);
    }

    public function convertPrice($price, $perUnitQty, $perUnit, $toUnit)
    {
        $conversionRates = [
            'GRAM' => ['KG' => 0.001, 'GRAM' => 1],
            'KG' => ['GRAM' => 1000, 'KG' => 1],
            'ML' => ['LTR' => 0.001, 'ML' => 1],
            'LTR' => ['ML' => 1000, 'LTR' => 1],
        ];

        // Convert per unit to the base unit price
        if (isset($conversionRates[$perUnit][$toUnit])) {
            $unitConversionFactor = $conversionRates[$perUnit][$toUnit];
            $pricePerGram = $price / $perUnitQty; // Price per 1 gram
            return $pricePerGram * (1 / $unitConversionFactor);
        }

        return null; // Conversion not found
    }
}
