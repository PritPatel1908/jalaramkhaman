<?php

namespace App\Filament\Admin\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Enums\PaymentVia;
use App\Enums\PaymentType;
use Filament\Tables\Table;
use App\Enums\PaymentStatus;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 40;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('oderabel_type')
    //                 ->maxLength(255),
    //             Forms\Components\TextInput::make('oderabel_id')
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('total_amount')
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('payment_status')
    //                 ->numeric(),
    //             Forms\Components\DateTimePicker::make('payment_date'),
    //             Forms\Components\TextInput::make('payment_type')
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('user_id')
    //                 ->numeric(),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('oderabel_type')
                    ->label('Order Type')
                    ->formatStateUsing(fn($record) => match ($record->oderabel_type) {
                        'App\Models\RecurringOrderSchedule' => 'Recurring Order',
                        'App\Models\Order' => 'Order',
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
                Tables\Actions\Action::make('edit')
                    ->label('Edit Payment')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Payment Amount')
                            ->readOnly(),
                        Forms\Components\Select::make('payment_type')
                            ->label('Payment Type')
                            ->options(PaymentType::class)
                            ->native(false)
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('payment_via')
                            ->label('Payment Via')
                            ->hidden(function (Get $get) {
                                return $get('payment_type') == '1';
                            })
                            ->options([
                                PaymentVia::Cash->getLabel(),
                                PaymentVia::UPI->getLabel(),
                                PaymentVia::Other->getLabel()
                            ])
                            ->native(false)
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->hidden(function (Get $get) {
                                return $get('payment_via') == null;
                            })
                            ->options(PaymentStatus::class)
                            ->native(false)
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, Payment $payment): void {
                        $payment->payment_type = $data['payment_type'];
                        $payment->payment_via = $data['payment_via'];
                        $payment->payment_status = $data['payment_status'];
                        $payment->payment_complate_date = Carbon::now();
                        $payment->save();
                    }),
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
