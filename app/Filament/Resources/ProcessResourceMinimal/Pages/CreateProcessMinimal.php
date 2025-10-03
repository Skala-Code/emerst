<?php

namespace App\Filament\Resources\ProcessResourceMinimal\Pages;

use App\Filament\Resources\ProcessResourceMinimal;
use Filament\Resources\Pages\CreateRecord;

class CreateProcessMinimal extends CreateRecord
{
    protected static string $resource = ProcessResourceMinimal::class;

    protected static ?string $title = 'Criar Processo (Debug)';
}
