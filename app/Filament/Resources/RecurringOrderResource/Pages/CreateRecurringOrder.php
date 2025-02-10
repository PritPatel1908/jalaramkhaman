<?php

namespace App\Filament\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RecurringOrderResource;

class CreateRecurringOrder extends CreateRecord
{
    protected static string $resource = RecurringOrderResource::class;

    protected function afterCreate(): void
    {
        $this->record->user_id = auth()->user()->id;
        $this->record->save();
    }
}
