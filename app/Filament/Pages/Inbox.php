<?php

namespace App\Filament\Pages;

use App\Models\Email;
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
use Filament\Tables\Table;
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
        if (!auth()->user()->isMicrosoftConnected()) {
            Notification::make()
                ->title('Conecte sua conta Microsoft')
                ->body('Você precisa conectar sua conta Microsoft para acessar os emails.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('connect')
                ->label('Conectar Microsoft')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->visible(fn () => !auth()->user()->isMicrosoftConnected())
                ->url(route('microsoft.redirect')),
            
            Action::make('sync')
                ->label('Sincronizar Emails')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->visible(fn () => auth()->user()->isMicrosoftConnected())
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $service = new MicrosoftGraphService(auth()->user());
                        $synced = $service->syncEmails(50);
                        
                        Notification::make()
                            ->title('Emails sincronizados')
                            ->body("{$synced} emails foram sincronizados com sucesso.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erro ao sincronizar')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Action::make('disconnect')
                ->label('Desconectar')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn () => auth()->user()->isMicrosoftConnected())
                ->requiresConfirmation()
                ->action(function () {
                    auth()->user()->update([
                        'microsoft_token' => null,
                        'microsoft_refresh_token' => null,
                        'microsoft_token_expires_at' => null,
                    ]);
                    
                    Notification::make()
                        ->title('Conta desconectada')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Email::query()->where('user_id', auth()->id())->where('is_archived', false))
            ->columns([
                TextColumn::make('from_name')
                    ->label('De')
                    ->searchable()
                    ->sortable(),
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
                //
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

