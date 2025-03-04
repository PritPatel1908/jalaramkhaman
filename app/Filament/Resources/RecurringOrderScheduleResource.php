<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Enums\Status;
use App\Enums\UnitIn;
use App\Enums\OrderPeriod;
use Filament\Tables\Table;
use App\Enums\PaymentCycle;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Models\RecurringOrderSchedule;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecurringOrderScheduleResource\Pages;

class RecurringOrderScheduleResource extends Resource
{
    protected static ?string $model = RecurringOrderSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 40;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                RepeatableEntry::make('recurring_order_detail_schedules')
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
                Tables\Actions\ViewAction::make(),
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
