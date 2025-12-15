<?php

namespace App\Filament\Widgets;

use App\Models\MicrosoftAccount;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MicrosoftConnectionWidget extends Widget
{
    protected static string $view = 'filament.widgets.microsoft-connection-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;

    public function getViewData(): array
    {
        $connectedAccounts = MicrosoftAccount::whereNotNull('token')->get();
        $isConnected = $connectedAccounts->count() > 0;
        
        return [
            'isConnected' => $isConnected,
            'connectedAccounts' => $connectedAccounts,
            'accountsCount' => $connectedAccounts->count(),
        ];
    }
}

