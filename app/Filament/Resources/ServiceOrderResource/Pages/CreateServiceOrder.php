<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Resources\ServiceOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceOrder extends CreateRecord
{
    protected static string $resource = ServiceOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Criar Ordem de Serviço';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ordem de serviço criada com sucesso!';
    }
}
