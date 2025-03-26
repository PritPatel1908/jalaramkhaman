<?php

namespace App\Filament\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RecurringOrderResource;
use Filament\Notifications\Notification;

class EditRecurringOrder extends EditRecord
{
    protected static string $resource = RecurringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the recurring order details for the product selector
        $record = $this->getRecord();

        $data['product_details'] = $record->recurring_order_details()
            ->get()
            ->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'qty' => $detail->qty,
                    'unit_in' => $detail->unit_in,
                ];
            })
            ->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $productDetails = $this->data['product_details'] ?? [];

        $record->status = '4';
        $record->main_status = 'waiting_for_approve';
        $record->save();

        // Delete existing details
        $record->recurring_order_details()->delete();

        // Create new details
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
    }
}
