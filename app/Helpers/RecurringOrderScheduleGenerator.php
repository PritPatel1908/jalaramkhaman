<?php

namespace App\Helpers;

use App\Enums\Status;
use App\Models\RecurringOrder;
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
        RecurringOrderSchedule::create([
            'order_period' => $this->recurringOrder->order_period,
            'created_date' => Carbon::today(),
            'payment_cycle' => $this->recurringOrder->payment_cycle,
            'user_id' => $this->recurringOrder->user_id,
            'status' => 5
        ]);
    }
}
