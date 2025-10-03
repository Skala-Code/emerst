<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomTabResource\Pages;
use App\Models\CustomTab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomTabResource extends Resource
{
    protected static ?string $model = CustomTab::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Abas Customizadas';

    protected static ?string $navigationGroup = 'Configurações';

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
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
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nome (identificador)')
                    ->required()
                    ->maxLength(255)
                    ->alphaNum(),
                Forms\Components\TextInput::make('label')
                    ->label('Rótulo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem de Exibição')
                    ->numeric()
                    ->default(0),
                Forms\Components\TagsInput::make('permissions')
                    ->label('Permissões (tipos de usuário)')
                    ->placeholder('Ex: admin, advogado, colaborador'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Rótulo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
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
            'index' => Pages\ListCustomTabs::route('/'),
            'create' => Pages\CreateCustomTab::route('/create'),
            'edit' => Pages\EditCustomTab::route('/{record}/edit'),
        ];
    }
}
