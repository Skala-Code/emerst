<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Fila de Sincronização TRT</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Processos aguardando sincronização com a API externa do TRT. A tabela é atualizada automaticamente a cada 30 segundos.
                    </p>
                </div>
            </div>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
