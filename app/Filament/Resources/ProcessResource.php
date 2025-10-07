<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessResource\Pages;
use App\Models\Process;
use App\Services\TrtApiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Processos TRT';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processos';

    protected static ?string $navigationGroup = 'Jurídico';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados do Processo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('processo')
                                    ->label('Número do Processo')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('0000000-00.0000.0.00.0000')
                                    ->helperText('Formato: NNNNNNN-DD.AAAA.J.TR.OOOO')
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('buscarApi')
                                            ->label('Buscar na API')
                                            ->icon('heroicon-o-arrow-path')
                                            ->action(function ($state, $set) {
                                                if (!$state) {
                                                    Notification::make()
                                                        ->title('Informe o número do processo')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                $trtService = new TrtApiService();
                                                $dados = $trtService->consultarProcesso($state);

                                                if ($dados) {
                                                    // Preenche todos os campos com os dados da API
                                                    $set('trt', $dados['trt'] ?? null);
                                                    $set('classe', $dados['classe'] ?? null);
                                                    $set('orgao_julgador', $dados['orgao_julgador'] ?? null);
                                                    $set('valor_causa', $dados['valor_causa'] ?? null);
                                                    $set('autuado', $dados['autuado'] ?? null);
                                                    $set('distribuido', $dados['distribuido'] ?? null);
                                                    $set('assuntos', $dados['assuntos'] ?? null);
                                                    $set('reclamantes', $dados['reclamantes'] ?? []);
                                                    $set('reclamados', $dados['reclamados'] ?? []);
                                                    $set('outros_interessados', $dados['outros_interessados'] ?? []);
                                                    $set('sincronizado', true);
                                                    $set('ultima_atualizacao_api', now());

                                                    Notification::make()
                                                        ->title('Dados importados com sucesso!')
                                                        ->success()
                                                        ->send();
                                                } else {
                                                    Notification::make()
                                                        ->title('Processo não encontrado na API')
                                                        ->danger()
                                                        ->send();
                                                }
                                            })
                                    ),

                                Forms\Components\TextInput::make('trt')
                                    ->label('TRT')
                                    ->maxLength(2),

                                Forms\Components\TextInput::make('classe')
                                    ->label('Classe'),

                                Forms\Components\TextInput::make('orgao_julgador')
                                    ->label('Órgão Julgador'),

                                Forms\Components\TextInput::make('valor_causa')
                                    ->label('Valor da Causa'),

                                Forms\Components\DateTimePicker::make('autuado')
                                    ->label('Data de Autuação')
                                    ->displayFormat('d/m/Y H:i'),

                                Forms\Components\DateTimePicker::make('distribuido')
                                    ->label('Data de Distribuição')
                                    ->displayFormat('d/m/Y H:i'),
                            ]),

                        Forms\Components\Textarea::make('assuntos')
                            ->label('Assuntos')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Partes Envolvidas')
                    ->schema([
                        Forms\Components\Repeater::make('reclamantes')
                            ->label('Reclamantes')
                            ->schema([
                                Forms\Components\TextInput::make('nome')
                                    ->label('Nome'),
                                Forms\Components\TextInput::make('cpf_cnpj')
                                    ->label('CPF/CNPJ'),
                                Forms\Components\TagsInput::make('advogados')
                                    ->label('Advogados')
                                    ->separator(','),
                            ])
                            ->columns(3)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['nome'] ?? null),

                        Forms\Components\Repeater::make('reclamados')
                            ->label('Reclamados')
                            ->schema([
                                Forms\Components\TextInput::make('nome')
                                    ->label('Nome'),
                                Forms\Components\TextInput::make('cpf_cnpj')
                                    ->label('CPF/CNPJ'),
                                Forms\Components\TagsInput::make('advogados')
                                    ->label('Advogados')
                                    ->separator(','),
                            ])
                            ->columns(3)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['nome'] ?? null),

                        Forms\Components\Repeater::make('outros_interessados')
                            ->label('Outros Interessados')
                            ->schema([
                                Forms\Components\TextInput::make('nome')
                                    ->label('Nome'),
                                Forms\Components\TextInput::make('cpf_cnpj')
                                    ->label('CPF/CNPJ'),
                                Forms\Components\TextInput::make('tipo')
                                    ->label('Tipo')
                                    ->placeholder('Ex: perito, assistente técnico'),
                            ])
                            ->columns(3)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => ($state['nome'] ?? '') . ($state['tipo'] ? ' (' . $state['tipo'] . ')' : '')),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Status da Sincronização')
                    ->schema([
                        Forms\Components\Toggle::make('sincronizado')
                            ->label('Sincronizado com API'),

                        Forms\Components\DateTimePicker::make('ultima_atualizacao_api')
                            ->label('Última Atualização via API')
                            ->displayFormat('d/m/Y H:i:s'),

                        Forms\Components\Textarea::make('error')
                            ->label('Erro na Sincronização')
                            ->rows(2)
                            ->visible(fn ($state) => !empty($state)),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('processo')
                    ->label('Número do Processo')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('trt')
                    ->label('TRT')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classe')
                    ->label('Classe')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('orgao_julgador')
                    ->label('Órgão Julgador')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->orgao_julgador),

                Tables\Columns\TextColumn::make('valor_causa')
                    ->label('Valor da Causa')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reclamantes')
                    ->label('Reclamantes')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state) || empty($state)) {
                            return '-';
                        }
                        $nomes = collect($state)->pluck('nome')->take(2)->toArray();
                        $texto = implode(', ', $nomes);
                        if (count($state) > 2) {
                            $texto .= ' (+' . (count($state) - 2) . ')';
                        }
                        return $texto;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('reclamados')
                    ->label('Reclamados')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state) || empty($state)) {
                            return '-';
                        }
                        $nomes = collect($state)->pluck('nome')->take(2)->toArray();
                        $texto = implode(', ', $nomes);
                        if (count($state) > 2) {
                            $texto .= ' (+' . (count($state) - 2) . ')';
                        }
                        return $texto;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('autuado')
                    ->label('Autuação')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('distribuido')
                    ->label('Distribuição')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('sincronizado')
                    ->label('Sincronizado')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ultima_atualizacao_api')
                    ->label('Últ. Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trt')
                    ->label('TRT')
                    ->options(function () {
                        return Process::query()
                            ->distinct()
                            ->whereNotNull('trt')
                            ->pluck('trt', 'trt')
                            ->mapWithKeys(fn ($trt) => [$trt => "TRT-{$trt}"])
                            ->toArray();
                    }),

                Tables\Filters\SelectFilter::make('classe')
                    ->label('Classe')
                    ->options(function () {
                        return Process::query()
                            ->distinct()
                            ->whereNotNull('classe')
                            ->pluck('classe', 'classe')
                            ->toArray();
                    }),

                Tables\Filters\TernaryFilter::make('sincronizado')
                    ->label('Sincronização')
                    ->placeholder('Todos')
                    ->trueLabel('Sincronizados')
                    ->falseLabel('Não sincronizados'),

                Tables\Filters\Filter::make('precisa_atualizacao')
                    ->label('Precisa Atualização')
                    ->query(fn ($query) => $query->precisandoAtualizacao()),
            ])
            ->actions([
                Tables\Actions\Action::make('sincronizar')
                    ->label('Sincronizar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->action(function (Process $record) {
                        $trtService = new TrtApiService();
                        $dados = $trtService->consultarProcesso($record->processo);

                        if ($dados) {
                            $record->atualizarDaApi($dados);
                            Notification::make()
                                ->title('Processo sincronizado com sucesso!')
                                ->success()
                                ->send();
                        } else {
                            $record->marcarErroSincronizacao('Não foi possível obter dados da API');
                            Notification::make()
                                ->title('Erro ao sincronizar processo')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('sincronizar_lote')
                    ->label('Sincronizar Selecionados')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records) {
                        $trtService = new TrtApiService();
                        $sucesso = 0;
                        $erro = 0;

                        foreach ($records as $record) {
                            $dados = $trtService->consultarProcesso($record->processo);
                            if ($dados) {
                                $record->atualizarDaApi($dados);
                                $sucesso++;
                            } else {
                                $record->marcarErroSincronizacao('Não foi possível obter dados da API');
                                $erro++;
                            }
                        }

                        Notification::make()
                            ->title("Sincronização concluída")
                            ->body("Sucesso: {$sucesso} | Erro: {$erro}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProcesses::route('/'),
            'create' => Pages\CreateProcess::route('/create'),
            'edit' => Pages\EditProcess::route('/{record}/edit'),
            'view' => Pages\ViewProcess::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::naoSincronizados()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}
