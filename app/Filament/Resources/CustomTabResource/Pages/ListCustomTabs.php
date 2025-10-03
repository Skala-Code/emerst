<?php

namespace App\Filament\Resources\CustomTabResource\Pages;

use App\Filament\Resources\CustomTabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomTabs extends ListRecords
{
    protected static string $resource = CustomTabResource::class;

    protected static ?string $title = 'Abas Personalizadas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Aba Personalizada'),
        ];
    }
}
