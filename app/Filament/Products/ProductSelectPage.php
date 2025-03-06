<?php

namespace App\Filament\Products\Pages;

use App\Models\User;
use App\Models\Guest;
use App\Models\Product;
use Filament\Pages\Page;

class ProductSelectPage extends Page
{
    public $products;
    public $selectedProducts;
    public User $user;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.products.pages.select-product-page';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = "";

    public function mount()
    {
        $this->selectedProducts = [];
        $this->products = Product::all();
        $this->user = $this->log->user;
    }

    public function toggleProductSelection($productId)
    {
        if (in_array($productId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$servableId]);
        } else {
            $this->selectedItems[] = $servableId;
        }
    }



    public function toggleMealSelection($servableId)
    {
        if (in_array($servableId, $this->selectedMeals)) {
            $this->selectedMeals = array_diff($this->selectedMeals, [$servableId]);
        } else {
            $this->selectedMeals[] = $servableId;
        }
    }

    public function cancelOrder()
    {
        $this->log->status = PunchStatus::Ignored;
        $this->log->is_ignored = true;
        $this->log->is_calculated = true;
        $this->log->process_tags()->delete();
        $this->log->process_tags()->create(['name' => "User Cancelled Order From Kiosk."]);
        $this->log->save();
        redirect(KioskHome::getUrl());
        return;
    }

    public function submitOrder()
    {
        $printit = true;

        if (count($this->selectedItems) == 0 && count($this->selectedMeals) == 0) {
            Notification::make()
                ->title('Error')
                ->body("Please select any item to continue.")
                ->danger()
                ->send();
            return;
        }

        $pc = new KioskPunchCalculator($this->log);

        if ($pc->checkAlreadyCalculated()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retriveMachine()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if ($pc->machine->kiosk->printer) {
            switch ($pc->machine->kiosk->printer->connection_type) {
                case "CupsPrintConnector":
                    $connector = new CupsPrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
                case "DummyPrintConnector":
                    $connector = new DummyPrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
                case "FilePrintConnector":
                    $connector = new FilePrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
                // case "MultiplePrintConnector":
                //     $connector = new MultiplePrintConnector($pc->machine->kiosk->printer->connection_string);
                // $printer = new Printer($connector);
                //     break;
                case "NetworkPrintConnector":
                    $connector = new NetworkPrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
                // case "PrintConnector":
                //     $connector = new PrintConnector($pc->machine->kiosk->printer->connection_string);
                // $printer = new Printer($connector);
                //     break;
                case "RawbtPrintConnector":
                    $connector = new RawbtPrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
                // case "UriPrintConnector":
                //     $connector = new UriPrintConnector($pc->machine->kiosk->printer->connection_string);
                //     $printer = new Printer($connector);
                //     break;
                case "WindowsPrintConnector":
                    $connector = new WindowsPrintConnector($pc->machine->kiosk->printer->connection_string);
                    $printer = new Printer($connector);
                    break;
            }
        } else {
            $printit = false;
        }
        if (!$pc->retriveUser()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retriveCanteen()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->isCanteenOpen()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retriveMachingRules()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retriveTimingRule()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retrivePricingRulesWithOrder($this->selectedItems, $this->selectedMeals)) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->retriveOrderFlow()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if (!$pc->placeFinalOrder()) {
            redirect(KioskHome::getUrl());
            return;
        }

        if ($printit) {

            foreach ($pc->order->order_details as $orderDetail) {
                $printer->setJustification($pc->machine->kiosk->printer->logo_position);
                // $image = storage_path('app/public/' . $pc->employee->company->logo_path);
                // $image = storage_path('app/public/images/download.png');
                // $img = EscposImage::load($image, true);

                // $printer->graphics($img, $pc->machine->kiosk->printer->logo_size);

                $printer->setJustification($pc->machine->kiosk->printer->servable_position);
                $printer->setTextSize($pc->machine->kiosk->printer->servable_height, $pc->machine->kiosk->printer->servable_weight);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text($orderDetail->servable->name_code . "\n");
                $printer->setUnderline(Printer::UNDERLINE_NONE);

                $printer->text("\n\n");

                $printer->setJustification($pc->machine->kiosk->printer->detail_position);
                $printer->setTextSize($pc->machine->kiosk->printer->detail_height, $pc->machine->kiosk->printer->detail_weight);

                $printer->text(
                    "Order Details\n" .
                        "------------\n" .
                        "Canteen : " . $pc->order->canteen->canteen_name . "\n" .
                        "User Name : " . $pc->order->user->name . "\n" .
                        "User Code : " . $pc->order->user->user_code . "\n" .
                        "Order ID: #" . $pc->order->id . "\n" .
                        "Order Time : " . $pc->order->order_time->format('Y-m-d h:i:s') . "\n" .
                        "---------------------\n" .
                        "Quantity : " . $orderDetail->quantity . "\n" .
                        "Price : " . $orderDetail->employee_contribution . "\n"
                );

                $printer->text(
                    "---------------------\n" .
                        "Total : " . ($orderDetail->price * $orderDetail->quantity) . "\n" .
                        "Grand Total : " . ($orderDetail->price * $orderDetail->quantity) . "\n" .
                        "---------------------\n"
                );

                $printer->text(
                    "Payment Method : " . $pc->payment_method->payment_method_code . "\n"
                );

                $printer->cut();
            }
            $printer->close();
        }
        session()->flash('status', 'Order #' . $pc->order->id . " placed successfully.");
        redirect(KioskHome::getUrl());
    }
}
