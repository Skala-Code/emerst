<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LawyerResource\Pages;
use App\Models\Lawyer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LawyerResource extends Resource
{
    protected static ?string $model = Lawyer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Advogados';

    protected static ?string $navigationGroup = 'Gestão';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_lawyers') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_lawyers') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermissionTo('edit_lawyers') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermissionTo('delete_lawyers') ?? false;
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
                    ->helperText('Selecione uma ou mais empresas que este advogado atende'),

                Forms\Components\Select::make('offices')
                    ->label('Escritórios')
                    ->relationship('offices', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->helperText('Selecione um ou mais escritórios onde este advogado trabalha'),
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('oab')
                    ->label('OAB')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->maxLength(255),
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
                    ->formatStateUsing(fn ($record) =>
                        $record->companies->pluck('name')->join(', ')
                    ),

                Tables\Columns\TextColumn::make('offices.name')
                    ->label('Escritórios')
                    ->searchable()
                    ->formatStateUsing(fn ($record) =>
                        $record->offices->pluck('name')->join(', ')
                    ),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('oab')
                    ->label('OAB')
                    ->searchable(),
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
            'index' => Pages\ListLawyers::route('/'),
            'create' => Pages\CreateLawyer::route('/create'),
            'edit' => Pages\EditLawyer::route('/{record}/edit'),
        ];
    }
}
