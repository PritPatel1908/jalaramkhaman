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
use App\Forms\Components\ProductSelector;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->description('Select products for your order')
                    ->schema([
                        ProductSelector::make('product_details')
                            ->label('Select Products')
                            ->columnSpanFull(),
                    ]),
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
