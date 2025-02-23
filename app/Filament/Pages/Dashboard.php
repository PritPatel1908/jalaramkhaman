<?php

namespace App\Filament\Pages;

use Closure;
use Illuminate\Support\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    // protected static string $view = 'filament.pages.dashboard';

    use HasFiltersAction;

    protected function getHeaderActions(): array
    {

        return [
            FilterAction::make()
                ->form([
                    DateRangePicker::make('range')
                        ->format('Y-m-d')
                        ->displayFormat('YYYY-MM-DD')
                        ->maxSpan(['months' => 1])
                        ->minDate(session('current_year', date("Y")) . "-01-01")
                        ->maxDate(session('current_year', date("Y")) . "-12-31")
                        ->ranges(
                            [
                                'Today' => [
                                    now(),
                                    now(),
                                ],
                                'Yesterday' => [
                                    now()->subDay(),
                                    now()->subDay(),
                                ],
                                'This month' => [
                                    now()->firstOfMonth(),
                                    now()->lastOfMonth(),
                                ],
                                'Last month' => [
                                    now()->subMonth()->firstOfMonth(),
                                    now()->subMonth()->lastOfMonth(),
                                ],
                                'Previous month' => [
                                    now()->subMonth(2)->firstOfMonth(),
                                    now()->subMonth(2)->lastOfMonth(),
                                ],
                            ]
                        )
                        ->rules([
                            fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                $dates = explode(' - ', $value);
                                if (count($dates) !== 2) {
                                    $fail('The :attribute is invalid.');
                                }
                                $from = Carbon::parse($dates[0]);
                                $to = Carbon::parse($dates[1]);
                                //same year
                                if ($from->year !== $to->year) {
                                    $fail('Dates are not in the same year');
                                }
                            },
                        ])
                ]),
        ];
    }
}
