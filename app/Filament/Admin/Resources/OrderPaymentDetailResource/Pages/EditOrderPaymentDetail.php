<?php

namespace App\Filament\Admin\Resources\OrderPaymentDetailResource\Pages;

use App\Filament\Admin\Resources\OrderPaymentDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderPaymentDetail extends EditRecord
{
    protected static string $resource = OrderPaymentDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
