<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\Status;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OrderResource;
use App\Jobs\GenerateOrder;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('approve_reject')
                ->label('Approve/Reject')
                ->form([
                    Select::make('main_status')
                        ->label('Approve/Reject Order Status')
                        ->options(function (Order $order) {
                            if ($order->main_status === 'waiting_for_approve') {
                                return ['approved' => 'Approved', 'rejected' => 'Rejected'];
                            } else if ($order->main_status === 'approved') {
                                return ['rejected' => 'Rejected'];
                            } else {
                                return ['waiting_for_approve' => 'Waiting for Approval'];
                            }
                        })
                        ->native(false)
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data, Order $order): void {
                    if ($data['main_status'] === 'approved') {
                        $order->status = 5;
                    } elseif ($data['main_status'] === 'rejected') {
                        $order->status = 2;
                    } else {
                        $order->status = 4;
                    }
                    $order->main_status = $data['main_status'];
                    $order->save();
                    GenerateOrder::dispatch($order);
                })
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Approve/Reject Order')
                ->modalSubheading('Are you sure you want to approve/reject this order?')
                ->modalButton('Yes, Approve/Reject'),
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

        $data['product_details'] = $record->order_details()
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
        $record->order_details()->delete();

        // Create new details
        foreach ($productDetails as $detail) {
            $record->order_details()->create([
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
