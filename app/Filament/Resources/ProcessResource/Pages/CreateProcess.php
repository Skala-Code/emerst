<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use App\Filament\Resources\ProcessResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateProcess extends CreateRecord
{
    protected static string $resource = ProcessResource::class;

    protected static ?string $title = 'Criar Processo';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            Log::info('Creating process with data:', $data);
            return $data;
        } catch (\Exception $e) {
            Log::error('Error in mutateFormDataBeforeCreate: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            Log::info('Handling record creation');
            return parent::handleRecordCreation($data);
        } catch (\Exception $e) {
            Log::error('Error creating process: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
