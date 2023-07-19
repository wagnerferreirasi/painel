<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Leandrocfe\FilamentPtbrFormFields\PtbrCep;
use Leandrocfe\FilamentPtbrFormFields\PtbrCpfCnpj;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $modelLabelPlural = 'Usuários';

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
                    ->rule('cpf_ou_cnpj')
                    ->disabled(fn (string $context): bool => $context === 'edit')
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
                        PtbrCep::make('zipcode')
                            ->label('Cep')
                            ->required()
                            ->viaCep(
                                mode: 'suffix',
                                errorMessage: 'CEP inválido.',
                                setFields: [
                                    'street' => 'logradouro',
                                    'number' => 'numero',
                                    'complement' => 'complemento',
                                    'district' => 'bairro',
                                    'city' => 'localidade',
                                    'uf' => 'uf'
                                ]
                            ),
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
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->url(fn ($record) => "mailto:{$record->email}")
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
