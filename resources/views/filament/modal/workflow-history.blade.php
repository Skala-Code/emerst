<div class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        Histórico de Workflow
    </h3>

    <div class="space-y-3">
        @foreach($history as $entry)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            @if($entry['action'] === 'assigned')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    Atribuição
                                </span>
                            @elseif($entry['action'] === 'started')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    Iniciado
                                </span>
                            @elseif($entry['action'] === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Concluído
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    {{ ucfirst($entry['action']) }}
                                </span>
                            @endif

                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($entry['timestamp'])->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <div class="text-sm text-gray-900 dark:text-white">
                            @if($entry['action'] === 'assigned')
                                <strong>{{ $entry['to_responsible_name'] ?? 'Responsável' }}</strong> foi atribuído(a)
                                @if(isset($entry['from_responsible_name']) && $entry['from_responsible_name'])
                                    (anteriormente: <em>{{ $entry['from_responsible_name'] }}</em>)
                                @endif

                                @if(isset($entry['to_stage']) && $entry['to_stage'])
                                    <br><span class="text-gray-600 dark:text-gray-300">
                                        Estágio: <strong>{{
                                            match($entry['to_stage']) {
                                                'created' => 'Criada',
                                                'assigned' => 'Atribuída',
                                                'in_progress' => 'Em Andamento',
                                                'review' => 'Em Revisão',
                                                'completed' => 'Concluída',
                                                'rejected' => 'Rejeitada',
                                                default => $entry['to_stage']
                                            }
                                        }}</strong>
                                    </span>
                                @endif
                            @elseif($entry['action'] === 'started')
                                <strong>{{ $entry['responsible_name'] ?? 'Responsável' }}</strong> iniciou o trabalho
                            @elseif($entry['action'] === 'completed')
                                <strong>{{ $entry['responsible_name'] ?? 'Responsável' }}</strong> concluiu a ordem de serviço
                            @endif
                        </div>

                        @if(isset($entry['notes']) && $entry['notes'])
                            <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-700 dark:text-gray-300">
                                <strong>Observações:</strong> {{ $entry['notes'] }}
                            </div>
                        @endif

                        @if(isset($entry['user_name']) && $entry['user_name'])
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Registrado por: {{ $entry['user_name'] }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>