<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessResource\Pages;
use App\Models\Lawyer;
use App\Models\Office;
use App\Models\Process;
use App\Services\CustomFieldService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Processos';

    protected static ?string $navigationGroup = 'Processos';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_processes') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_processes') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermissionTo('edit_processes') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermissionTo('delete_processes') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // === SEÇÃO 1: INFORMAÇÕES BÁSICAS ===
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->label('Empresa')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('office_id', null)),
                    Forms\Components\Select::make('office_id')
                        ->label('Escritório')
                        ->options(function (callable $get) {
                            $companyId = $get('company_id');
                            if (! $companyId) {
                                return [];
                            }

                            return Office::where('company_id', $companyId)->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('lawyer_id', null)),
                    Forms\Components\Select::make('lawyer_id')
                        ->label('Advogado Responsável')
                        ->options(function (callable $get) {
                            $officeId = $get('office_id');
                            if (! $officeId) {
                                return [];
                            }

                            return Lawyer::where('office_id', $officeId)->pluck('name', 'id');
                        })
                        ->searchable(),
                ])
                ->columns(3),

            // === SEÇÃO 2: DADOS DO PROCESSO ===
            Forms\Components\Section::make('Dados do Processo')
                ->schema([
                    Forms\Components\TextInput::make('number')
                        ->label('Número do Processo')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('folder_number')
                        ->label('Número da Pasta')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('Descrição')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Ativo',
                            'suspended' => 'Suspenso',
                            'archived' => 'Arquivado',
                            'completed' => 'Concluído',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Data de Início')
                        ->required(),
                    Forms\Components\DatePicker::make('deadline')
                        ->label('Prazo Final'),
                ])
                ->columns(3),

            // === SEÇÃO 3: DADOS DO FUNCIONÁRIO/RECLAMANTE ===
            Forms\Components\Section::make('Dados do Funcionário/Reclamante')
                ->schema([
                    Forms\Components\TextInput::make('employee_function')
                        ->label('Função do Funcionário')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('city')
                        ->label('Cidade')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('state')
                        ->label('Estado')
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('admission_date')
                        ->label('Data de Admissão'),
                    Forms\Components\DatePicker::make('termination_date')
                        ->label('Data de Demissão'),
                ])
                ->columns(3),

            // === SEÇÃO 4: CONTROLE PROCESSUAL ===
            Forms\Components\Section::make('Controle Processual')
                ->schema([
                    Forms\Components\Select::make('procedural_phase')
                        ->label('Fase Processual')
                        ->options([
                            'inicial' => 'Inicial',
                            'contestacao' => 'Contestação',
                            'instrucao' => 'Instrução',
                            'sentenca' => 'Sentença',
                            'recurso_trt' => 'Recurso TRT',
                            'acordao_trt' => 'Acórdão TRT',
                            'recurso_tst' => 'Recurso TST',
                            'acordao_tst' => 'Acórdão TST',
                            'execucao' => 'Execução',
                            'acordo' => 'Acordo',
                            'arquivado' => 'Arquivado',
                        ]),
                    Forms\Components\TextInput::make('previous_phase')
                        ->label('Fase Anterior')
                        ->maxLength(255),
                    Forms\Components\Select::make('case_type')
                        ->label('Tipo de Caso')
                        ->options([
                            'trabalhista' => 'Trabalhista',
                            'civil' => 'Civil',
                            'criminal' => 'Criminal',
                            'tributario' => 'Tributário',
                            'administrativo' => 'Administrativo',
                        ]),
                    Forms\Components\Select::make('procedure_type')
                        ->label('Tipo de Rito')
                        ->options([
                            'ordinario' => 'Ordinário',
                            'sumario' => 'Sumário',
                            'sumarissimo' => 'Sumaríssimo',
                        ]),
                    Forms\Components\TextInput::make('law_firm')
                        ->label('Escritório de Advocacia')
                        ->maxLength(255),
                    Forms\Components\Select::make('defendant_type')
                        ->label('Tipo de Reclamada')
                        ->options([
                            'pessoa_fisica' => 'Pessoa Física',
                            'pessoa_juridica' => 'Pessoa Jurídica',
                            'empresa_publica' => 'Empresa Pública',
                            'autarquia' => 'Autarquia',
                        ]),
                ])
                ->columns(3),

            // === SEÇÃO 5: OBSERVAÇÕES E ACOMPANHAMENTO ===
            Forms\Components\Section::make('Observações e Acompanhamento')
                ->schema([
                    Forms\Components\Textarea::make('observations')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('monthly_movements')
                        ->label('Movimentações Mensais')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('procedural_progress')
                        ->label('Andamento Processual')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('decision_phase')
                        ->label('Fase da Decisão')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('situation')
                        ->label('Situação')
                        ->maxLength(255),
                ])
                ->columns(3),

            // === SEÇÃO 6: CONTROLE FINANCEIRO ===
            Forms\Components\Section::make('Controle Financeiro')
                ->schema([
                    Forms\Components\TextInput::make('interest_rate')
                        ->label('Taxa de Juros (%)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('interest_rate_diff')
                        ->label('Diferença Taxa de Juros (%)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('prescription_months')
                        ->label('Meses de Prescrição')
                        ->numeric(),
                ])
                ->columns(3),

            // === SEÇÃO 7: CONTROLE DE TEMPO E ÍNDICES ===
            Forms\Components\Section::make('Controle de Tempo e Índices')
                ->schema([
                    Forms\Components\TextInput::make('termination_to_filing_tr')
                        ->label('Demissão até Ajuizamento (TR %)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('filing_to_current_tr')
                        ->label('Ajuizamento até Atual (TR %)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('termination_to_filing_ipca')
                        ->label('Demissão até Ajuizamento (IPCA %)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('filing_to_current_ipca')
                        ->label('Ajuizamento até Atual (IPCA %)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                ])
                ->columns(2),

            // === SEÇÃO 8: PROVISÕES POR FASE ===
            Forms\Components\Section::make('Provisões por Fase')
                ->schema([
                    Forms\Components\TextInput::make('initial_provision')
                        ->label('Provisão Inicial')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('sentence_provision')
                        ->label('Provisão Sentença')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('trt_provision')
                        ->label('Provisão TRT')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('tst_provision')
                        ->label('Provisão TST')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('settlement_provision')
                        ->label('Provisão Acordo')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('current_provision')
                        ->label('Provisão Atual')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(3),

            // === SEÇÃO 9: DEPÓSITOS E PAGAMENTOS ===
            Forms\Components\Section::make('Depósitos e Pagamentos')
                ->schema([
                    Forms\Components\TextInput::make('appeal_deposits')
                        ->label('Depósitos Recursais')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('judicial_deposits')
                        ->label('Depósitos Judiciais')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('releases_payments')
                        ->label('Liberações/Pagamentos')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(3),

            // === SEÇÃO 10: PROVISÕES ATUALIZADAS ===
            Forms\Components\Section::make('Provisões Atualizadas')
                ->schema([
                    Forms\Components\TextInput::make('current_provision_tr')
                        ->label('Provisão Atual TR')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('previous_month_provision_tr')
                        ->label('Provisão Mês Anterior TR')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('current_provision_ipca')
                        ->label('Provisão Atual IPCA')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(3),

            // === SEÇÃO 11: STATUS DE PERDA E PREVISÕES ===
            Forms\Components\Section::make('Status de Perda e Previsões')
                ->schema([
                    Forms\Components\Select::make('loss_status_previous')
                        ->label('Status Perda Anterior')
                        ->options([
                            'provavel' => 'Provável',
                            'possivel' => 'Possível',
                            'remota' => 'Remota',
                        ]),
                    Forms\Components\Select::make('loss_status_current')
                        ->label('Status Perda Atual')
                        ->options([
                            'provavel' => 'Provável',
                            'possivel' => 'Possível',
                            'remota' => 'Remota',
                        ]),
                    Forms\Components\DatePicker::make('disbursement_forecast')
                        ->label('Previsão de Desembolso'),
                    Forms\Components\TextInput::make('probable_status_change')
                        ->label('Provável Mudança de Status')
                        ->maxLength(255),
                ])
                ->columns(2),

            // === SEÇÃO 12: CAMPOS PERSONALIZADOS ===
            ...CustomFieldService::getCustomFieldsForModel('process'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.name')
                    ->label('Escritório')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lawyer.name')
                    ->label('Advogado')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'secondary' => 'archived',
                        'primary' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'archived' => 'Arquivado',
                        'completed' => 'Concluído',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Data de Início')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Prazo')
                    ->date()
                    ->sortable(),
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
                Tables\Actions\Action::make('export_single')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        $exporter = new \App\Services\ExcelExportService();
                        $filepath = $exporter->exportProcesses(collect([$record]));

                        return response()->download($filepath, basename($filepath))->deleteFileAfterSend();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_excel')
                        ->label('Exportar para Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $exporter = new \App\Services\ExcelExportService();
                            $filepath = $exporter->exportProcesses($records);

                            return response()->download($filepath, basename($filepath))->deleteFileAfterSend();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Exportar Processos Selecionados')
                        ->modalDescription('Será gerado um arquivo Excel compatível com o template UNIMED contendo todos os dados dos processos selecionados.'),
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
            'index' => Pages\ListProcesses::route('/'),
            'create' => Pages\CreateProcess::route('/create'),
            'edit' => Pages\EditProcess::route('/{record}/edit'),
        ];
    }
}
