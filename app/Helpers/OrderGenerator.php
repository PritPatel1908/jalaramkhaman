<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UnitIn;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Enums\PaymentStatus;
use App\Mail\OrderNotifyEmail;
use App\Models\OrderPaymentDetail;
use Illuminate\Support\Facades\Mail;

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
                    $item_total = $converted_price * $purchase_qty;
                    $this->total += $item_total;
                } else {
                    $item_total = $base_price * $purchase_qty;
                    $this->total += $item_total;
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

        $payment = Payment::create([
            'oderabel_type' => $this->order::class,
            'oderabel_id' => $this->order->id,
            'total_amount' => $this->total,
            'pending_payment_amount' => $this->total,
            'payment_status' => PaymentStatus::Pending,
            'payment_date' => Carbon::today()->format('Y-m-d'),
            'user_id' => $this->order->user_id
        ]);

        OrderPaymentDetail::create([
            'oderabel_type' => $this->order::class,
            'oderabel_id' => $this->order->id,
            'paymentabel_type' => $payment::class,
            'paymentabel_id' => $payment->id,
            'user_id' => $this->order->user_id
        ]);

        Mail::to($this->order->user->email)->send(new OrderNotifyEmail($this->order, $this->order->user));
    }

    public function convertPrice($price, $perUnitQty, $baseUnit, $toUnit)
    {
        // If units are the same, no conversion needed
        if ($baseUnit === $toUnit) {
            return $price / $perUnitQty;
        }

        $conversionRates = [
            'Gram' => ['Kilogram' => 0.001, 'Gram' => 1],
            'Kilogram' => ['Gram' => 1000, 'Kilogram' => 1],
            'Milliliter' => ['Liter' => 0.001, 'Milliliter' => 1],
            'Liter' => ['Milliliter' => 1000, 'Liter' => 1],
            'No' => ['No' => 1],
        ];

        // If conversion exists
        if (isset($conversionRates[$baseUnit][$toUnit])) {
            // Calculate price per base unit (e.g., price per 1 KG)
            $pricePerBaseUnit = $price / $perUnitQty;

            // Apply conversion factor to get price per target unit
            return $pricePerBaseUnit * $conversionRates[$baseUnit][$toUnit];
        }
        return null; // Conversion not found
    }
}
