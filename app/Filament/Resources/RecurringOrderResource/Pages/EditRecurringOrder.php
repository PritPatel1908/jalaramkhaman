<?php

namespace App\Filament\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RecurringOrderResource;
use App\Filament\Resources\RecurringOrderResource\RelationManagers\RecurringOrderDetailRelationManager;

class EditRecurringOrder extends EditRecord
{
    protected static string $resource = RecurringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RecurringOrderDetailRelationManager::class,
        ];
    }
}
