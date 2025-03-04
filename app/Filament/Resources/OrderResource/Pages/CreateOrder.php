<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\Status;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\OrderResource;
use App\Jobs\GenerateOrder;
use Carbon\Carbon;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        $this->record->user_id = auth()->user()->id;
        $this->record->created_date = Carbon::now();
        $this->record->status = Status::Waiting;
        $this->record->save();
        GenerateOrder::dispatch($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
