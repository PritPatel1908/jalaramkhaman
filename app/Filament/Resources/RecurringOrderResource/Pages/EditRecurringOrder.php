<?php

namespace App\Filament\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RecurringOrderResource;

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

    protected function afterSave(): void
    {
        // Try to get the product details from the form state
        $formState = $this->form->getState();
        $productDetails = $formState['product_selector'] ?? [];

        // Log for debugging
        Log::info('Form state:', $formState);
        Log::info('Product details:', ['details' => $productDetails]);

        // If still empty, try to get from the request
        if (empty($productDetails)) {
            $productDetails = request()->input('data.product_selector', []);
            Log::info('Product details from request:', ['details' => $productDetails]);
        }

        // Delete existing details
        $this->record->recurring_order_details()->delete();

        // Create new recurring order details
        if (!empty($productDetails)) {
            foreach ($productDetails as $detail) {
                $this->record->recurring_order_details()->create([
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'unit_in' => $detail['unit_in'],
                ]);
            }
        }
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        // Load existing details for the product selector
        $details = $this->record->recurring_order_details()
            ->get()
            ->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'qty' => $detail->qty,
                    'unit_in' => $detail->unit_in,
                ];
            })
            ->toArray();

        $this->form->fill([
            'product_selector' => $details,
        ]);
    }
}
