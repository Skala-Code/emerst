<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($reportData as $index => $data)
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                {{-- Cabeçalho da Ordem de Serviço --}}
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 border-b border-gray-200 dark:border-gray-600 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            OS #{{ $data['service_order']['number'] }} - {{ $data['service_order']['title'] }}
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($data['service_order']['status'] === 'completed') bg-green-100 text-green-800
                            @elseif($data['service_order']['status'] === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($data['service_order']['status'] === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($data['service_order']['status']) }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Criado em: {{ $data['service_order']['created_at']->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    {{-- Dados do Processo --}}
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Dados do Processo
                        </h4>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número do Processo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $data['process']['number'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['process']['client_name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Escritório</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['process']['office_name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tribunal</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['process']['court'] }}</dd>
                                </div>
                                @if($data['process']['judge'])
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Juiz</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['process']['judge'] }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    {{-- Dados da Liquidação --}}
                    @if($data['liquidation']['numero_calculo'])
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Liquidação
                        </h4>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número do Cálculo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $data['liquidation']['numero_calculo'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $data['liquidation']['data'] ? \Carbon\Carbon::parse($data['liquidation']['data'])->format('d/m/Y') : 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['liquidation']['status'] ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Atualizado em</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $data['liquidation']['updated_at'] ? \Carbon\Carbon::parse($data['liquidation']['updated_at'])->format('d/m/Y H:i') : 'N/A' }}
                                    </dd>
                                </div>
                            </dl>

                            {{-- Valores da Liquidação --}}
                            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Valores</h5>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="bg-white dark:bg-gray-800 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Valor Total</dt>
                                        <dd class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">
                                            R$ {{ number_format($data['liquidation']['valor_total'] ?? 0, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Valor Principal</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                            R$ {{ number_format($data['liquidation']['valor_principal'] ?? 0, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Juros</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                            R$ {{ number_format($data['liquidation']['valor_juros'] ?? 0, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Correção</dt>
                                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                            R$ {{ number_format($data['liquidation']['valor_correcao'] ?? 0, 2, ',', '.') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Itens da Liquidação --}}
                            @if(count($data['liquidation']['itens']) > 0)
                            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Itens</h5>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descrição</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Juros</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Correção</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($data['liquidation']['itens'] as $item)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $item['descricao'] ?? 'N/A' }}</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900 dark:text-white">R$ {{ number_format($item['valor'] ?? 0, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900 dark:text-white">R$ {{ number_format($item['juros'] ?? 0, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900 dark:text-white">R$ {{ number_format($item['correcao'] ?? 0, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 text-sm text-right font-semibold text-gray-900 dark:text-white">R$ {{ number_format($item['total'] ?? 0, 2, ',', '.') }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if(($item['tipo'] ?? '') === 'PRINCIPAL') bg-blue-100 text-blue-800
                                                        @elseif(($item['tipo'] ?? '') === 'REFLEXO') bg-purple-100 text-purple-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ $item['tipo'] ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Relatórios de Cálculo --}}
                    @if(count($data['reports']) > 0)
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Relatórios de Cálculo ({{ count($data['reports']) }})
                        </h4>
                        <div class="space-y-3">
                            @foreach($data['reports'] as $report)
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-4">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nº Cálculo</dt>
                                                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $report['numero_calculo'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipo</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $report['tipo_relatorio'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $report['status'] }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Gerado em</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                    {{ $report['data_geracao'] ? \Carbon\Carbon::parse($report['data_geracao'])->format('d/m/Y H:i') : 'N/A' }}
                                                </dd>
                                            </div>
                                        </div>
                                    </div>
                                    @if($report['url_direta'])
                                    <div class="ml-4">
                                        <a href="{{ $report['url_direta'] }}" target="_blank"
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$loop->last)
            <div class="border-t-4 border-gray-300 dark:border-gray-600 my-8"></div>
            @endif
        @endforeach
    </div>

    @if(count($reportData) === 0)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    Nenhuma ordem de serviço selecionada para gerar o relatório.
                </p>
            </div>
        </div>
    </div>
    @endif

    <style>
        @media print {
            .fi-topbar, .fi-sidebar, .fi-header-actions, nav, footer {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
</x-filament-panels::page>
