<?php

namespace App\Filament\Resources\RecurringOrderScheduleResource\Pages;

use App\Filament\Resources\RecurringOrderScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecurringOrderSchedules extends ListRecords
{
    protected static string $resource = RecurringOrderScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
