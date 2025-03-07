<?php

namespace App\Filament\Resources;

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
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\OrderDetailRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Repeater::make('order_details')
                                    ->relationship('order_details')
                                    ->label('Products Details')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship('products', 'name')
                                            // ->getOptionLabelFromRecordUsing(fn(User $record): string => ($record->name ?? "") . " (" . ($record->user_code ?? "") . ")")
                                            ->getOptionLabelFromRecordUsing(function ($record) {
                                                if (Auth::user()->user_type == 'business') {
                                                    return ($record->name) . " (₹" . ($record->business_type_product_price->first()->price) . '/' . ($record->business_type_product_price->first()->per) . ' ' . (UnitIn::from($record->business_type_product_price->first()->unit_in)->getLabel()) . ")";
                                                } elseif (Auth::user()->user_type == 'customer') {
                                                    return ($record->name) . " (₹" . ($record->customer_type_product_price->first()->price) . '/' . ($record->customer_type_product_price->first()->per) . ' ' . (UnitIn::from($record->customer_type_product_price->first()->unit_in)->getLabel()) . ")";
                                                }
                                            })
                                            ->native(false)
                                            ->preload()
                                            ->required(),
                                        Forms\Components\TextInput::make('qty')
                                            ->label('Quantity')
                                            ->required(),
                                        Forms\Components\Select::make('unit_in')
                                            // ->default(OrderPeriod::Daily)
                                            ->options(UnitIn::class)
                                            ->native(false)
                                            ->preload()
                                            ->required()
                                            ->live(),
                                    ])
                                    ->columns(3)
                            ]),
                    ])
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
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();
        $query->where("user_id", auth()->user()->id);
        return $query;
    }
}
