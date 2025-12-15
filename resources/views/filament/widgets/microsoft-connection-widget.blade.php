@php
    $isConnected = $this->getViewData()['isConnected'];
    $connectedAccounts = $this->getViewData()['connectedAccounts'];
    $accountsCount = $this->getViewData()['accountsCount'];
@endphp

<div class="fi-wi-stats-overview-stat relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10">
                <x-heroicon-o-envelope class="h-6 w-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Microsoft Outlook
                </h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    @if($isConnected)
                        <span class="text-success-600 dark:text-success-400">
                            {{ $accountsCount }} {{ $accountsCount === 1 ? 'Conta Conectada' : 'Contas Conectadas' }}
                        </span>
                    @else
                        <span class="text-gray-400">Desconectado</span>
                    @endif
                </p>
                @if($isConnected && $accountsCount > 0)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @foreach($connectedAccounts as $account)
                            {{ $account->email }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endif
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($isConnected)
                <a 
                    href="{{ route('filament.admin.pages.inbox') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    <x-heroicon-o-inbox class="h-4 w-4" />
                    Caixa de Entrada
                </a>
            @else
                <a 
                    href="{{ route('microsoft.redirect') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
                >
                    <x-heroicon-o-link class="h-4 w-4" />
                    Conectar Email
                </a>
            @endif
        </div>
    </div>
</div>

