<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UnitIn;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Models\RecurringOrder;
use Illuminate\Support\Facades\Mail;
use App\Models\RecurringOrderSchedule;
use App\Mail\RecurringOrderNotifyEmail;
use App\Models\OrderPaymentDetail;
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
        if ($this->recurringOrder->status == 7) {
            if ($this->recurringOrder->user->order_period === 1) {
                $this->recurringOrder->next_created_date = $this->recurringOrder->next_created_date->addDay();
            } elseif ($this->recurringOrder->user->order_period === 1) {
                $this->recurringOrder->next_created_date = $this->recurringOrder->next_created_date->addWeek();
            } else {
                $this->recurringOrder->next_created_date = $this->recurringOrder->next_created_date->addMonth();
            }
            $this->recurringOrder->save();
        } else {
            $recurring_order_schedule = RecurringOrderSchedule::create([
                'order_period' => $this->recurringOrder->user->order_period,
                'created_date' => Carbon::today()->format('Y-m-d'),
                'payment_cycle' => $this->recurringOrder->user->payment_cycle,
                'user_id' => $this->recurringOrder->user_id,
                'status' => 5
            ]);

            foreach ($this->recurringOrder->recurring_schedules as $recurring_schedule) {
                RecurringOrderDetailSchedule::create([
                    'qty' => $recurring_schedule->qty,
                    'unit_in' => $recurring_schedule->unit_in,
                    'product_id' => $recurring_schedule->product_id,
                    'order_schedule_id' => $recurring_order_schedule->id,
                ]);
            }

            $this->recurringOrder->last_created_date = $recurring_order_schedule->created_date;
            if ($this->recurringOrder->user->order_period === 1) {
                $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addDay();
            } elseif ($this->recurringOrder->user->order_period === 1) {
                $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addWeek();
            } else {
                $this->recurringOrder->next_created_date = $recurring_order_schedule->created_date->addMonth();
            }
            $this->recurringOrder->save();

            // $recurring_schedule_schedules = RecurringOrderDetailSchedule::where('order_schedule_id', $recurring_order_schedule->id)->get();
            foreach ($recurring_order_schedule->recurring_schedule_schedules as $schedule) {
                if ($this->recurringOrder->user->user_type === 'business') {
                    $product_type = $schedule->products->business_type_product_price->first();
                    $price_type = 'business';

                    if (!$product_type) {
                        \Illuminate\Support\Facades\Log::error("No {$price_type} price found for product ID: {$schedule->products->id}");
                        continue;
                    }

                    $base_price = $product_type->price;
                    $per_unit_qty = $product_type->per;
                    $base_unit = UnitIn::from($product_type->unit_in)->getLabel();

                    $purchase_qty = $schedule->qty;
                    $purchase_unit = UnitIn::from($schedule->unit_in)->getLabel();

                    \Illuminate\Support\Facades\Log::info("CALCULATION DATA - Product: {$schedule->products->name}, Base price: {$base_price}, Per unit qty: {$per_unit_qty}, Base unit: {$base_unit}, Purchase qty: {$purchase_qty}, Purchase unit: {$purchase_unit}");

                    $converted_price = $this->convertPrice($base_price, $per_unit_qty, $base_unit, $purchase_unit);
                    \Illuminate\Support\Facades\Log::info("Converted price per unit: " . ($converted_price ?? 'NULL'));

                    if ($converted_price !== null) {
                        $item_total = $converted_price * $purchase_qty;
                        \Illuminate\Support\Facades\Log::info("Calculation: {$converted_price} (converted price) × {$purchase_qty} (qty) = {$item_total}");
                    } else {
                        // Fallback when conversion isn't possible
                        $pricePerUnit = $base_price / $per_unit_qty;
                        $item_total = $pricePerUnit * $purchase_qty;
                        \Illuminate\Support\Facades\Log::info("FALLBACK Calculation: {$base_price} ÷ {$per_unit_qty} × {$purchase_qty} = {$item_total}");
                    }

                    $this->total += $item_total;
                    \Illuminate\Support\Facades\Log::info("Updated running total: {$this->total}");
                }
            }

            \Illuminate\Support\Facades\Log::info("FINAL TOTAL: {$this->total}");

            $payment = Payment::create([
                'oderabel_type' => $recurring_order_schedule::class,
                'oderabel_id' => $recurring_order_schedule->id,
                'total_amount' => $this->total,
                'pending_payment_amount' => $this->total,
                'payment_status' => PaymentStatus::Pending,
                'payment_date' => Carbon::today()->format('Y-m-d'),
                'user_id' => $this->recurringOrder->user_id
            ]);

            \Illuminate\Support\Facades\Log::info("Created payment with ID: {$payment->id}, Amount: {$payment->total_amount}");

            OrderPaymentDetail::create([
                'oderabel_type' => $recurring_order_schedule::class,
                'oderabel_id' => $recurring_order_schedule->id,
                'paymentabel_type' => $payment::class,
                'paymentabel_id' => $payment->id,
                'user_id' => $this->recurringOrder->user_id
            ]);

            Mail::to($this->recurringOrder->user->email)->send(new RecurringOrderNotifyEmail($recurring_order_schedule, $this->recurringOrder->user));
        }
    }

    public function convertPrice($price, $perUnitQty, $baseUnit, $toUnit)
    {
        \Illuminate\Support\Facades\Log::info("CONVERSION REQUEST - Price: {$price} for {$perUnitQty} {$baseUnit}, converting to {$toUnit}");

        // Guard against division by zero
        if ($perUnitQty <= 0) {
            \Illuminate\Support\Facades\Log::error("Invalid per unit quantity: {$perUnitQty}, must be > 0");
            return null;
        }

        // Units have a hierarchy - we need the base-most unit as our reference point
        $toBaseUnit = [
            'Kilogram' => ['unit' => 'Gram', 'factor' => 1000],
            'Gram' => ['unit' => 'Gram', 'factor' => 1],
            'Liter' => ['unit' => 'Milliliter', 'factor' => 1000],
            'Milliliter' => ['unit' => 'Milliliter', 'factor' => 1],
            'No' => ['unit' => 'No', 'factor' => 1],
        ];

        // Step 1: Convert price to the smallest unit
        $baseUnitInfo = $toBaseUnit[$baseUnit] ?? null;
        $targetUnitInfo = $toBaseUnit[$toUnit] ?? null;

        if (!$baseUnitInfo || !$targetUnitInfo) {
            \Illuminate\Support\Facades\Log::error("Unknown unit: {$baseUnit} or {$toUnit}");
            return null;
        }

        // Make sure we're working with compatible unit types
        if ($baseUnitInfo['unit'] !== $targetUnitInfo['unit']) {
            \Illuminate\Support\Facades\Log::error("Incompatible units: {$baseUnit} and {$toUnit}");
            return null;
        }

        // First, get the total price for the entire quantity in the base unit
        $totalPriceForEntireQuantity = $price;
        \Illuminate\Support\Facades\Log::info("Total price for entire quantity: {$totalPriceForEntireQuantity}");

        // Then, calculate the number of smallest units we have
        $totalSmallestUnits = $perUnitQty * $baseUnitInfo['factor'];
        \Illuminate\Support\Facades\Log::info("Total {$baseUnitInfo['unit']}s: {$perUnitQty} {$baseUnit} = {$totalSmallestUnits} {$baseUnitInfo['unit']}");

        // Calculate price per smallest unit
        $pricePerSmallestUnit = $totalPriceForEntireQuantity / $totalSmallestUnits;
        \Illuminate\Support\Facades\Log::info("Price per {$baseUnitInfo['unit']}: {$totalPriceForEntireQuantity} ÷ {$totalSmallestUnits} = {$pricePerSmallestUnit}");

        // Calculate price per target unit
        $pricePerTargetUnit = $pricePerSmallestUnit * $targetUnitInfo['factor'];
        \Illuminate\Support\Facades\Log::info("Price per {$toUnit}: {$pricePerSmallestUnit} × {$targetUnitInfo['factor']} = {$pricePerTargetUnit}");

        return $pricePerTargetUnit;
    }
}
