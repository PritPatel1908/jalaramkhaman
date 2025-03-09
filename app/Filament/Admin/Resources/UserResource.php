<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderPeriod;
use App\Enums\PaymentCycle;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('fname')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('mname')
                            // ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('lname')
                            // ->required()
                            ->maxLength(20),
                    ])
                    ->columns(3),
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->required()
                            ->minLength(10)
                            ->maxLength(10),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_type')
                            ->label('User Type')
                            ->searchable()
                            ->options([
                                'business' => 'business',
                                'customer' => 'customer'
                            ])
                            ->preload()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->searchable()
                            ->options([
                                'male' => 'male',
                                'female' => 'female',
                                'other' => 'other'
                            ])
                            ->preload()
                            ->native(false),
                        Forms\Components\Select::make('order_period')
                            ->label('Order Period')
                            ->searchable()
                            ->options(OrderPeriod::class)
                            ->preload()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('payment_cycle')
                            ->label('Payment Cycle')
                            ->searchable()
                            ->options(PaymentCycle::class)
                            ->preload()
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(4),
                Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('dob'),
                        Forms\Components\Toggle::make('is_locked')
                            ->required()
                            ->inline(false),
                        Forms\Components\Toggle::make('is_activate')
                            ->required()
                            ->inline(false),
                    ])
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_type'),
                Tables\Columns\IconColumn::make('is_locked')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('profile_pic')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_activate')
                    ->boolean(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
