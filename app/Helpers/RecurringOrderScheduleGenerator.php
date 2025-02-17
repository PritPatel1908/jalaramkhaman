<?php

namespace App\Helpers;

use App\Enums\Status;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderDetailSchedule;
use App\Models\RecurringOrderSchedule;
use Carbon\Carbon;

class RecurringOrderScheduleGenerator
{
    protected RecurringOrder $recurringOrder;

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
    }
}
