<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\OrderPeriod;
use Filament\Tables\Table;
use App\Enums\PaymentCycle;
use App\Models\RecurringOrder;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecurringOrderResource\Pages;
use App\Filament\Resources\RecurringOrderResource\RelationManagers;
use App\Filament\Resources\RecurringOrderResource\RelationManagers\RecurringOrderDetailRelationManager;
use Filament\Forms\Components\Repeater;

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
                                            ->label('Quantity in KG/LTR')
                                            ->required()
                                    ])
                                    ->columns(2)
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
                Tables\Columns\ImageColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
