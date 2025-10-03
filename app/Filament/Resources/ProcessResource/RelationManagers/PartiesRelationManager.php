<?php

namespace App\Filament\Resources\ProcessResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PartiesRelationManager extends RelationManager
{
    protected static string $relationship = 'parties';

    protected static ?string $title = 'Partes do Processo';

    protected static ?string $recordTitleAttribute = 'nome';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('polo')
                    ->label('Polo')
                    ->options([
                        'ATIVO' => 'Polo Ativo',
                        'PASSIVO' => 'Polo Passivo',
                        'TERCEIROS' => 'Terceiros Interessados',
                    ])
                    ->required()
                    ->columnSpan(1),

                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'RECLAMANTE' => 'Reclamante',
                        'RECLAMADO' => 'Reclamado',
                        'TERCEIRO INTERESSADO' => 'Terceiro Interessado',
                        'ADVOGADO' => 'Advogado',
                    ])
                    ->required()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('nome')
                    ->label('Nome/Razão Social')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                Forms\Components\TextInput::make('documento')
                    ->label('CPF/CNPJ')
                    ->maxLength(20)
                    ->columnSpan(1),

                Forms\Components\Select::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'CPF' => 'CPF',
                        'CNPJ' => 'CNPJ',
                        'RG' => 'RG',
                        'OAB' => 'OAB',
                    ])
                    ->columnSpan(1),

                Forms\Components\TextInput::make('login')
                    ->label('Login')
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\Toggle::make('utiliza_login_senha')
                    ->label('Utiliza Login e Senha')
                    ->default(false)
                    ->columnSpan(1),

                Forms\Components\Section::make('Endereço')
                    ->schema([
                        Forms\Components\TextInput::make('endereco.logradouro')
                            ->label('Logradouro')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('endereco.numero')
                            ->label('Número')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('endereco.complemento')
                            ->label('Complemento')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('endereco.bairro')
                            ->label('Bairro')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('endereco.municipio')
                            ->label('Município')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('endereco.estado')
                            ->label('Estado')
                            ->maxLength(2),
                        Forms\Components\TextInput::make('endereco.cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(9),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Textarea::make('papeis')
                    ->label('Papéis/Funções')
                    ->helperText('Informações sobre os papéis desempenhados pela parte')
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('polo')
                    ->label('Polo')
                    ->colors([
                        'success' => 'ATIVO',
                        'danger' => 'PASSIVO',
                        'warning' => 'TERCEIROS',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'ATIVO' => 'Polo Ativo',
                        'PASSIVO' => 'Polo Passivo',
                        'TERCEIROS' => 'Terceiros',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'RECLAMANTE' => 'Reclamante',
                        'RECLAMADO' => 'Reclamado',
                        'TERCEIRO INTERESSADO' => 'Terceiro Int.',
                        'ADVOGADO' => 'Advogado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('documento')
                    ->label('Documento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo Doc.')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('utiliza_login_senha')
                    ->label('Login/Senha')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('polo')
                    ->label('Polo')
                    ->options([
                        'ATIVO' => 'Polo Ativo',
                        'PASSIVO' => 'Polo Passivo',
                        'TERCEIROS' => 'Terceiros Interessados',
                    ]),
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'RECLAMANTE' => 'Reclamante',
                        'RECLAMADO' => 'Reclamado',
                        'TERCEIRO INTERESSADO' => 'Terceiro Interessado',
                        'ADVOGADO' => 'Advogado',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Parte'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('polo', 'asc');
    }
}
