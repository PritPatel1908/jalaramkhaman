<?php

namespace App\Filament\Resources\OrderPaymentDetailResource\Pages;

use App\Filament\Resources\OrderPaymentDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderPaymentDetails extends ListRecords
{
    protected static string $resource = OrderPaymentDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
