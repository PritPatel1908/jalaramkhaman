<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Employee;
use App\Enums\OrderStatus;
use App\Models\OrderDetail;
use App\Enums\PaymentStatus;
use App\Helpers\GeneralHelper;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PaymentsChart extends ChartWidget
{
    use InteractsWithPageFilters;
    // use HasWidgetShield;
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = "10s";
    protected static ?string $heading = 'Payments';

    protected static ?array $options = [
        'backgroundColor' =>  [
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
        ],
        'borderColor' => [
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
        ],
        'borderWidth' => 2,
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $pending_payment = 0;
        $success_payment = 0;
        $labels = ["Pending Payment", "Success Payment"];
        $filteredData = $this->getEloquentQuery();
        if ($filteredData) {
            $user_type = Auth::user()->user_type;
            if ($user_type == 'business') {
                $pending_payment = $filteredData->where('user_id', Auth::user()->id)->where('payment_status', PaymentStatus::Pending)->sum('total_amount');
                $success_payment = $filteredData->where('user_id', Auth::user()->id)->where('payment_status', PaymentStatus::Completed)->sum('total_amount');
            } elseif ($user_type == 'customer') {
                $pending_payment = $filteredData->where('payment_status', PaymentStatus::Pending)->sum('total_amount');
                $success_payment = $filteredData->where('payment_status', PaymentStatus::Completed)->sum('total_amount');
            }
        }
        $rdata = [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data' => [$pending_payment, $success_payment],
                ],
            ],
            'labels' => $labels,

        ];
        return $rdata;
    }

    protected function getEloquentQuery()
    {
        if (is_array($this->filters) && key_exists("range", $this->filters)) {
            $range = $this->filters["range"];
            $range = explode(' - ', $range);
            $startDate = Carbon::parse($range[0])->startOfDay();
            $endDate = Carbon::parse($range[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        $payment = Payment::whereBetween(
            'payment_date',
            [
                $startDate,
                $endDate,
            ]
        )->get();
        return $payment;
    }

    protected function getType(): string
    {
        return  'bar';
    }
}
