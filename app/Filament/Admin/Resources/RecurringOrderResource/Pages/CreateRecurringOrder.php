<?php

namespace App\Filament\Admin\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\RecurringOrderResource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateRecurringOrder extends CreateRecord
{
    protected static string $resource = RecurringOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
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
            $record->recurring_order_details()->create([
                'product_id' => $detail['product_id'],
                'qty' => $detail['qty'],
                'unit_in' => $detail['unit_in'],
            ]);
        }

        Notification::make()
            ->title("Request Sent for Approval")
            ->body("Request Sent for Approval.")
            ->persistent()
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Debug method to see what's happening
    // protected function afterCreate(): void
    // {
    //     $record = $this->getRecord();

    //     // Log the selected products for debugging
    //     \Illuminate\Support\Facades\Log::info('Selected Products:', [
    //         'recurring_order_id' => $record->id,
    //         'selected_products' => request()->input('selected_products', []),
    //         'details_count' => $record->recurring_order_details()->count(),
    //     ]);
    // }
}
