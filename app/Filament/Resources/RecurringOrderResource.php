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
use App\Models\RecurringOrder;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecurringOrderResource\Pages;
use App\Filament\Resources\RecurringOrderResource\RelationManagers;
use App\Filament\Resources\RecurringOrderResource\RelationManagers\RecurringOrderDetailRelationManager;
use App\Models\RecurringOrderDetail;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\HtmlString;
use App\Forms\Components\ProductSelector;
use Filament\Notifications\Notification;

class RecurringOrderResource extends Resource
{
    protected static ?string $model = RecurringOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return Auth::user()->user_type == 'business';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->description('Select products for your recurring order')
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
                Tables\Columns\TextColumn::make('user.order_period')
                    ->formatStateUsing(fn($record) => OrderPeriod::from($record->user->order_period)->getLabel()),
                Tables\Columns\TextColumn::make('user.payment_cycle')
                    ->formatStateUsing(fn($record) => PaymentCycle::from($record->user->payment_cycle)->getLabel()),
                Tables\Columns\TextColumn::make('last_created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_created_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn($record) => Status::from($record->status)->getLabel()),
                Tables\Columns\TextColumn::make('main_status')
                    ->formatStateUsing(function ($record) {
                        if ($record->main_status === 'waiting_for_approve') {
                            return new HtmlString('<span class="text-yellow-500">Waiting for Approval</span>');
                        } elseif ($record->main_status === 'approved') {
                            return new HtmlString('<span class="text-green-500">Approved</span>');
                        } else {
                            return new HtmlString('<span class="text-red-500">Rejected</span>');
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->hidden(fn($record) => Status::from($record->status)->name === 'End'),
                    Tables\Actions\Action::make('status')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Order Status')
                                ->options(function (RecurringOrder $recurring_order) {
                                    if ($recurring_order->status === 1) {
                                        return [Status::Pause->value => Status::Pause->name, Status::End->value => Status::End->name];
                                    } else if ($recurring_order->status === 7) {
                                        return [Status::Start->value => Status::Start->name, Status::End->value => Status::End->name];
                                    }
                                })
                                ->native(false)
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (array $data, RecurringOrder $recurring_order): void {
                            $recurring_order->status = $data['status'];
                            $recurring_order->save();
                            // if (Status::from($data['status'])->name === 'Start' && $recurring_order->last_created_date == null && $recurring_order->next_created_date == null) {
                            //     GenerateOrder::dispatch($recurring_order);
                            // }
                        })
                        ->hidden(fn($record) => Status::from($record->status)->name === 'End'),
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

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();
        $query->where("user_id", auth()->user()->id);
        return $query;
    }
}
