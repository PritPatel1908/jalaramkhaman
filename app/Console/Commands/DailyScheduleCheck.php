<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Jobs\GenerateOrder;
use App\Models\RecurringOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyScheduleCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-schedule-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('user_type', 'business')->get();
        if (count($users) > 0) {
            Log::error('Start checking daily, weekly, monthly base recurring order at ' . Carbon::today()->format('Y-m-d'));
            foreach ($users as $user) {
                $recurringOrders = RecurringOrder::where('user_id', $user->id)->get();
                if (count($recurringOrders) > 0) {
                    foreach ($recurringOrders as $recurringOrder) {
                        if ($recurringOrder->next_created_date->format('Y-m-d') == Carbon::today()->format('Y-m-d')) {
                            GenerateOrder::dispatch($recurringOrder);
                        } else {
                            Log::error($user->name . ' #' . $recurringOrder->id . ' is not match to daily, monthly, yearly recurring schedule at ' . Carbon::today()->format('Y-m-d'));
                        }
                    }
                } else {
                    Log::error($user->name . ' is not have any recurring order schedule');
                }
            }
        } else {
            Log::error('Not found any user at' . Carbon::today()->format('Y-m-d'));
        }
    }
}
