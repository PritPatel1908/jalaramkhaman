<?php

namespace App\Filament\Admin\Resources\ApproveRequestResource\Pages;

use App\Filament\Admin\Resources\ApproveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApproveRequest extends EditRecord
{
    protected static string $resource = ApproveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
