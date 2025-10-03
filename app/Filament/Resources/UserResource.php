<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $navigationGroup = 'Administração';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_users') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Dados do Usuário')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Dados Cadastrais')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('custom_name')
                                    ->label('Nome Personalizado')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cpf')
                                    ->label('CPF')
                                    ->mask('999.999.999-99')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('email')
                                    ->label('Login/E-mail')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('password')
                                    ->label('Senha')
                                    ->password()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Dados Profissionais')
                            ->schema([
                                Forms\Components\DatePicker::make('admission_date')
                                    ->label('Admissão')
                                    ->columnSpan(1),
                                Forms\Components\DatePicker::make('termination_date')
                                    ->label('Demissão')
                                    ->columnSpan(1),
                                Forms\Components\Select::make('contract_status')
                                    ->label('Situação Contratual')
                                    ->options([
                                        'active' => 'Ativo',
                                        'inactive' => 'Inativo',
                                        'vacation' => 'Férias',
                                        'leave' => 'Afastado',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\Select::make('team')
                                    ->label('Equipe')
                                    ->options([
                                        'juridico' => 'Jurídico',
                                        'administrativo' => 'Administrativo',
                                        'ti' => 'TI',
                                        'financeiro' => 'Financeiro',
                                    ])
                                    ->columnSpan(1),
                                Forms\Components\Select::make('roles')
                                    ->label('Perfis Autorizados')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->columnSpan(2),
                            ])
                            ->columns(2),
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
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin' => 'danger',
                        'admin' => 'warning',
                        'advogado' => 'success',
                        'colaborador' => 'info',
                        'cliente' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->dateTime()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
