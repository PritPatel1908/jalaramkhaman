<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Enums\UnitIn;
use Filament\Tables;
use App\Enums\Status;
use Filament\Forms\Form;
use App\Enums\OrderPeriod;
use Filament\Tables\Table;
use App\Enums\PaymentCycle;
use App\Jobs\GenerateOrder;
use App\Models\RecurringOrder;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecurringOrderResource\Pages;
use App\Filament\Resources\RecurringOrderResource\RelationManagers;
use App\Filament\Resources\RecurringOrderResource\RelationManagers\RecurringOrderDetailRelationManager;

class RecurringOrderResource extends Resource
{
    protected static ?string $model = RecurringOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Section::make('Recurring Order Detail')
                            ->schema([
                                Forms\Components\Select::make('order_period')
                                    // ->default(OrderPeriod::Daily)
                                    ->options(OrderPeriod::class)
                                    ->native(false)
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('payment_cycle')
                                    // ->default(PaymentCycle::Daily)
                                    ->options(PaymentCycle::class)
                                    ->native(false)
                                    ->preload()
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make()
                            ->schema([
                                Repeater::make('recurring_order_details')
                                    ->relationship('recurring_order_details')
                                    ->label('Products Details')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship('products', 'name')
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
                                            ->required(),
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
                Tables\Columns\TextColumn::make('order_period')
                    ->formatStateUsing(fn($record) => OrderPeriod::from($record->order_period)->getLabel()),
                Tables\Columns\TextColumn::make('payment_cycle')
                    ->formatStateUsing(fn($record) => PaymentCycle::from($record->payment_cycle)->getLabel()),
                Tables\Columns\TextColumn::make('last_created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn($record) => Status::from($record->status)->getLabel()),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('status')
                        // ->icon('heroicon-o-key')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Order Status')
                                ->options(function (RecurringOrder $recurring_order) {
                                    if ($recurring_order->status === 1) {
                                        return [Status::End->value => Status::End->name];
                                    } else if ($recurring_order->status === 2) {
                                        return [Status::Start->value => Status::Start->name];
                                    } else if ($recurring_order->status === 4) {
                                        return [Status::Start->value => Status::Start->name];
                                    }
                                })
                                ->native(false)
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (array $data, RecurringOrder $recurring_order): void {
                            $recurring_order->status = $data['status'];
                            $recurring_order->save();
                            if (Status::from($data['status'])->name === 'Start') {
                                GenerateOrder::dispatch($recurring_order);
                            }
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecurringOrders::route('/'),
            'create' => Pages\CreateRecurringOrder::route('/create'),
            'edit' => Pages\EditRecurringOrder::route('/{record}/edit'),
        ];
    }
}
