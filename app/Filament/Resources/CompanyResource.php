<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Empresas';

    protected static ?string $navigationGroup = 'Gestão';

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
                Forms\Components\Tabs::make('Dados da Empresa')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Dados Cadastrais')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('custom_name')
                                    ->label('Nome Personalizado')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('legal_name')
                                    ->label('Razão Social')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cnpj')
                                    ->label('CNPJ')
                                    ->mask('99.999.999/9999-99')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Select::make('economic_group')
                                    ->label('Grupo Econômico')
                                    ->options([])
                                    ->searchable()
                                    ->columnSpan(1),
                                Forms\Components\Select::make('contract_status')
                                    ->label('Situação Contratual')
                                    ->options([
                                        'active' => 'Ativo',
                                        'inactive' => 'Inativo',
                                        'pending' => 'Pendente',
                                        'suspended' => 'Suspenso',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('active')
                                    ->label('Ativo')
                                    ->default(true)
                                    ->columnSpan(2),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Endereço')
                            ->schema([
                                Forms\Components\TextInput::make('zip_code')
                                    ->label('CEP')
                                    ->mask('99999-999')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('address')
                                    ->label('Endereço')
                                    ->rows(2)
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('address_number')
                                    ->label('Número')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('complement')
                                    ->label('Complemento')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('state')
                                    ->label('UF')
                                    ->maxLength(2)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('city')
                                    ->label('Cidade')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Responsável Principal')
                            ->schema([
                                Forms\Components\TextInput::make('responsible_name')
                                    ->label('Nome')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_cpf_cnpj')
                                    ->label('CPF/CNPJ')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_phone')
                                    ->label('Telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_email')
                                    ->label('E-mail')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('responsible_position')
                                    ->label('Função/Cargo')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Dados de Faturamento')
                            ->schema([
                                Forms\Components\Select::make('contract_type')
                                    ->label('Tipo de Contrato')
                                    ->options([
                                        'monthly' => 'Mensal',
                                        'hourly' => 'Por Hora',
                                        'project' => 'Por Projeto',
                                        'success_fee' => 'Êxito',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('interested_party')
                                    ->label('Parte Interessada')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TagsInput::make('departments')
                                    ->label('Departamentos (Cliente)')
                                    ->columnSpan(2),
                                Forms\Components\Select::make('sync_internal_system')
                                    ->label('Sincronizar Sistema Interno')
                                    ->options([
                                        'yes' => 'Sim',
                                        'no' => 'Não',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('contract_start_date')
                                    ->label('Vigência - Início')
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('contract_end_date')
                                    ->label('Vigência - Fim')
                                    ->columnSpan(1),
                                Forms\Components\Select::make('readjustment_month')
                                    ->label('Mês de Reajuste')
                                    ->options([
                                        '1' => 'Janeiro',
                                        '2' => 'Fevereiro',
                                        '3' => 'Março',
                                        '4' => 'Abril',
                                        '5' => 'Maio',
                                        '6' => 'Junho',
                                        '7' => 'Julho',
                                        '8' => 'Agosto',
                                        '9' => 'Setembro',
                                        '10' => 'Outubro',
                                        '11' => 'Novembro',
                                        '12' => 'Dezembro',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\Select::make('readjustment_index')
                                    ->label('Índice de Reajuste')
                                    ->options([
                                        'IPCA' => 'IPCA',
                                        'IGPM' => 'IGP-M',
                                        'INPC' => 'INPC',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cutoff_day')
                                    ->label('Dia de Corte')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(31)
                                    ->columnSpan(1),
                                Forms\Components\Select::make('payment_modality')
                                    ->label('Modalidade de Pagamento')
                                    ->options([
                                        'boleto' => 'Boleto',
                                        'transfer' => 'Transferência',
                                        'pix' => 'PIX',
                                    ])
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Dados Técnicos')
                            ->schema([
                                Forms\Components\TextInput::make('company_rate')
                                    ->label('Alíquota - (%) Empresa')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxValue(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('sat_rate')
                                    ->label('Alíquota - (%) SAT')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxValue(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('third_party_rate')
                                    ->label('Alíquota - (%) Terceiro')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxValue(100)
                                    ->columnSpan(1),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
