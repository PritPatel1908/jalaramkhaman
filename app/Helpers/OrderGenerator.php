<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UnitIn;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderNotifyEmail;
use App\Models\OrderDetail;

class OrderGenerator
{
    protected Order $order;
    protected $total = 0;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function generateOrder(): void
    {   // $recurring_order_detail_schedules = RecurringOrderDetailSchedule::where('order_schedule_id', $recurring_order_schedule->id)->get();
        foreach ($this->order->order_details as $order_detail) {
            if ($this->order->user->user_type === 'business') {
                $product_type = $order_detail->products->business_type_product_price->first();

                $base_price = $product_type->price;
                $per_unit_qty = $product_type->per;
                $base_unit = UnitIn::from($product_type->unit_in)->getLabel();

                $purchase_qty = $order_detail->qty;
                $purchase_unit = UnitIn::from($order_detail->unit_in)->getLabel();

                $converted_price = $this->convertPrice($base_price, $per_unit_qty, $base_unit, $purchase_unit);

                if ($converted_price !== null) {
                    $this->total += $converted_price * $purchase_qty;
                } else {
                    $this->total += $base_price * $purchase_qty;
                }
            } else {
                $product_type = $order_detail->products->customer_type_product_price->first();

                $base_price = $product_type->price;
                $per_unit_qty = $product_type->per;
                $base_unit = UnitIn::from($product_type->unit_in)->getLabel();

                $purchase_qty = $order_detail->qty;
                $purchase_unit = UnitIn::from($order_detail->unit_in)->getLabel();

                $converted_price = $this->convertPrice($base_price, $per_unit_qty, $base_unit, $purchase_unit);

                if ($converted_price !== null) {
                    $this->total += $converted_price * $purchase_qty;
                } else {
                    $this->total += $base_price * $purchase_qty;
                }
            }
        }

        Payment::create([
            'oderabel_type' => $this->order::class,
            'oderabel_id' => $this->order->id,
            'total_amount' => $this->total,
            'pending_payment_amount' => $this->total,
            'payment_status' => PaymentStatus::Pending,
            'payment_date' => Carbon::today()->format('Y-m-d'),
            'user_id' => $this->order->user_id
        ]);

        Mail::to($this->order->user->email)->send(new OrderNotifyEmail($this->order, $this->order->user));
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
