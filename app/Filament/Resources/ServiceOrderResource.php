<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceOrderResource\Pages;
use App\Models\Lawyer;
use App\Models\Process;
use App\Models\ServiceOrder;
use App\Services\CustomFieldService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceOrderResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Ordens de Serviço';

    protected static ?string $navigationGroup = 'Processos';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_service_orders') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_service_orders') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermissionTo('edit_service_orders') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermissionTo('delete_service_orders') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Service Order Tabs')
                ->tabs([
                    // === ABA 1: INFORMAÇÕES BÁSICAS ===
                    Forms\Components\Tabs\Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\Section::make('Dados da Ordem de Serviço')
                                ->schema([
                                    Forms\Components\Select::make('process_id')
                                        ->label('Processo')
                                        ->relationship('process', 'title')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(fn (callable $set) => $set('lawyer_id', null)),
                                    Forms\Components\Select::make('lawyer_id')
                                        ->label('Advogado Criador')
                                        ->options(function (callable $get) {
                                            $processId = $get('process_id');
                                            if (! $processId) {
                                                return [];
                                            }
                                            $process = Process::find($processId);
                                            if (! $process) {
                                                return [];
                                            }

                                            return Lawyer::where('office_id', $process->office_id)->pluck('name', 'id');
                                        })
                                        ->searchable(),
                                    Forms\Components\TextInput::make('number')
                                        ->label('Número da OS')
                                        ->required()
                                        ->unique(ignoreRecord: true)
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
                                ])
                                ->columns(3),

                            Forms\Components\Section::make('Gerenciador')
                                ->schema([
                                    Forms\Components\Select::make('team')
                                        ->label('Equipe')
                                        ->options([
                                            'juridico' => 'Jurídico',
                                            'contabil' => 'Contábil',
                                            'pericial' => 'Pericial',
                                            'administrativo' => 'Administrativo',
                                        ])
                                        ->searchable(),
                                    Forms\Components\CheckboxList::make('diligences')
                                        ->label('Diligências')
                                        ->options([
                                            'citacao' => 'Citação',
                                            'intimacao' => 'Intimação',
                                            'audiencia' => 'Audiência',
                                            'pericia' => 'Perícia',
                                            'calculo' => 'Cálculo',
                                            'outra' => 'Outra',
                                        ])
                                        ->columns(3),
                                    Forms\Components\CheckboxList::make('purposes')
                                        ->label('Finalidades')
                                        ->options([
                                            'contestacao' => 'Contestação',
                                            'defesa' => 'Defesa',
                                            'recurso' => 'Recurso',
                                            'execucao' => 'Execução',
                                            'acordo' => 'Acordo',
                                            'outra' => 'Outra',
                                        ])
                                        ->columns(3),
                                ])
                                ->columns(1)
                                ->collapsible(),

                            Forms\Components\Section::make('Prioridade e Status')
                                ->schema([
                                    Forms\Components\Select::make('priority')
                                        ->label('Prioridade')
                                        ->options([
                                            'low' => 'Baixa',
                                            'medium' => 'Média',
                                            'high' => 'Alta',
                                            'urgent' => 'Urgente',
                                        ])
                                        ->default('medium')
                                        ->required(),
                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'pending' => 'Pendente',
                                            'in_progress' => 'Em Andamento',
                                            'review' => 'Em Revisão',
                                            'completed' => 'Concluída',
                                            'rejected' => 'Rejeitada',
                                        ])
                                        ->default('pending')
                                        ->required(),
                                    Forms\Components\DatePicker::make('due_date')
                                        ->label('Data de Vencimento'),
                                ])
                                ->columns(3),
                        ]),

                    // === ABA 2: WORKFLOW E RESPONSABILIDADES ===
                    Forms\Components\Tabs\Tab::make('Workflow e Responsabilidades')
                        ->schema([
                            Forms\Components\Section::make('Controle de Workflow')
                                ->schema([
                                    Forms\Components\Select::make('current_responsible_id')
                                        ->label('Responsável Atual')
                                        ->relationship('currentResponsible', 'name')
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('workflow_stage')
                                        ->label('Etapa do Workflow')
                                        ->options([
                                            'created' => 'Criada',
                                            'assigned' => 'Atribuída',
                                            'in_progress' => 'Em Andamento',
                                            'review' => 'Em Revisão',
                                            'completed' => 'Concluída',
                                            'rejected' => 'Rejeitada',
                                        ])
                                        ->default('created'),
                                    Forms\Components\Textarea::make('current_notes')
                                        ->label('Observações Atuais')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Controle de Tempo')
                                ->schema([
                                    Forms\Components\TextInput::make('estimated_hours')
                                        ->label('Horas Estimadas')
                                        ->numeric()
                                        ->step(0.01),
                                    Forms\Components\TextInput::make('actual_hours')
                                        ->label('Horas Realizadas')
                                        ->numeric()
                                        ->step(0.01),
                                    Forms\Components\DateTimePicker::make('started_at')
                                        ->label('Iniciado em'),
                                    Forms\Components\DateTimePicker::make('completed_at')
                                        ->label('Concluído em'),
                                ])
                                ->columns(2),
                        ]),

                    // === ABA 3: CÁLCULO PJE ===
                    Forms\Components\Tabs\Tab::make('Cálculo PJE')
                        ->schema([
                            Forms\Components\Section::make('Horas Extras')
                                ->schema([
                                    Forms\Components\TextInput::make('special_interval_operators')
                                        ->label('Operadores de Intervalo Especial')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_int_intrajornada_384')
                                        ->label('HE Int. Intrajornada 384')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_excedent_6_daily')
                                        ->label('HE Excedente 6h Diárias')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_excedent_8_daily')
                                        ->label('HE Excedente 8h Diárias')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_int_entrejornadas_66')
                                        ->label('HE Int. Entrejornadas 66')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_in_itinere')
                                        ->label('HE In Itinere')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_int_intrajornada_71')
                                        ->label('HE Int. Intrajornada 71')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_time_bank')
                                        ->label('HE Banco de Horas')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_standby')
                                        ->label('HE Sobreaviso')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('he_sundays_holidays')
                                        ->label('HE Domingos e Feriados')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                ])
                                ->columns(3)
                                ->collapsible(),

                            Forms\Components\Section::make('Adicionais')
                                ->schema([
                                    Forms\Components\TextInput::make('night_shift_bonus')
                                        ->label('Adicional Noturno')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('unhealthiness_bonus')
                                        ->label('Adicional Insalubridade')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('danger_bonus')
                                        ->label('Adicional Periculosidade')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('salary_plus')
                                        ->label('Salary Plus')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('productivity_bonus')
                                        ->label('Prêmio Produtividade')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('sales_bonus')
                                        ->label('Prêmio Vendas')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                ])
                                ->columns(3)
                                ->collapsible(),

                            Forms\Components\Section::make('Diferenças Salariais')
                                ->schema([
                                    Forms\Components\TextInput::make('salary_diff_equalization')
                                        ->label('Diferenças Salariais Equiparação')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('salary_diff_accumulated_function')
                                        ->label('Diferenças Função Acumulada')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('function_gratification')
                                        ->label('Gratificação de Função')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                ])
                                ->columns(3)
                                ->collapsible(),

                            Forms\Components\Section::make('Verbas Rescisórias')
                                ->schema([
                                    Forms\Components\TextInput::make('thirteenth_salary')
                                        ->label('13º Salário')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('prior_notice')
                                        ->label('Aviso Prévio')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('double_vacation_one_third')
                                        ->label('Férias Dobradas + 1/3')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('proportional_vacation_one_third')
                                        ->label('Férias Proporcionais + 1/3')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('accrued_vacation_one_third')
                                        ->label('Férias Vencidas + 1/3')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('moral_damages')
                                        ->label('Danos Morais')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                ])
                                ->columns(3)
                                ->collapsible(),

                            Forms\Components\Section::make('Totalizadores')
                                ->schema([
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('fgts_contract_diff')
                                        ->label('Diferenças FGTS Contrato')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('gross_total')
                                        ->label('Total Bruto')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('inss_employee')
                                        ->label('INSS Empregado')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('irrf')
                                        ->label('IRRF')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('net_total')
                                        ->label('Total Líquido')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('attorney_fees_percentage')
                                        ->label('Honorários Advocatícios (%)')
                                        ->numeric()
                                        ->step(0.0001)
                                        ->suffix('%'),
                                    Forms\Components\TextInput::make('attorney_fees_amount')
                                        ->label('Valor Honorários Advocatícios')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                    Forms\Components\TextInput::make('total_due_by_defendant')
                                        ->label('Total Devido pela Ré')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('R$'),
                                ])
                                ->columns(3)
                                ->collapsible(),
                        ]),

                    // === ABA 4: CONTROLE DE CÁLCULO ===
                    Forms\Components\Tabs\Tab::make('Controle de Cálculo')
                        ->schema([
                            Forms\Components\Section::make('Situação e Validação')
                                ->schema([
                                    Forms\Components\Select::make('calculation_situation')
                                        ->label('Situação do Cálculo')
                                        ->options([
                                            'pendente' => 'Pendente',
                                            'em_andamento' => 'Em Andamento',
                                            'concluido' => 'Concluído',
                                            'revisao' => 'Em Revisão',
                                            'aprovado' => 'Aprovado',
                                        ]),
                                    Forms\Components\Select::make('calculation_phase')
                                        ->label('Fase do Cálculo')
                                        ->options([
                                            'inicial' => 'Inicial',
                                            'sentenca' => 'Sentença',
                                            'acordao_trt' => 'Acórdão TRT',
                                            'acordao_tst' => 'Acórdão TST',
                                            'execucao' => 'Execução',
                                            'acordo' => 'Acordo',
                                        ]),
                                    Forms\Components\Toggle::make('validation_passed')
                                        ->label('Validação Aprovada'),
                                    Forms\Components\TextInput::make('errors_in_benefits_count')
                                        ->label('Quantidade de Erros nos Benefícios')
                                        ->numeric(),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Dados do Cálculo')
                                ->schema([
                                    Forms\Components\DateTimePicker::make('calculation_date')
                                        ->label('Data do Cálculo'),
                                    Forms\Components\Select::make('calculated_by')
                                        ->label('Calculado por')
                                        ->relationship('calculatedBy', 'name')
                                        ->searchable(),
                                    Forms\Components\Textarea::make('calculation_notes')
                                        ->label('Observações do Cálculo')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    // === ABA 5: CÁLCULO ANALISADO ===
                    Forms\Components\Tabs\Tab::make('Cálculo Analisado')
                        ->schema([
                            Forms\Components\Section::make('Dados do Cálculo Analisado')
                                ->schema([
                                    Forms\Components\TextInput::make('analyzed_calculation_id_fls')
                                        ->label('ID./Fls.')
                                        ->maxLength(255),
                                    Forms\Components\Select::make('analyzed_index_type')
                                        ->label('Índice Adotado')
                                        ->options([
                                            'ipca' => 'IPCA',
                                            'inpc' => 'INPC',
                                            'igpm' => 'IGP-M',
                                            'selic' => 'SELIC',
                                            'tr' => 'TR',
                                            'outro' => 'Outro',
                                        ])
                                        ->reactive(),
                                    Forms\Components\TextInput::make('analyzed_index_other')
                                        ->label('Índice Diverso')
                                        ->maxLength(255)
                                        ->visible(fn (callable $get) => $get('analyzed_index_type') === 'outro'),
                                    Forms\Components\DatePicker::make('analyzed_date_updated')
                                        ->label('Data Atualizado'),
                                    Forms\Components\TextInput::make('analyzed_value_updated')
                                        ->label('Valor Atualizado')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Pagamentos Efetuados')
                                ->schema([
                                    Forms\Components\Repeater::make('payments_made')
                                        ->label('Pagamentos')
                                        ->schema([
                                            Forms\Components\Select::make('payment_method')
                                                ->label('Meio de Pagamento')
                                                ->options([
                                                    'dinheiro' => 'Dinheiro',
                                                    'cheque' => 'Cheque',
                                                    'transferencia' => 'Transferência',
                                                    'deposito' => 'Depósito',
                                                    'pix' => 'PIX',
                                                    'cartao' => 'Cartão',
                                                    'outro' => 'Outro',
                                                ])
                                                ->required(),
                                            Forms\Components\DatePicker::make('date')
                                                ->label('Data')
                                                ->required(),
                                            Forms\Components\TextInput::make('value')
                                                ->label('Valor')
                                                ->numeric()
                                                ->prefix('R$')
                                                ->step(0.01)
                                                ->required(),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(0)
                                        ->addActionLabel('Adicionar Pagamento'),
                                ])
                                ->collapsible(),
                        ]),

                    // === ABA 6: PRAZOS ===
                    Forms\Components\Tabs\Tab::make('Prazos')
                        ->schema([
                            Forms\Components\Section::make('Prazos Processuais')
                                ->schema([
                                    Forms\Components\DatePicker::make('publication_date')
                                        ->label('Data Publicação')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $days = $get('deadline_days');
                                            if ($state && $days) {
                                                $publicationDate = \Carbon\Carbon::parse($state);
                                                $judicialDeadline = $publicationDate->addWeekdays($days);
                                                $set('judicial_deadline', $judicialDeadline->format('Y-m-d'));
                                            }
                                        }),
                                    Forms\Components\TextInput::make('deadline_days')
                                        ->label('Dias de Prazo (dias úteis)')
                                        ->numeric()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $publicationDate = $get('publication_date');
                                            if ($state && $publicationDate) {
                                                $date = \Carbon\Carbon::parse($publicationDate);
                                                $judicialDeadline = $date->addWeekdays($state);
                                                $set('judicial_deadline', $judicialDeadline->format('Y-m-d'));
                                            }
                                        }),
                                    Forms\Components\DatePicker::make('judicial_deadline')
                                        ->label('Prazo Judicial')
                                        ->disabled()
                                        ->dehydrated(),
                                    Forms\Components\DatePicker::make('internal_deadline')
                                        ->label('Prazo Interno'),
                                ])
                                ->columns(2)
                                ->description('O Prazo Judicial é calculado automaticamente com base na Data de Publicação + Dias de Prazo (considerando apenas dias úteis).'),
                        ]),

                    // === ABA 7: TÉCNICO ===
                    Forms\Components\Tabs\Tab::make('Técnico')
                        ->schema([
                            Forms\Components\Section::make('Informações Técnicas')
                                ->schema([
                                    Forms\Components\Toggle::make('client_is_first_defendant')
                                        ->label('Cliente é 01ª Reclamada'),
                                    Forms\Components\TextInput::make('number_of_substitutes')
                                        ->label('Nº de Substituídos')
                                        ->numeric(),
                                    Forms\Components\Select::make('work_providence')
                                        ->label('Providência do Trabalho')
                                        ->options([
                                            'calculo_liquidacao' => 'Cálculo de Liquidação',
                                            'impugnacao' => 'Impugnação',
                                            'manifestacao' => 'Manifestação',
                                            'recurso' => 'Recurso',
                                            'parecer' => 'Parecer',
                                            'outra' => 'Outra',
                                        ])
                                        ->searchable(),
                                ])
                                ->columns(3),

                            Forms\Components\Section::make('Dados do Cálculo Efetuado')
                                ->schema([
                                    Forms\Components\Select::make('performed_index_type')
                                        ->label('Índice Adotado')
                                        ->options([
                                            'ipca' => 'IPCA',
                                            'inpc' => 'INPC',
                                            'igpm' => 'IGP-M',
                                            'selic' => 'SELIC',
                                            'tr' => 'TR',
                                            'outro' => 'Outro',
                                        ])
                                        ->reactive(),
                                    Forms\Components\TextInput::make('performed_index_other')
                                        ->label('Índice Diverso')
                                        ->maxLength(255)
                                        ->visible(fn (callable $get) => $get('performed_index_type') === 'outro'),
                                    Forms\Components\DatePicker::make('performed_date_updated')
                                        ->label('Data Atualizado'),
                                    Forms\Components\TextInput::make('performed_value_updated')
                                        ->label('Valor Atualizado')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                ])
                                ->columns(2)
                                ->collapsible(),

                            Forms\Components\Section::make('Pagamentos Considerados')
                                ->schema([
                                    Forms\Components\Repeater::make('payments_considered')
                                        ->label('Pagamentos')
                                        ->schema([
                                            Forms\Components\Select::make('payment_method')
                                                ->label('Meio de Pagamento')
                                                ->options([
                                                    'dinheiro' => 'Dinheiro',
                                                    'cheque' => 'Cheque',
                                                    'transferencia' => 'Transferência',
                                                    'deposito' => 'Depósito',
                                                    'pix' => 'PIX',
                                                    'cartao' => 'Cartão',
                                                    'outro' => 'Outro',
                                                ])
                                                ->required(),
                                            Forms\Components\DatePicker::make('date')
                                                ->label('Data')
                                                ->required(),
                                            Forms\Components\TextInput::make('value')
                                                ->label('Valor')
                                                ->numeric()
                                                ->prefix('R$')
                                                ->step(0.01)
                                                ->required(),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(0)
                                        ->addActionLabel('Adicionar Pagamento'),
                                ])
                                ->collapsible(),
                        ]),

                    // === ABA 8: FATURAMENTO ===
                    Forms\Components\Tabs\Tab::make('Faturamento')
                        ->schema([
                            Forms\Components\Section::make('Dados do Contrato')
                                ->schema([
                                    Forms\Components\Select::make('billing_contract_type')
                                        ->label('Tipo de Contrato')
                                        ->options([
                                            'fixo' => 'Fixo',
                                            'variavel' => 'Variável',
                                            'misto' => 'Misto',
                                            'avulso' => 'Avulso',
                                        ]),
                                    Forms\Components\TextInput::make('billing_economic_group')
                                        ->label('Grupo Econômico')
                                        ->maxLength(255),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Dados do Solicitante')
                                ->schema([
                                    Forms\Components\TextInput::make('billing_requester_company_name')
                                        ->label('Razão Social do Solicitante')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('billing_requester_cnpj')
                                        ->label('CNPJ do Solicitante')
                                        ->mask('99.999.999/9999-99')
                                        ->maxLength(18),
                                ])
                                ->columns(2)
                                ->collapsible(),

                            Forms\Components\Section::make('Dados do Emitente')
                                ->schema([
                                    Forms\Components\TextInput::make('billing_issuer_company_name')
                                        ->label('Razão Social do Emitente')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('billing_issuer_cnpj')
                                        ->label('CNPJ do Emitente')
                                        ->mask('99.999.999/9999-99')
                                        ->maxLength(18),
                                ])
                                ->columns(2)
                                ->collapsible(),

                            Forms\Components\Section::make('Nota Fiscal')
                                ->schema([
                                    Forms\Components\TextInput::make('billing_invoice_number')
                                        ->label('Nº Nota Fiscal')
                                        ->maxLength(255),
                                    Forms\Components\DatePicker::make('billing_issue_date')
                                        ->label('Data Emissão'),
                                    Forms\Components\TextInput::make('billing_gross_value')
                                        ->label('Valor Bruto')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                ])
                                ->columns(3),

                            Forms\Components\Section::make('Custos')
                                ->schema([
                                    Forms\Components\TextInput::make('billing_internal_technical_cost')
                                        ->label('Custo Técnico Interno')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                    Forms\Components\TextInput::make('billing_external_technical_cost')
                                        ->label('Custo Técnico Externo')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                    Forms\Components\TextInput::make('billing_other_costs')
                                        ->label('Outros Custos')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                    Forms\Components\TextInput::make('billing_tax')
                                        ->label('Imposto')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                    Forms\Components\TextInput::make('billing_net_total')
                                        ->label('Total Líquido')
                                        ->numeric()
                                        ->prefix('R$')
                                        ->step(0.01),
                                ])
                                ->columns(3)
                                ->collapsible(),

                            Forms\Components\Section::make('Situação')
                                ->schema([
                                    Forms\Components\Select::make('billing_invoice_status')
                                        ->label('Situação da Nota Fiscal')
                                        ->options([
                                            'emitida' => 'Emitida',
                                            'pendente' => 'Pendente',
                                            'cancelada' => 'Cancelada',
                                            'paga' => 'Paga',
                                        ]),
                                    Forms\Components\Select::make('billing_reconciliation_status')
                                        ->label('Situação da Conciliação')
                                        ->options([
                                            'conciliada' => 'Conciliada',
                                            'pendente' => 'Pendente',
                                            'divergente' => 'Divergente',
                                            'nao_aplicavel' => 'Não Aplicável',
                                        ]),
                                ])
                                ->columns(2),
                        ]),

                    // === ABA 9: CAMPOS PERSONALIZADOS ===
                    Forms\Components\Tabs\Tab::make('Campos Personalizados')
                        ->schema(CustomFieldService::getCustomFieldsForModel('service_order')),
                ])
                ->columnSpanFull(),
        ]);
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
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('process.title')
                    ->label('Processo')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('currentResponsible.name')
                    ->label('Responsável Atual')
                    ->searchable()
                    ->sortable()
                    ->default('Não atribuído')
                    ->color(fn ($state) => $state === 'Não atribuído' ? 'warning' : 'primary'),

                Tables\Columns\BadgeColumn::make('workflow_stage')
                    ->label('Estágio')
                    ->colors([
                        'secondary' => 'created',
                        'warning' => 'assigned',
                        'primary' => 'in_progress',
                        'info' => 'review',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Criada',
                        'assigned' => 'Atribuída',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'completed' => 'Concluída',
                        'rejected' => 'Rejeitada',
                        default => $state,
                    }),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Prioridade')
                    ->colors([
                        'secondary' => 'low',
                        'primary' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baixa',
                        'medium' => 'Média',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'primary')
                    ->formatStateUsing(function ($record) {
                        if (! $record->due_date) {
                            return '-';
                        }

                        $daysUntilDue = $record->days_until_due;
                        $dateFormatted = $record->due_date->format('d/m/Y');

                        if ($record->is_overdue) {
                            return $dateFormatted.' (Atrasado)';
                        } elseif ($daysUntilDue !== null && $daysUntilDue <= 3) {
                            return $dateFormatted." ({$daysUntilDue}d)";
                        }

                        return $dateFormatted;
                    }),

                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label('Est./Real')
                    ->formatStateUsing(function ($record) {
                        $estimated = $record->estimated_hours ? number_format($record->estimated_hours, 1).'h' : '-';
                        $actual = $record->actual_hours ? number_format($record->actual_hours, 1).'h' : '-';

                        return "{$estimated} / {$actual}";
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_overdue')
                    ->label('Status')
                    ->icon(fn ($record) => $record->is_overdue ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('workflow_stage')
                    ->label('Estágio')
                    ->options([
                        'created' => 'Criada',
                        'assigned' => 'Atribuída',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'completed' => 'Concluída',
                        'rejected' => 'Rejeitada',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('current_responsible_id')
                    ->label('Responsável Atual')
                    ->relationship('currentResponsible', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridade')
                    ->options([
                        'urgent' => 'Urgente',
                        'high' => 'Alta',
                        'medium' => 'Média',
                        'low' => 'Baixa',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Geral')
                    ->options([
                        'pending' => 'Pendente',
                        'in_progress' => 'Em Andamento',
                        'completed' => 'Concluída',
                        'cancelled' => 'Cancelada',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Atrasadas')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->toggle(),

                Tables\Filters\Filter::make('due_soon')
                    ->label('Vencem em 3 dias')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '>=', now())
                        ->where('due_date', '<=', now()->addDays(3))
                        ->whereNotIn('workflow_stage', ['completed', 'rejected'])
                    )
                    ->toggle(),

                Tables\Filters\SelectFilter::make('process')
                    ->label('Processo')
                    ->relationship('process', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('assign')
                    ->label('Atribuir')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('responsible_id')
                            ->label('Novo Responsável')
                            ->options(Lawyer::pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('stage')
                            ->label('Novo Estágio')
                            ->options([
                                'assigned' => 'Atribuída',
                                'in_progress' => 'Em Andamento',
                                'review' => 'Em Revisão',
                            ])
                            ->default('assigned'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->rows(3),
                    ])
                    ->action(function (ServiceOrder $record, array $data): void {
                        $lawyer = Lawyer::find($data['responsible_id']);
                        $record->assignTo($lawyer, $data['stage'], $data['notes']);

                        Notification::make()
                            ->title('Ordem de serviço atribuída com sucesso!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (ServiceOrder $record) => ! in_array($record->workflow_stage, ['completed', 'rejected'])),

                Tables\Actions\Action::make('start')
                    ->label('Iniciar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (ServiceOrder $record): void {
                        $record->markAsStarted();

                        Notification::make()
                            ->title('Ordem de serviço iniciada!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (ServiceOrder $record) => $record->workflow_stage === 'assigned' &&
                        (
                            $record->current_responsible_id === auth()->user()?->lawyer?->id ||
                            auth()->user()?->hasAnyRole(['super-admin', 'admin'])
                        )
                    ),

                Tables\Actions\Action::make('complete')
                    ->label('Concluir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ServiceOrder $record): void {
                        $record->markAsCompleted();

                        Notification::make()
                            ->title('Ordem de serviço concluída!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (ServiceOrder $record) => $record->workflow_stage === 'in_progress' &&
                        (
                            $record->current_responsible_id === auth()->user()?->lawyer?->id ||
                            auth()->user()?->hasAnyRole(['super-admin', 'admin'])
                        )
                    ),

                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('view_history')
                    ->label('Histórico')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalContent(function (ServiceOrder $record) {
                        if (! $record->workflow_history) {
                            return view('filament.modal.no-history');
                        }

                        return view('filament.modal.workflow-history', [
                            'history' => $record->workflow_history,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_assign')
                        ->label('Atribuir em Lote')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('responsible_id')
                                ->label('Responsável')
                                ->options(Lawyer::pluck('name', 'id'))
                                ->required(),
                            Forms\Components\Select::make('stage')
                                ->label('Estágio')
                                ->options([
                                    'assigned' => 'Atribuída',
                                    'in_progress' => 'Em Andamento',
                                ])
                                ->default('assigned'),
                        ])
                        ->action(function (array $data, $records): void {
                            $lawyer = Lawyer::find($data['responsible_id']);
                            foreach ($records as $record) {
                                $record->assignTo($lawyer, $data['stage']);
                            }

                            Notification::make()
                                ->title('Ordens de serviço atribuídas com sucesso!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh a cada 30 segundos
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
            'index' => Pages\ListServiceOrders::route('/'),
            'create' => Pages\CreateServiceOrder::route('/create'),
            'edit' => Pages\EditServiceOrder::route('/{record}/edit'),
        ];
    }
}