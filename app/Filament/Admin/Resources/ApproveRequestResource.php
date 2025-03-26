<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use App\Enums\Status;
use App\Filament\Admin\Resources\ApproveRequestResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\RecurringOrder;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ApproveRequestResource extends Resource
{
    protected static ?string $model = RecurringOrder::class;

    protected static ?string $label = 'Approve Requests';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 50;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.order_period')
                    ->formatStateUsing(fn($record) => OrderPeriod::from($record->user->order_period)->getLabel()),
                Tables\Columns\TextColumn::make('user.payment_cycle')
                    ->formatStateUsing(fn($record) => PaymentCycle::from($record->user->payment_cycle)->getLabel()),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn($record) => Status::from($record->status)->getLabel()),
                Tables\Columns\TextColumn::make('main_status')
                    ->formatStateUsing(function ($record) {
                        if ($record->main_status === 'waiting_for_approve') {
                            return new HtmlString('<span class="text-yellow-500">Waiting for Approval</span>');
                        } elseif ($record->main_status === 'waiting_for_approve') {
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
                Tables\Actions\ViewAction::make()
                    ->hidden(function ($record) {
                        if ($record->id == 1) {
                            return true;
                        }
                        return false;
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
            'index' => Pages\ListApproveRequests::route('/'),
            'create' => Pages\CreateApproveRequest::route('/create'),
            'view' => Pages\ViewApproveRequest::route('/{record}'),
            'edit' => Pages\EditApproveRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();
        $query->where("user_id", '!=', 1);
        return $query;
    }
}
