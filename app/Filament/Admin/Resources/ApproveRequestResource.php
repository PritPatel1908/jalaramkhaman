<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\Status;
use Filament\Forms\Form;
use App\Enums\OrderPeriod;
use Filament\Tables\Table;
use App\Enums\PaymentCycle;
use App\Jobs\GenerateOrder;
use App\Models\RecurringOrder;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\ApproveRequestResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\ApprovalRequest;
use Filament\Tables\Columns\ViewColumn;

class ApproveRequestResource extends Resource
{
    protected static ?string $model = ApprovalRequest::class;

    protected static ?string $label = 'Approve Requests';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 50;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('type')->view('filament.tables.columns.type'),
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
                Tables\Actions\Action::make('approve_reject')
                    ->label('Approve/Reject')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                "approved" => "Approved",
                                "rejected" => "Rejected",
                            ])
                            ->native(false)
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data, ApprovalRequest $model): void {
                        $originalModel = $model->getOriginalModel();

                        if ($originalModel) {
                            $originalModel->status = Status::Start->value;
                            $originalModel->main_status = $data['status'];
                            $originalModel->save();

                            if (
                                $model->request_type === 'recurring' &&
                                $data['status'] === 'approved' &&
                                $originalModel->last_created_date == null &&
                                $originalModel->next_created_date == null
                            ) {
                                GenerateOrder::dispatch($originalModel);
                            }
                        }
                    })
                // ->hidden(fn($record) => Status::from($record->status)->name === 'End'),
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
            // 'create' => Pages\CreateApproveRequest::route('/create'),
            // 'view' => Pages\ViewApproveRequest::route('/{record}'),
            // 'edit' => Pages\EditApproveRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();
        $query->where("user_id", '!=', 1);
        $query->where("main_status", 'waiting_for_approve');
        return $query;
    }
}
