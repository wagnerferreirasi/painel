<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ClientResource\Pages;
use Leandrocfe\FilamentPtbrFormFields\PtbrPhone;
use Leandrocfe\FilamentPtbrFormFields\PtbrCpfCnpj;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $pluralModelLabel = 'Clientes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(180),
                Forms\Components\Select::make('lead_source')
                    ->label('Como conheceu a empresa?')
                    ->options([
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'google' => 'Google',
                        'linkedin' => 'Linkedin',
                        'youtube' => 'Youtube',
                        'other' => 'Outro',
                    ])
                    ->reactive(),
                Forms\Components\TextInput::make('lead_source_other')
                    ->label('Outro')
                    ->required()
                    ->maxLength(180)
                    ->hidden(fn (Closure $get) => $get('lead_source') !== 'other'),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->maxLength(120),
                Forms\Components\Radio::make('document_type')
                    ->label('Tipo de documento')
                    ->options([
                        'cpf' => 'CPF',
                        'rg' => 'RG',
                    ])
                    ->reactive()
                    ->required(),
                PtbrCpfCnpj::make('document')
                    ->id('cpf')
                    ->label('CPF')
                    ->rule('cpf')
                    ->cpf()
                    ->unique(ignoreRecord:true)
                    ->required(fn (Closure $get) => $get('document_type') === 'cpf')
                    ->hidden(fn (Closure $get) => $get('document_type') !== 'cpf'),
                Forms\Components\TextInput::make('document')
                    ->id('rg')
                    ->label('RG')
                    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('00.000.000-0'))
                    ->unique(ignoreRecord:true)
                    ->required(fn (Closure $get) => $get('document_type') === 'rg')
                    ->hidden(fn (Closure $get) => $get('document_type') !== 'rg'),
                PtbrPhone::make('phone')
                    ->label('Telefone'),
                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(12),
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
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('CPF')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClients::route('/'),
        ];
    }
}
