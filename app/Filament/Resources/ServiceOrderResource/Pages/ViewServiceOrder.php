<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Resources\ServiceOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceOrder extends ViewRecord
{
    protected static string $resource = ServiceOrderResource::class;

    protected static ?string $title = 'Visualizar Ordem de Serviço';

    protected function getHeaderActions(): array
    {
        return [
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
            Actions\EditAction::make()
                ->label('Editar'),
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->successNotificationTitle('Ordem de serviço excluída com sucesso!'),
        ];
    }
}