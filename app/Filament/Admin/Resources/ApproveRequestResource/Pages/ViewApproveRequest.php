<?php

namespace App\Filament\Admin\Resources\ApproveRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Admin\Resources\ApproveRequestResource;

class ViewApproveRequest extends ViewRecord
{
    protected static string $resource = ApproveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->hidden(function ($record) {
                    if ($record->id == 1) {
                        return true;
                    }
                    return false;
                }),
        ];
    }
}
