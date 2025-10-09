<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Resources\ServiceOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceOrder extends EditRecord
{
    protected static string $resource = ServiceOrderResource::class;

    protected static ?string $title = 'Editar Ordem de Serviço';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->successNotificationTitle('Ordem de serviço excluída com sucesso!'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Ordem de serviço atualizada com sucesso!';
    }
}
