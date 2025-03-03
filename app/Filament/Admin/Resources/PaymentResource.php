<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('oderabel_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('oderabel_id')
                    ->numeric(),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('payment_status')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('payment_date'),
                Forms\Components\TextInput::make('payment_type')
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('oderabel_type')
                    ->label('Order Type')
                    ->formatStateUsing(fn($record) => match ($record->oderabel_type) {
                        'App\Models\RecurringOrderSchedule' => 'Recurring Order',
                        default => $record->RecurringOrderSchedule,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->formatStateUsing(function ($record) {
                        if ($record->payment_status != null) {
                            return PaymentStatus::from($record->payment_status)->getLabel();
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->formatStateUsing(function ($record) {
                        return $record->payment_date->format('d-m-Y');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->formatStateUsing(function ($record) {
                        if ($record->payment_type != null) {
                            return PaymentType::from($record->payment_type)->getLabel();
                        }
                    })
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
