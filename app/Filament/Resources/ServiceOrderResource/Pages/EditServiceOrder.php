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
            Actions\Action::make('view_email')
                ->label('Ver Email')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->visible(fn () => $this->record->email_id)
                ->modalHeading('Email Vinculado')
                ->modalContent(fn () => view('filament.components.email-view', ['email' => $this->record->email]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar'),
            Actions\Action::make('open_pjecalc')
                ->label('Abrir PJeCalc')
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->url(function () {
                    $orderId = $this->record->id;
                    $conversationId = rand(10000, 99999); // Gera um ID de conversação aleatório
                    return "http://calculo.emerst.com.br:9257/pjecalc/pages/calculo/calculo.jsf?conversationId={$conversationId}&ordem_id={$orderId}";
                })
                ->openUrlInNewTab()
                ->tooltip('Abrir calculadora PJeCalc com os dados desta ordem'),
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
