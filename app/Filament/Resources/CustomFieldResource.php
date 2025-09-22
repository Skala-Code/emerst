<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFieldResource\Pages;
use App\Models\CustomField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Campos Customizados';

    protected static ?string $navigationGroup = 'Configurações';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_custom_fields') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_custom_fields') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermissionTo('edit_custom_fields') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermissionTo('delete_custom_fields') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('model_type')
                    ->label('Tipo de Modelo')
                    ->options([
                        'process' => 'Processo',
                        'service_order' => 'Ordem de Serviço',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('custom_tab_id')
                    ->label('Aba')
                    ->relationship(
                        'customTab',
                        'label',
                        fn (Builder $query, Forms\Get $get) => $query->where('model_type', $get('model_type'))
                    )
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('Nome (identificador)')
                    ->required()
                    ->maxLength(255)
                    ->alphaNum(),
                Forms\Components\TextInput::make('label')
                    ->label('Rótulo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipo de Campo')
                    ->options([
                        'text' => 'Texto',
                        'textarea' => 'Área de Texto',
                        'number' => 'Número',
                        'email' => 'E-mail',
                        'date' => 'Data',
                        'datetime' => 'Data e Hora',
                        'select' => 'Seleção',
                        'radio' => 'Radio Button',
                        'checkbox' => 'Checkbox',
                        'file' => 'Arquivo',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\KeyValue::make('options')
                    ->label('Opções')
                    ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['select', 'radio']))
                    ->keyLabel('Valor')
                    ->valueLabel('Rótulo'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem de Exibição')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('required')
                    ->label('Campo Obrigatório')
                    ->default(false),
                Forms\Components\Toggle::make('active')
                    ->label('Ativo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'process' => 'Processo',
                        'service_order' => 'Ordem de Serviço',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('customTab.label')
                    ->label('Aba')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Rótulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'text' => 'Texto',
                        'textarea' => 'Área de Texto',
                        'number' => 'Número',
                        'email' => 'E-mail',
                        'date' => 'Data',
                        'datetime' => 'Data e Hora',
                        'select' => 'Seleção',
                        'radio' => 'Radio Button',
                        'checkbox' => 'Checkbox',
                        'file' => 'Arquivo',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                Tables\Columns\IconColumn::make('required')
                    ->label('Obrigatório')
                    ->boolean(),
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
            'index' => Pages\ListCustomFields::route('/'),
            'create' => Pages\CreateCustomField::route('/create'),
            'edit' => Pages\EditCustomField::route('/{record}/edit'),
        ];
    }
}
