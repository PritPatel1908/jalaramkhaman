<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Guest;
use App\Models\Employee;
use App\Enums\OrderStatus;
use App\Models\OrderDetail;
use App\Helpers\GeneralHelper;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class AmountsChart extends ChartWidget
{
    use InteractsWithPageFilters;
    // use HasWidgetShield;
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = "10s";
    protected static ?string $heading = 'Amounts';

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
            $pending_payment = $filteredData->pending_payment;
            $success_payment = $filteredData->success_payment;
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
        // dd($this->filters);
        if (is_array($this->filters) && key_exists("range", $this->filters)) {
            $range = $this->filters["range"];
            $range = explode(' - ', $range);
            $startDate = Carbon::parse($range[0])->startOfDay();
            $endDate = Carbon::parse($range[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        $user_type = Auth::user()->user_type;
        $payment = Payment::where('user_id', Auth::user()->id);
        $orderDetails->select(DB::raw('sum(company_contribution) as company_contribution, sum(employee_contribution) as employee_contribution'));
        $orderDetails->whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->where('status', OrderStatus::Delivered);
            //Filter By Order Time
            $query->whereBetween(
                'order_time',
                [
                    $startDate,
                    $endDate,
                ]
            );
            if (is_array($this->filters) && key_exists("canteens", $this->filters) && count($this->filters["canteens"]) > 0) {
                $query->whereIn('canteen_id', $this->filters["canteens"]);
            }
            if (is_array($this->filters) && key_exists("location_id", $this->filters)) {
                $query->whereHas('user', function ($query) {
                    $query->whereHasMorph('authable', [Employee::class, Guest::class], function ($query) {
                        $query->where('location_id', $this->filters["location_id"]);
                    });
                });
            }
            if (is_array($this->filters) && key_exists("companies", $this->filters) && count($this->filters["companies"]) > 0) {
                $query->whereHas('user', function ($query) {
                    $query->whereHasMorph('authable', [Employee::class, Guest::class], function ($query) {
                        $query->whereIn('company_id', $this->filters["companies"]);
                    });
                });
            }
        });
        // $orderDetails->groupBy(["servable_type", "servable_id"]);
        // dd(GeneralHelper::getEloquentSqlWithBindings($orderDetails));
        return $orderDetails->first();
    }

    protected function getType(): string
    {
        return  'bar';
    }
}
