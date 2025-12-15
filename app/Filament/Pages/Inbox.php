<?php

namespace App\Filament\Pages;

use App\Models\Email;
use App\Models\MicrosoftAccount;
use App\Models\Process;
use App\Services\MicrosoftGraphService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Inbox extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static string $view = 'filament.pages.inbox';
    protected static ?string $navigationLabel = 'Caixa de Entrada';
    protected static ?string $navigationGroup = 'Comunicação';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;

    public ?Email $selectedEmail = null;

    public function mount(): void
    {
        $connectedAccounts = MicrosoftAccount::whereNotNull('token')->count();
        
        if ($connectedAccounts === 0) {
            Notification::make()
                ->title('Conecte uma conta Microsoft')
                ->body('Você precisa conectar pelo menos uma conta Microsoft para acessar os emails.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        $connectedAccounts = MicrosoftAccount::whereNotNull('token')->get();
        $hasConnectedAccounts = $connectedAccounts->count() > 0;
        
        $actions = [
            Action::make('connect')
                ->label('Conectar Email')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->url(route('microsoft.redirect')),
            
            Action::make('sync')
                ->label('Sincronizar Emails')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->visible($hasConnectedAccounts)
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Emails')
                ->modalDescription('Isso irá sincronizar emails de todas as contas conectadas.')
                ->action(function () use ($connectedAccounts) {
                    $totalSynced = 0;
                    $errors = [];
                    
                    foreach ($connectedAccounts as $account) {
                        try {
                            $service = new MicrosoftGraphService($account);
                            $synced = $service->syncEmails(50);
                            $totalSynced += $synced;
                        } catch (\Exception $e) {
                            $errors[] = "Erro ao sincronizar {$account->email}: " . $e->getMessage();
                        }
                    }
                    
                    if (empty($errors)) {
                        Notification::make()
                            ->title('Emails sincronizados')
                            ->body("{$totalSynced} emails foram sincronizados com sucesso de " . $connectedAccounts->count() . " conta(s).")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Sincronização parcial')
                            ->body("{$totalSynced} emails sincronizados. Erros: " . implode('; ', $errors))
                            ->warning()
                            ->send();
                    }
                }),
        ];
        
        // Add disconnect action for each connected account
        foreach ($connectedAccounts as $account) {
            $actions[] = Action::make('disconnect_' . $account->id)
                ->label('Desconectar ' . $account->email)
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Desconectar Conta')
                ->modalDescription("Tem certeza que deseja desconectar a conta {$account->email}?")
                ->action(function () use ($account) {
                    $account->delete();
                    
                    Notification::make()
                        ->title('Conta desconectada')
                        ->body("A conta {$account->email} foi desconectada com sucesso.")
                        ->success()
                        ->send();
                });
        }
        
        return $actions;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Email::query()->whereNotNull('microsoft_account_id'))
            ->columns([
                TextColumn::make('microsoftAccount.email')
                    ->label('Conta')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('from_name')
                    ->label('De')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Email $record): string {
                        if ($record->is_archived) {
                            return 'arquivado';
                        }
                        if ($record->service_order_id) {
                            return 'cadastrado';
                        }
                        return 'em_aberto';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'em_aberto' => 'info',
                        'cadastrado' => 'success',
                        'arquivado' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'em_aberto' => 'Em Aberto',
                        'cadastrado' => 'Cadastrado',
                        'arquivado' => 'Arquivado',
                        default => $state,
                    }),
                TextColumn::make('subject')
                    ->label('Assunto')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('received_at')
                    ->label('Recebido em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                IconColumn::make('is_read')
                    ->label('Lido')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'em_aberto' => 'Em Aberto',
                        'cadastrado' => 'Cadastrado',
                        'arquivado' => 'Arquivado',
                    ])
                    ->default('em_aberto')
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? 'em_aberto') {
                            'em_aberto' => $query->where('is_archived', false)->whereNull('service_order_id'),
                            'cadastrado' => $query->whereNotNull('service_order_id'),
                            'arquivado' => $query->where('is_archived', true),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                TableAction::make('view')
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->action(function (Email $record) {
                        $this->selectedEmail = $record;
                        $record->markAsRead();
                    }),
                TableAction::make('register')
                    ->label('Registrar')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->form([
                        Select::make('process_id')
                            ->label('Processo')
                            ->options(function () {
                                return Process::all()->pluck('processo', 'id')->mapWithKeys(function ($processo, $id) {
                                    $process = Process::find($id);
                                    return [$id => $process ? $process->display_name : $processo];
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('processo')
                                    ->label('Número do Processo')
                                    ->required()
                                    ->unique('processes', 'processo'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Process::create($data)->id;
                            }),
                    ])
                    ->action(function (Email $record, array $data) {
                        $processId = $data['process_id'] ?? null;
                        
                        if (!$processId) {
                            Notification::make()
                                ->title('Processo é obrigatório')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Redirect to create service order with email and process
                        return redirect()->route('filament.admin.resources.service-orders.create', [
                            'email_id' => $record->id,
                            'process_id' => $processId,
                        ]);
                    }),
                TableAction::make('archive')
                    ->label('Arquivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(function (Email $record) {
                        $record->archive();
                        Notification::make()
                            ->title('Email arquivado')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('received_at', 'desc')
            ->poll('30s');
    }
}

