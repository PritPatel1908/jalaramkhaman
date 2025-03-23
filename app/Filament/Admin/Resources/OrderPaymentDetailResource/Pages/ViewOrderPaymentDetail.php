<?php

namespace App\Filament\Admin\Resources\OrderPaymentDetailResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Admin\Resources\OrderPaymentDetailResource;

class ViewOrderPaymentDetail extends ViewRecord
{
    protected static string $resource = OrderPaymentDetailResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make()
    //             ->hidden(function ($record) {
    //                 if ($record->id == 1) {
    //                     return true;
    //                 }
    //                 return false;
    //             }),
    //     ];
    // }
}
