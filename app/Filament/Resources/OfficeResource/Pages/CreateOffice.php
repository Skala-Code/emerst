<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    protected static ?string $title = 'Criar Escritório';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se houver empresas selecionadas, usa a primeira como company_id principal
        // Isso é necessário porque a tabela offices requer company_id (NOT NULL)
        // enquanto o relacionamento many-to-many é gerenciado separadamente
        if (isset($data['companies']) && is_array($data['companies']) && !empty($data['companies'])) {
            $data['company_id'] = $data['companies'][0];
        }

        return $data;
    }
}
