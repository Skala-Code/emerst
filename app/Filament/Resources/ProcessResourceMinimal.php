<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessResourceMinimal\Pages;
use App\Models\Process;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProcessResourceMinimal extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Processos (Debug)';

    protected static ?string $navigationGroup = 'Processos';

    protected static ?int $navigationSort = 99;

    // NOTA: Este resource está usando a estrutura simplificada da tabela processes
    // após a migração para a API do TRT - desabilitado por padrão
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados Mínimos')
                    ->schema([
                        Forms\Components\TextInput::make('processo')
                            ->label('Número do Processo')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('trt')
                            ->label('TRT')
                            ->maxLength(2),

                        Forms\Components\TextInput::make('classe')
                            ->label('Classe'),

                        Forms\Components\Toggle::make('sincronizado')
                            ->label('Sincronizado com API')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('autuado')
                            ->label('Data de Autuação'),

                        Forms\Components\DateTimePicker::make('distribuido')
                            ->label('Data de Distribuição'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('processo')
                    ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trt')
                    ->label('TRT'),
                Tables\Columns\IconColumn::make('sincronizado')
                    ->label('Sincronizado')
                    ->boolean(),
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
            'index' => Pages\ListProcessesMinimal::route('/'),
            'create' => Pages\CreateProcessMinimal::route('/create'),
            'edit' => Pages\EditProcessMinimal::route('/{record}/edit'),
        ];
    }
}
