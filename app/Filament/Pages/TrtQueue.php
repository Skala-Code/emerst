<?php

namespace App\Filament\Pages;

use App\Jobs\ProcessTrtData;
use App\Models\Process;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TrtQueue extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'filament.pages.trt-queue';

    protected static ?string $navigationGroup = 'Processos';

    protected static ?string $navigationLabel = 'Fila API TRT';

    protected static ?string $title = 'Fila de Sincronização TRT';

    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Process::query()
                    ->where('status', 'aguardando_api_trt')
                    ->orWhere(function (Builder $query) {
                        $query->whereNotNull('trt_api_error')
                            ->where('trt_api_attempts', '<', 3);
                    })
                    ->orderBy('created_at', 'asc')
            )
            ->columns([
                TextColumn::make('number')
                    ->label('Número do Processo')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('trt_number')
                    ->label('TRT')
                    ->badge()
                    ->color('info')
                    ->default('N/A'),

                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'aguardando_api_trt' => 'info',
                        'suspended' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('trt_api_attempts')
                    ->label('Tentativas')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'gray',
                        $state < 3 => 'warning',
                        default => 'danger',
                    })
                    ->default(0),

                TextColumn::make('trt_api_error')
                    ->label('Último Erro')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->default('-')
                    ->color('danger'),

                TextColumn::make('trt_api_synced_at')
                    ->label('Última Sincronização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                TableAction::make('process')
                    ->label('Processar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Process $record) {
                        ProcessTrtData::dispatch($record);

                        Notification::make()
                            ->title('Job despachado!')
                            ->body("Processo {$record->number} adicionado à fila de processamento.")
                            ->success()
                            ->send();
                    }),

                TableAction::make('view')
                    ->label('Ver Processo')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Process $record) => route('filament.admin.resources.processes.edit', $record)),
            ])
            ->bulkActions([
                BulkAction::make('process_all')
                    ->label('Processar Selecionados')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            ProcessTrtData::dispatch($record);
                        }

                        Notification::make()
                            ->title('Jobs despachados!')
                            ->body("{$records->count()} processo(s) adicionado(s) à fila de processamento.")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Nenhum processo na fila')
            ->emptyStateDescription('Não há processos aguardando sincronização com a API TRT.')
            ->emptyStateIcon('heroicon-o-queue-list')
            ->poll('30s');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('process_all')
                ->label('Processar Toda a Fila')
                ->icon('heroicon-o-play-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Processar toda a fila?')
                ->modalDescription('Isso irá despachar jobs para todos os processos na fila. Tem certeza?')
                ->action(function () {
                    $processes = Process::where('status', 'aguardando_api_trt')
                        ->whereNull('trt_api_synced_at')
                        ->get();

                    foreach ($processes as $process) {
                        ProcessTrtData::dispatch($process);
                    }

                    Notification::make()
                        ->title('Fila processada!')
                        ->body("{$processes->count()} processo(s) adicionado(s) à fila de processamento.")
                        ->success()
                        ->send();
                }),

            Action::make('refresh')
                ->label('Atualizar')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->dispatch('$refresh');

                    Notification::make()
                        ->title('Tabela atualizada')
                        ->success()
                        ->send();
                }),
        ];
    }
}
