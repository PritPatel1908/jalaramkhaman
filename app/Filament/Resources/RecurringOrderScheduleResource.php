<?php

namespace App\Filament\Resources;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use App\Enums\Status;
use App\Filament\Resources\RecurringOrderScheduleResource\Pages;
use App\Filament\Resources\RecurringOrderScheduleResource\RelationManagers;
use App\Models\RecurringOrderSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecurringOrderScheduleResource extends Resource
{
    protected static ?string $model = RecurringOrderSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_period')
                    ->formatStateUsing(fn($record) => OrderPeriod::from($record->order_period)->getLabel()),
                Tables\Columns\TextColumn::make('payment_cycle')
                    ->formatStateUsing(fn($record) => PaymentCycle::from($record->payment_cycle)->getLabel()),
                Tables\Columns\TextColumn::make('created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn($record) => Status::from($record->status)->getLabel()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListRecurringOrderSchedules::route('/'),
            'create' => Pages\CreateRecurringOrderSchedule::route('/create'),
            'edit' => Pages\EditRecurringOrderSchedule::route('/{record}/edit'),
        ];
    }
}
