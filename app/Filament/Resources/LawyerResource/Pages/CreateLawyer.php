<?php

namespace App\Filament\Resources\LawyerResource\Pages;

use App\Filament\Resources\LawyerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLawyer extends CreateRecord
{
    protected static string $resource = LawyerResource::class;

    protected static ?string $title = 'Criar Advogado';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se houver escritórios selecionados, usa o primeiro como office_id principal
        // Isso é necessário porque a tabela lawyers requer office_id (NOT NULL)
        // enquanto o relacionamento many-to-many é gerenciado separadamente
        if (isset($data['offices']) && is_array($data['offices']) && !empty($data['offices'])) {
            $data['office_id'] = $data['offices'][0];
        }

        return $data;
    }
}
