<?php

namespace App\Filament\Resources\RecurringOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RecurringOrderResource;

class CreateRecurringOrder extends CreateRecord
{
    protected static string $resource = RecurringOrderResource::class;

    protected function afterCreate(): void
    {
        $this->record->user_id = auth()->user()->id;
        $this->record->save();

        $formState = $this->form->getState();
        $productDetails = $formState['product_selector'] ?? [];

        dd($productDetails);
        // Log for debugging
        // Log::info('Form state:', $formState);
        // Log::info('Product details:', ['details' => $productDetails]);

        // // If still empty, try to get from the request
        // if (empty($productDetails)) {
        //     $productDetails = request()->input('data.product_selector', []);
        //     Log::info('Product details from request:', ['details' => $productDetails]);
        // }

        // // Create recurring order details
        // if (!empty($productDetails)) {
        //     foreach ($productDetails as $detail) {
        //         $this->record->recurring_order_details()->create([
        //             'product_id' => $detail['product_id'],
        //             'qty' => $detail['qty'],
        //             'unit_in' => $detail['unit_in'],
        //         ]);
        //     }
        // }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
