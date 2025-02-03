<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            // ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->relationship('category', 'name'),
                    ]),
                Section::make()
                    ->schema([
                        Forms\Components\Repeater::make('Business Type Product Price')
                            ->label('Product Price')
                            ->relationship('business_type_product_price')
                            ->hidden(fn() => auth()->user()->user_type == 'customer')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->required(),
                            ])
                            ->maxItems(1),
                        Forms\Components\Repeater::make('Customer Type Product Price')
                            ->label('Product Price')
                            ->relationship('customer_type_product_price')
                            ->hidden(fn() => auth()->user()->user_type == 'business')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->required(),
                            ])
                            ->maxItems(1),
                    ])
                    ->columns(1),
                Section::make('Product Stock')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->label('Stock In KG/LTR')
                        // ->required(),
                    ]),
                Section::make('Product Image')
                    ->columns(1)
                    ->schema([
                        Forms\Components\FileUpload::make('product_image_path')
                            ->label('Product Image')
                            ->columnSpanFull()
                            // ->avatar()
                            ->imageEditor()
                            ->default(null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('product_image_path'),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
