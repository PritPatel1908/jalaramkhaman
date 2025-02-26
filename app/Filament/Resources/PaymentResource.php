<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Payment;
use App\Enums\PaymentType;
use Filament\Tables\Table;
use App\Enums\PaymentStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 40;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                RepeatableEntry::make('')
                    // ->relationship('leave_balance_details')
                    ->schema([
                        Split::make([
                            Section::make()
                                ->schema([
                                    ImageEntry::make('products.product_image_path')
                                        ->label('')
                                        ->size(120)
                                        ->circular()
                                ])
                                ->columns(1),
                            Section::make()
                                ->schema([
                                    TextEntry::make('products.name')
                                        ->label(''),
                                    TextEntry::make('qty')
                                        ->label('')
                                        ->formatStateUsing(function ($record) {
                                            return $record->qty . ' ' . UnitIn::from($record->unit_in)->getLabel();
                                        }),
                                    TextEntry::make('products')
                                        ->label('')
                                        ->formatStateUsing(function ($record) {
                                            $auth_type = Auth::user()->user_type;
                                            if ($auth_type === 'business') {
                                                return $record->products->business_type_product_price->first()->price;
                                            } elseif ($auth_type === 'customer') {
                                                return $record->products->customer_type_product_price->first()->price;
                                            }
                                        })
                                ])
                                ->columns(1),
                        ])->from('md')
                    ])
                    ->grid(2)
            ])
            ->columns(1);
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
                Tables\Actions\ViewAction::make(),
            ]);
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
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
