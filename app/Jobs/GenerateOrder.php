<?php

namespace App\Jobs;

use App\Helpers\RecurringOrderScheduleGenerator;
use App\Models\RecurringOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateOrder implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;

    public $uniqueFor = 3600;
    /**
     * Create a new job instance.
     */
    public function __construct($record)
    {
        $this->record = $record;
    }

    public function uniqueId()
    {
        return $this->record->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (get_class($this->record) === RecurringOrder::class) {
            $recurring_order_schedule = new RecurringOrderScheduleGenerator($this->record);
            $recurring_order_schedule->generateRecurringOrderSchedule();
        }
    }
}
