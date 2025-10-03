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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados Mínimos')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Empresa')
                            ->relationship('company', 'name')
                            ->required(),

                        Forms\Components\Select::make('office_id')
                            ->label('Escritório')
                            ->relationship('office', 'name')
                            ->required(),

                        Forms\Components\TextInput::make('number')
                            ->label('Número do Processo')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Ativo',
                                'suspended' => 'Suspenso',
                                'archived' => 'Arquivado',
                                'completed' => 'Concluído',
                                'aguardando_api_trt' => 'Aguardando API TRT',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Data de Início')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
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
