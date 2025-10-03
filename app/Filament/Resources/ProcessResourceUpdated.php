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

class ProcessResourceUpdated extends Resource
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
        $basicFields = [
            Forms\Components\Section::make('Informações Básicas do Processo')
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
                            if (!$companyId) {
                                return [];
                            }
                            return Office::where('company_id', $companyId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('lawyer_id', null)),
                    Forms\Components\Select::make('lawyer_id')
                        ->label('Advogado Responsável')
                        ->options(function (callable $get) {
                            $officeId = $get('office_id');
                            if (!$officeId) {
                                return [];
                            }
                            return Lawyer::where('office_id', $officeId)->pluck('name', 'id');
                        })
                        ->searchable(),
                    Forms\Components\TextInput::make('number')
                        ->label('Número do Processo')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('title')
                        ->label('Título/Descrição')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Descrição Detalhada')
                        ->rows(3),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Ativo',
                            'suspended' => 'Suspenso',
                            'completed' => 'Concluído',
                            'archived' => 'Arquivado',
                            'aguardando_api_trt' => 'Aguardando API TRT',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Data de Ajuizamento'),
                    Forms\Components\DatePicker::make('deadline')
                        ->label('Prazo Final'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dados do Funcionário/Reclamante')
                ->schema([
                    Forms\Components\TextInput::make('employee_function')
                        ->label('Função'),
                    Forms\Components\TextInput::make('city')
                        ->label('Cidade'),
                    Forms\Components\Select::make('state')
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
                    Forms\Components\DatePicker::make('admission_date')
                        ->label('Data de Admissão'),
                    Forms\Components\DatePicker::make('termination_date')
                        ->label('Data de Demissão'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Controle Processual')
                ->schema([
                    Forms\Components\TextInput::make('folder_number')
                        ->label('Número da Pasta'),
                    Forms\Components\Select::make('procedural_phase')
                        ->label('Fase Processual')
                        ->options([
                            'inicial' => 'Inicial',
                            'contestacao' => 'Contestação',
                            'instrucao' => 'Instrução',
                            'sentenca' => 'Sentença',
                            'recurso_ordinario' => 'Recurso Ordinário',
                            'acordao_trt' => 'Acórdão TRT',
                            'recurso_revista' => 'Recurso de Revista',
                            'acordao_tst' => 'Acórdão TST',
                            'execucao' => 'Execução',
                            'acordo' => 'Acordo',
                            'arquivado' => 'Arquivado',
                        ]),
                    Forms\Components\TextInput::make('law_firm')
                        ->label('Escritório de Advocacia'),
                    Forms\Components\Select::make('case_type')
                        ->label('Tipo de Caso')
                        ->options([
                            'horas_extras' => 'Horas Extras',
                            'adicional_noturno' => 'Adicional Noturno',
                            'diferenca_salarial' => 'Diferença Salarial',
                            'equiparacao_salarial' => 'Equiparação Salarial',
                            'estabilidade' => 'Estabilidade',
                            'indenizacao' => 'Indenização',
                            'rescisao_indireta' => 'Rescisão Indireta',
                            'dano_moral' => 'Dano Moral',
                            'acidente_trabalho' => 'Acidente de Trabalho',
                            'outros' => 'Outros',
                        ]),
                    Forms\Components\Select::make('procedure_type')
                        ->label('Rito')
                        ->options([
                            'ordinario' => 'Ordinário',
                            'sumaríssimo' => 'Sumaríssimo',
                            'sumario' => 'Sumário',
                        ]),
                    Forms\Components\Select::make('defendant_type')
                        ->label('Tipo de Reclamada')
                        ->options([
                            'empresa_privada' => 'Empresa Privada',
                            'empresa_publica' => 'Empresa Pública',
                            'administracao_direta' => 'Administração Direta',
                            'fundacao' => 'Fundação',
                            'ong' => 'ONG',
                        ]),
                ])
                ->columns(2),

            Forms\Components\Section::make('Observações e Acompanhamento')
                ->schema([
                    Forms\Components\Textarea::make('observations')
                        ->label('Observações Gerais')
                        ->rows(3),
                    Forms\Components\Textarea::make('monthly_movements')
                        ->label('Movimentações do Mês')
                        ->rows(3),
                    Forms\Components\TextInput::make('previous_phase')
                        ->label('Fase Anterior'),
                    Forms\Components\Select::make('decision_phase')
                        ->label('Fase de Decisões')
                        ->options([
                            'aguardando_sentenca' => 'Aguardando Sentença',
                            'sentenca_proferida' => 'Sentença Proferida',
                            'recurso_interposto' => 'Recurso Interposto',
                            'acordao_proferido' => 'Acórdão Proferido',
                            'transito_julgado' => 'Trânsito em Julgado',
                            'execucao' => 'Execução',
                        ]),
                    Forms\Components\Select::make('situation')
                        ->label('Situação Atual')
                        ->options([
                            'ativo' => 'Ativo',
                            'suspenso' => 'Suspenso',
                            'arquivado' => 'Arquivado',
                            'acordo' => 'Acordo',
                            'sentenca_favoravel' => 'Sentença Favorável',
                            'sentenca_desfavoravel' => 'Sentença Desfavorável',
                        ]),
                ])
                ->columns(2),
        ];

        // Seção de Controle Financeiro
        $financialFields = [
            Forms\Components\Section::make('Controle de Juros e Atualização')
                ->schema([
                    Forms\Components\TextInput::make('interest_rate')
                        ->label('Taxa de Juros (%)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('interest_rate_diff')
                        ->label('Diferença de Juros (%)')
                        ->numeric()
                        ->step(0.0001)
                        ->suffix('%'),
                    Forms\Components\TextInput::make('termination_to_filing_tr')
                        ->label('Demissão x Ajuizamento (TR)')
                        ->numeric()
                        ->step(0.0001),
                    Forms\Components\TextInput::make('filing_to_current_tr')
                        ->label('Ajuizamento x Atual (TR)')
                        ->numeric()
                        ->step(0.0001),
                    Forms\Components\TextInput::make('termination_to_filing_ipca')
                        ->label('Demissão x Ajuizamento (IPCA-E)')
                        ->numeric()
                        ->step(0.0001),
                    Forms\Components\TextInput::make('filing_to_current_ipca')
                        ->label('Ajuizamento x Atual (IPCA-E)')
                        ->numeric()
                        ->step(0.0001),
                    Forms\Components\TextInput::make('prescription_months')
                        ->label('Meses de Prescrição')
                        ->numeric(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Provisões por Fase Processual')
                ->schema([
                    Forms\Components\TextInput::make('initial_provision')
                        ->label('Provisão - Inicial')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('sentence_provision')
                        ->label('Provisão - Sentença')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('trt_provision')
                        ->label('Provisão - Acórdão TRT')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('tst_provision')
                        ->label('Provisão - Acórdão TST')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('settlement_provision')
                        ->label('Provisão - Acordo')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('current_provision')
                        ->label('Provisionamento Atual')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(2),

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
                        ->label('(-) Alvarás e Pagamentos')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Provisões Atualizadas')
                ->schema([
                    Forms\Components\TextInput::make('current_provision_tr')
                        ->label('Provisão Atual TR')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('previous_month_provision_tr')
                        ->label('Provisão TR - Mês Anterior')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                    Forms\Components\TextInput::make('current_provision_ipca')
                        ->label('Provisão Atual IPCA-E')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('R$'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Status de Perda e Previsões')
                ->schema([
                    Forms\Components\Select::make('loss_status_previous')
                        ->label('Status de Perda Mês Passado')
                        ->options([
                            'provavel' => 'Provável',
                            'possivel' => 'Possível',
                            'remota' => 'Remota',
                        ]),
                    Forms\Components\Select::make('loss_status_current')
                        ->label('Status de Perda Atual')
                        ->options([
                            'provavel' => 'Provável',
                            'possivel' => 'Possível',
                            'remota' => 'Remota',
                        ]),
                    Forms\Components\DatePicker::make('disbursement_forecast')
                        ->label('Previsão de Desembolso'),
                    Forms\Components\TextInput::make('probable_status_change')
                        ->label('Estimativa Alteração Status Provável'),
                    Forms\Components\Textarea::make('procedural_progress')
                        ->label('Andamento Processual')
                        ->rows(3),
                ])
                ->columns(2),
        ];

        $customFields = CustomFieldService::getCustomFieldsForModel('process');

        return $form->schema(array_merge($basicFields, $financialFields, $customFields));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lawyer.name')
                    ->label('Advogado')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'primary' => 'completed',
                        'secondary' => 'archived',
                        'info' => 'aguardando_api_trt',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'completed' => 'Concluído',
                        'archived' => 'Arquivado',
                        'aguardando_api_trt' => 'Aguardando API TRT',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('procedural_phase')
                    ->label('Fase')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'inicial' => 'Inicial',
                        'contestacao' => 'Contestação',
                        'instrucao' => 'Instrução',
                        'sentenca' => 'Sentença',
                        'recurso_ordinario' => 'Recurso Ordinário',
                        'acordao_trt' => 'Acórdão TRT',
                        'recurso_revista' => 'Recurso de Revista',
                        'acordao_tst' => 'Acórdão TST',
                        'execucao' => 'Execução',
                        'acordo' => 'Acordo',
                        'arquivado' => 'Arquivado',
                        default => 'Não definida',
                    }),
                Tables\Columns\TextColumn::make('current_provision')
                    ->label('Provisão Atual')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('loss_status_current')
                    ->label('Status de Perda')
                    ->colors([
                        'danger' => 'provavel',
                        'warning' => 'possivel',
                        'success' => 'remota',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'provavel' => 'Provável',
                        'possivel' => 'Possível',
                        'remota' => 'Remota',
                        default => 'Não definido',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Ajuizamento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Prazo')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'suspended' => 'Suspenso',
                        'completed' => 'Concluído',
                        'archived' => 'Arquivado',
                        'aguardando_api_trt' => 'Aguardando API TRT',
                    ]),
                Tables\Filters\SelectFilter::make('procedural_phase')
                    ->label('Fase Processual')
                    ->options([
                        'inicial' => 'Inicial',
                        'contestacao' => 'Contestação',
                        'instrucao' => 'Instrução',
                        'sentenca' => 'Sentença',
                        'recurso_ordinario' => 'Recurso Ordinário',
                        'acordao_trt' => 'Acórdão TRT',
                        'recurso_revista' => 'Recurso de Revista',
                        'acordao_tst' => 'Acórdão TST',
                        'execucao' => 'Execução',
                        'acordo' => 'Acordo',
                        'arquivado' => 'Arquivado',
                    ]),
                Tables\Filters\SelectFilter::make('loss_status_current')
                    ->label('Status de Perda')
                    ->options([
                        'provavel' => 'Provável',
                        'possivel' => 'Possível',
                        'remota' => 'Remota',
                    ]),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name')
                    ->searchable(),
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
            'index' => Pages\ListProcesses::route('/'),
            'create' => Pages\CreateProcess::route('/create'),
            'edit' => Pages\EditProcess::route('/{record}/edit'),
        ];
    }
}