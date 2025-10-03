<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Escritórios';

    protected static ?string $navigationGroup = 'Gestão';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_offices') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_offices') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermissionTo('edit_offices') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermissionTo('delete_offices') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Dados do Escritório')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Dados Cadastrais')
                            ->schema([
                                Forms\Components\Select::make('companies')
                                    ->label('Empresas')
                                    ->relationship('companies', 'name')
                                    ->multiple()
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione uma ou mais empresas que este escritório atende')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('custom_name')
                                    ->label('Nome Personalizado')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('legal_name')
                                    ->label('Razão Social')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cnpj')
                                    ->label('CNPJ')
                                    ->mask('99.999.999/9999-99')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Select::make('contract_status')
                                    ->label('Situação Contratual')
                                    ->options([
                                        'active' => 'Ativo',
                                        'inactive' => 'Inativo',
                                        'pending' => 'Pendente',
                                        'suspended' => 'Suspenso',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('active')
                                    ->label('Ativo')
                                    ->default(true)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Endereço')
                            ->schema([
                                Forms\Components\TextInput::make('zip_code')
                                    ->label('CEP')
                                    ->mask('99999-999')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('address')
                                    ->label('Endereço')
                                    ->rows(2)
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('address_number')
                                    ->label('Número')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('complement')
                                    ->label('Complemento')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('state')
                                    ->label('UF')
                                    ->maxLength(2)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('city')
                                    ->label('Cidade')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Responsável Principal')
                            ->schema([
                                Forms\Components\TextInput::make('responsible_name')
                                    ->label('Nome Completo')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_cpf')
                                    ->label('CPF')
                                    ->mask('999.999.999-99')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_phone')
                                    ->label('Telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_email')
                                    ->label('E-mail')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_position')
                                    ->label('Função/Cargo')
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('companies.name')
                    ->label('Empresas')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) =>
                        $record->companies->pluck('name')->join(', ')
                    ),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone'),
                Tables\Columns\IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
