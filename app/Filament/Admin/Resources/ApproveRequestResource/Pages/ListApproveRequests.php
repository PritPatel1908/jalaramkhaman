<?php

namespace App\Filament\Admin\Resources\ApproveRequestResource\Pages;

use App\Filament\Admin\Resources\ApproveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApproveRequests extends ListRecords
{
    protected static string $resource = ApproveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
