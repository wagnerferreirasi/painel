<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Leandrocfe\FilamentPtbrFormFields\PtbrCpfCnpj;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(180),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->maxLength(120),
                PtbrCpfCnpj::make('document')
                    ->label('CPF/CNPJ')
                    ->unique(ignoreRecord:true),
                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(12),

                Forms\Components\Fieldset::make('address')
                    ->relationship('address')
                    ->label('Endereço')
                    ->schema([
                        Forms\Components\TextInput::make('zipcode')
                            ->label('CEP')
                            ->required()
                            ->maxLength(8),
                        Forms\Components\TextInput::make('street')
                            ->label('Rua')
                            ->required()
                            ->maxLength(180),
                        Forms\Components\TextInput::make('number')
                            ->label('Número')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\TextInput::make('complement')
                            ->label('Complemento')
                            ->maxLength(180),
                        Forms\Components\TextInput::make('district')
                            ->label('Bairro')
                            ->required()
                            ->maxLength(180),
                        Forms\Components\TextInput::make('city')
                            ->label('Cidade')
                            ->required()
                            ->maxLength(180),
                        Forms\Components\TextInput::make('uf')
                            ->label('Estado')
                            ->required()
                            ->maxLength(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('email')
                    ->url(fn ($record) => "mailto:{$record->email}")
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('document')
                    ->label('CPF'),
                Tables\Columns\TextColumn::make('address.street')
                    ->label('Endereço'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i:s'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
