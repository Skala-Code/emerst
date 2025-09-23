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
                Forms\Components\Select::make('companies')
                    ->label('Empresas')
                    ->relationship('companies', 'name')
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Selecione uma ou mais empresas que este escritório atende'),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Endereço')
                    ->rows(3),
                Forms\Components\Toggle::make('active')
                    ->label('Ativo')
                    ->default(true),
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
