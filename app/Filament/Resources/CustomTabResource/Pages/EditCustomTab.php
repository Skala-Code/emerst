<?php

namespace App\Filament\Resources\CustomTabResource\Pages;

use App\Filament\Resources\CustomTabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomTab extends EditRecord
{
    protected static string $resource = CustomTabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
