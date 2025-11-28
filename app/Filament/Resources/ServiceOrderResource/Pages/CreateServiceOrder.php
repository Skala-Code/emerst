<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Resources\ServiceOrderResource;
use App\Models\Email;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceOrder extends CreateRecord
{
    protected static string $resource = ServiceOrderResource::class;

    protected static ?string $title = 'Criar Ordem de Serviço';
    
    protected static string $view = 'filament.resources.service-order-resource.pages.create-service-order';

    public ?Email $linkedEmail = null;

    public function mount(): void
    {
        parent::mount();

        // Get email_id and process_id from query params
        $emailId = request()->query('email_id');
        $processId = request()->query('process_id');

        if ($emailId) {
            $this->linkedEmail = Email::find($emailId);
            
            // Pre-fill form with email data
            if ($this->linkedEmail) {
                $this->form->fill([
                    'email_id' => $emailId,
                    'process_id' => $processId,
                    'title' => $this->linkedEmail->subject,
                    'description' => strip_tags($this->linkedEmail->body_html ?? $this->linkedEmail->body_text ?? ''),
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ordem de serviço criada com sucesso!';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure email_id is set if coming from inbox
        if ($this->linkedEmail && !isset($data['email_id'])) {
            $data['email_id'] = $this->linkedEmail->id;
        }

        return $data;
    }
}
