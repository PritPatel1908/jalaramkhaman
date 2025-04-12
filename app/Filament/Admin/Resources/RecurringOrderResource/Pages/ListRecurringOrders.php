<?php

namespace App\Filament\Admin\Resources\RecurringOrderResource\Pages;

use App\Filament\Admin\Resources\RecurringOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecurringOrders extends ListRecords
{
    protected static string $resource = RecurringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
