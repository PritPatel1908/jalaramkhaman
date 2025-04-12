<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\Status;
use App\Enums\UnitIn;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Enums\OrderPeriod;
use Filament\Tables\Table;
use App\Enums\PaymentCycle;
use App\Jobs\GenerateOrder;
use App\Models\Order;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\OrderDetailRelationManager;
use App\Forms\Components\ProductSelector;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->description('Select products for your order')
                    ->schema([
                        ProductSelector::make('product_details')
                            ->label('Select Products')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn($record) => Status::from($record->status)->getLabel()),
                Tables\Columns\TextColumn::make('main_status')
                    ->formatStateUsing(function ($record) {
                        if ($record->main_status === 'waiting_for_approve') {
                            return new HtmlString('<span class="text-yellow-500">Waiting for Approval</span>');
                        } elseif ($record->main_status === 'waiting_for_approve') {
                            return new HtmlString('<span class="text-green-500">Approved</span>');
                        } else {
                            return new HtmlString('<span class="text-red-500">Rejected</span>');
                        }
                    }),
            ])
            ->filters([
                //
            ])
            // ->actions([
            //     ActionGroup::make([
            //         Tables\Actions\EditAction::make(),
            //         Tables\Actions\Action::make('status')
            //             // ->icon('heroicon-o-key')
            //             ->form([
            //                 Forms\Components\Select::make('status')
            //                     ->label('Order Status')
            //                     ->options(function (Order $recurring_order) {
            //                         if ($recurring_order->status === 1) {
            //                             return [Status::End->value => Status::End->name];
            //                         } else if ($recurring_order->status === 2) {
            //                             return [Status::Start->value => Status::Start->name];
            //                         } else if ($recurring_order->status === 4) {
            //                             return [Status::Start->value => Status::Start->name];
            //                         }
            //                     })
            //                     ->native(false)
            //                     ->preload()
            //                     ->required(),
            //             ])
            //             ->action(function (array $data, Order $recurring_order): void {
            //                 $recurring_order->status = $data['status'];
            //                 $recurring_order->save();
            //                 if (Status::from($data['status'])->name === 'Start') {
            //                     GenerateOrder::dispatch($recurring_order);
            //                 }
            //             }),
            //     ])
            // ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    // $query = static::getModel()::query();
    // $query->where("user_id", auth()->user()->id)
    //     ->orderByRaw("CASE WHEN main_status = 'waiting_for_approve' THEN 0 ELSE 1 END")
    //     ->orderBy('created_date', 'desc');
    // return $query;
    // }
}
