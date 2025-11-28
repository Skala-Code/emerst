<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MicrosoftConnectionWidget extends Widget
{
    protected static string $view = 'filament.widgets.microsoft-connection-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;

    public function getViewData(): array
    {
        $user = Auth::user();
        $isConnected = $user && $user->isMicrosoftConnected();
        
        return [
            'isConnected' => $isConnected,
            'user' => $user,
        ];
    }
}

