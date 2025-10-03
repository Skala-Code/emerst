<?php

namespace App\Filament\Resources\ProcessResourceMinimal\Pages;

use App\Filament\Resources\ProcessResourceMinimal;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcessesMinimal extends ListRecords
{
    protected static string $resource = ProcessResourceMinimal::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
