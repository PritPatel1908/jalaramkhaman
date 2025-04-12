<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Enums\Status;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\OrderResource;
use App\Jobs\GenerateOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $data['created_date'] = Carbon::now();
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extract product details
        $productDetails = $data['product_details'] ?? [];
        unset($data['product_details']);

        // Create the recurring order
        $record = static::getModel()::create($data);

        // Create recurring order details
        foreach ($productDetails as $detail) {
            $record->order_details()->create([
                'product_id' => $detail['product_id'],
                'qty' => $detail['qty'],
                'unit_in' => $detail['unit_in'],
            ]);
        }

        return $record;
    }

    // protected function afterCreate(): void
    // {
    //     GenerateOrder::dispatch($this->record);
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
