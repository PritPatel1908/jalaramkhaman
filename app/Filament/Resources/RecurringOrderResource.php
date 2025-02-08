<?php

namespace App\Filament\Resources;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use App\Filament\Resources\RecurringOrderResource\Pages;
use App\Filament\Resources\RecurringOrderResource\RelationManagers;
use App\Models\RecurringOrder;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Forms\Components\Select::make('order_period')
                            ->default(OrderPeriod::Daily)
                            ->options(OrderPeriod::class)
                            ->native(false)
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('payment_cycle')
                            ->default(PaymentCycle::Daily)
                            ->options(PaymentCycle::class)
                            ->native(false)
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->required()
                            ->numeric()
                            ->default(1),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_period'),
                Tables\Columns\TextColumn::make('last_created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_cycle'),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
