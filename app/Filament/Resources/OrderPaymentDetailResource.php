<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Enums\PaymentType;
use Filament\Tables\Table;
use App\Enums\PaymentStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\OrderPaymentDetail;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderPaymentDetailResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentDetailResource extends Resource
{
    protected static ?string $model = OrderPaymentDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 50;

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
                ViewColumn::make('total_amount')->view('tables.columns.total-amount'),
                ViewColumn::make('paid_amount')->view('tables.columns.paid-amount'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make("details")
                    ->label('Details')
                    // ->hidden(fn() => !auth()->user()?->can('debug_attendance::log'))
                    ->icon('heroicon-o-beaker')
                    ->modalSubmitAction(false)
                    ->modalContent(function (OrderPaymentDetail $record): View {
                        return view(
                            'filament.pages.actions.details',
                            [
                                'detail' => $record
                            ],
                        );
                    })
            ]);
        // ->actions([
        //     Tables\Actions\ViewAction::make(),
        // ]);
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
            'index' => Pages\ListOrderPaymentDetails::route('/'),
            'create' => Pages\CreateOrderPaymentDetail::route('/create'),
            'view' => Pages\ViewOrderPaymentDetail::route('/{record}'),
            'edit' => Pages\EditOrderPaymentDetail::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();
        $query->where("user_id", auth()->user()->id);
        return $query;
    }
}
