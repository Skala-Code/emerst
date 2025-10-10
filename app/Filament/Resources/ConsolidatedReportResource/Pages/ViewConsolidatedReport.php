<?php

namespace App\Filament\Resources\ConsolidatedReportResource\Pages;

use App\Filament\Resources\ConsolidatedReportResource;
use App\Models\ServiceOrder;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class ViewConsolidatedReport extends Page
{
    protected static string $resource = ConsolidatedReportResource::class;

    protected static string $view = 'filament.pages.consolidated-report';

    protected static ?string $title = 'Relatório Consolidado';

    public Collection $serviceOrders;
    public array $reportData = [];

    public function mount(): void
    {
        $ids = request()->query('ids');

        if (empty($ids)) {
            redirect()->route('filament.admin.resources.consolidated-reports.index');
            return;
        }

        // Converter IDs de string para array
        $idsArray = explode(',', $ids);

        // Buscar ordens de serviço com relacionamentos
        $this->serviceOrders = ServiceOrder::with([
            'process',
            'reports',
        ])->whereIn('id', $idsArray)->get();

        // Preparar dados do relatório
        $this->prepareReportData();
    }

    protected function prepareReportData(): void
    {
        $this->reportData = $this->serviceOrders->map(function ($serviceOrder) {
            $process = $serviceOrder->process;

            // Pegar primeiro reclamante
            $reclamantes = $process->reclamantes ?? [];
            $primeiroReclamante = is_array($reclamantes) && count($reclamantes) > 0
                ? ($reclamantes[0]['nome'] ?? 'N/A')
                : 'N/A';

            // Pegar primeiro reclamado
            $reclamados = $process->reclamados ?? [];
            $primeiroReclamado = is_array($reclamados) && count($reclamados) > 0
                ? ($reclamados[0]['nome'] ?? 'N/A')
                : 'N/A';

            return [
                'service_order' => [
                    'id' => $serviceOrder->id,
                    'number' => $serviceOrder->number,
                    'title' => $serviceOrder->title,
                    'status' => $serviceOrder->workflow_stage,
                    'created_at' => $serviceOrder->created_at,
                ],
                'process' => [
                    'number' => $process->processo ?? 'N/A',
                    'trt' => $process->trt ?? 'N/A',
                    'classe' => $process->classe ?? 'N/A',
                    'orgao_julgador' => $process->orgao_julgador ?? 'N/A',
                    'valor_causa' => $process->valor_causa ?? 'N/A',
                    'reclamante' => $primeiroReclamante,
                    'reclamado' => $primeiroReclamado,
                ],
                'liquidation' => [
                    'numero_calculo' => $serviceOrder->liquidation_numero_calculo,
                    'data' => $serviceOrder->liquidation_data,
                    'status' => $serviceOrder->liquidation_status,
                    'mensagem' => $serviceOrder->liquidation_mensagem,
                    'valor_total' => $serviceOrder->liquidation_valor_total,
                    'valor_principal' => $serviceOrder->liquidation_valor_principal,
                    'valor_juros' => $serviceOrder->liquidation_valor_juros,
                    'valor_correcao' => $serviceOrder->liquidation_valor_correcao,
                    'itens' => $serviceOrder->liquidation_itens ?? [],
                    'alertas' => $serviceOrder->liquidation_alertas ?? [],
                    'updated_at' => $serviceOrder->liquidation_updated_at,
                ],
                'reports' => $serviceOrder->reports->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'numero_calculo' => $report->numero_calculo,
                        'tipo_relatorio' => $report->tipo_relatorio,
                        'status' => $report->status,
                        'data_geracao' => $report->data_geracao,
                        'tamanho_bytes' => $report->tamanho_bytes,
                        'url_direta' => $report->url_direta,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Voltar')
                ->url(route('filament.admin.resources.consolidated-reports.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),

            \Filament\Actions\Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->action('print'),

            \Filament\Actions\Action::make('export_excel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportExcel'),
        ];
    }

    public function print(): void
    {
        $this->js('window.print()');
    }

    public function exportExcel(): void
    {
        // Implementar export para Excel se necessário
        \Filament\Notifications\Notification::make()
            ->title('Funcionalidade em desenvolvimento')
            ->warning()
            ->send();
    }
}
