<?php

namespace App\Filament\Resources\RecurringOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RecurringOrderDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'recurring_order_details';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('Products Details')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('products', 'name')
                            ->native(false)
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity in KG/LTR')
                            ->required()
                    ])
                    ->columns(2)
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('recurring_order.last_created_date')
            ->columns([
                Tables\Columns\TextColumn::make('products.name'),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantity In KG/LTR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
