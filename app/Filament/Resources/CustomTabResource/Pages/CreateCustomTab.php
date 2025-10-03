<?php

namespace App\Filament\Resources\CustomTabResource\Pages;

use App\Filament\Resources\CustomTabResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomTab extends CreateRecord
{
    protected static string $resource = CustomTabResource::class;

    protected static ?string $title = 'Criar Aba Personalizada';
}
