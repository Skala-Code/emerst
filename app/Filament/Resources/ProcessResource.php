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
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('linked_process_number')
                        ->label('Número do Processo Vinculado')
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('folder_number')
                        ->label('Número da Pasta')
                        ->maxLength(255)
                        ->columnSpan(1),
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

                    // === PARTES DO PROCESSO ===
                    Forms\Components\Repeater::make('parties')
                        ->relationship('parties')
                        ->label('Partes do Processo')
                        ->schema([
                            Forms\Components\Select::make('party_type')
                                ->label('Tipo')
                                ->options([
                                    'active' => 'Polo Ativo (Reclamantes)',
                                    'passive' => 'Polo Passivo (Reclamados)',
                                    'interested' => 'Outros Interessados (Peritos, etc.)'
                                ])
                                ->required()
                                ->reactive()
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('role')
                                ->label('Função')
                                ->placeholder('Ex: reclamante, reclamado, perito')
                                ->maxLength(255)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('name')
                                ->label('Nome/Razão Social')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('document')
                                ->label('CPF/CNPJ')
                                ->maxLength(20)
                                ->columnSpan(1),

                            // Advogados (apenas para polo ativo e passivo)
                            Forms\Components\Repeater::make('lawyers')
                                ->label('Advogados')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nome do Advogado')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('oab')
                                        ->label('OAB')
                                        ->required()
                                        ->maxLength(20)
                                        ->columnSpan(1),
                                ])
                                ->columns(3)
                                ->defaultItems(0)
                                ->addActionLabel('Adicionar Advogado')
                                ->visible(fn (callable $get) => in_array($get('party_type'), ['active', 'passive']))
                                ->columnSpan(3),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Adicionar Parte')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string =>
                            ($state['name'] ?? '') . ' - ' . ($state['role'] ?? 'Sem função')
                        )
                        ->columnSpanFull(),
                ])
                ->columns(3),

            // === SEÇÃO 3: INFORMAÇÕES DO ÓRGÃO JULGADOR ===
            Forms\Components\Section::make('Informações do Órgão Julgador')
                ->schema([
                    Forms\Components\TextInput::make('court_name')
                        ->label('Órgão Julgador')
                        ->maxLength(255)
                        ->placeholder('Ex: 1ª VARA DO TRABALHO DE CANOAS'),
                    Forms\Components\Select::make('court_state')
                        ->label('Estado')
                        ->options([
                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                        ])
                        ->searchable(),
                    Forms\Components\DateTimePicker::make('distributed_at')
                        ->label('Data de Distribuição')
                        ->displayFormat('d/m/Y H:i'),
                    Forms\Components\DateTimePicker::make('filed_at')
                        ->label('Data de Autuação')
                        ->displayFormat('d/m/Y H:i'),
                    Forms\Components\TextInput::make('case_value')
                        ->label('Valor da Causa')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\Toggle::make('free_justice_granted')
                        ->label('Processo com Justiça Gratuita Deferida')
                        ->default(false),
                ])
                ->columns(3),

            // === SEÇÃO 4: CLASSIFICAÇÃO E ASSUNTOS ===
            Forms\Components\Section::make('Classificação e Assuntos')
                ->schema([
                    Forms\Components\Select::make('process_class')
                        ->label('Classe do Processo')
                        ->options([
                            'reclamacao_trabalhista' => 'Reclamação Trabalhista',
                            'acao_cautelar' => 'Ação Cautelar',
                            'acao_rescisoria' => 'Ação Rescisória',
                            'mandado_seguranca' => 'Mandado de Segurança',
                            'habeas_corpus' => 'Habeas Corpus',
                            'embargos_execucao' => 'Embargos à Execução',
                            'execucao' => 'Execução',
                            'outros' => 'Outros'
                        ])
                        ->searchable(),
                    Forms\Components\CheckboxList::make('subjects')
                        ->label('Assuntos do Processo')
                        ->options([
                            'horas_extras' => 'Horas Extras',
                            'adicional_hora_extra' => 'Adicional de Hora Extra',
                            'aviso_previo' => 'Aviso Prévio',
                            'base_calculo' => 'Base de Cálculo',
                            'ctps' => 'CTPS',
                            'honorarios_justica_trabalho' => 'Honorários na Justiça do Trabalho',
                            'indenizacao_dano_material' => 'Indenização por Dano Material',
                            'indenizacao_dano_moral' => 'Indenização por Dano Moral',
                            'intervalo_interjornadas' => 'Intervalo Interjornadas',
                            'intervalo_intrajornada' => 'Intervalo Intrajornada',
                            'repouso_semanal' => 'Repouso Semanal Remunerado e Feriado',
                            'salario_natura' => 'Salário in Natura',
                            'verbas_rescissorias' => 'Verbas Rescisórias',
                            'fgts' => 'FGTS',
                            'pis' => 'PIS',
                            'seguro_desemprego' => 'Seguro Desemprego',
                            'adicional_periculosidade' => 'Adicional de Periculosidade',
                            'adicional_insalubridade' => 'Adicional de Insalubridade',
                            'adicional_noturno' => 'Adicional Noturno',
                            '13_salario' => '13º Salário',
                            'ferias' => 'Férias',
                            'terco_ferias' => '1/3 de Férias',
                            'multa_fgts' => 'Multa FGTS 40%',
                            'diferenca_salarial' => 'Diferença Salarial',
                            'equiparacao_salarial' => 'Equiparação Salarial',
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // === SEÇÃO 5: DADOS DO FUNCIONÁRIO/RECLAMANTE ===
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
            // Temporariamente desabilitado para debug
            // ...CustomFieldService::getCustomFieldsForModel('process'),
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
                Tables\Columns\TextColumn::make('linked_process_number')
                    ->label('Processo Vinculado')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('court_name')
                    ->label('Vara')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
                    ->label('Data Início')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Prazo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('case_value')
                    ->label('Valor da Causa')
                    ->money('BRL')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('free_justice_granted')
                    ->label('Justiça Gratuita')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('office_id')
                    ->label('Escritório')
                    ->relationship('office', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'archived' => 'Arquivado',
                        'completed' => 'Concluído',
                    ]),
                Tables\Filters\Filter::make('has_deadline')
                    ->label('Com Prazo')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('deadline')),
                Tables\Filters\Filter::make('free_justice')
                    ->label('Justiça Gratuita')
                    ->query(fn (Builder $query): Builder => $query->where('free_justice_granted', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
