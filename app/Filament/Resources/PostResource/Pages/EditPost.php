<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Concerns\HasPreview;
use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditPost extends EditRecord
{
    use HasPreview, HasPreviewModal;

    /**
     * The resource model.
     */
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Editar Postagem';

    /**
     * The preview modal URL.
     */
    protected function getPreviewModalUrl(): ?string
    {
        $this->generatePreviewSession();

        return route('post.show', [
            'post' => $this->record->slug,
            'previewToken' => $this->previewToken,
        ]);
    }

    /**
     * The header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            PreviewAction::make()
                ->label('Visualizar'),

            Actions\Action::make('view')
                ->label('Ver postagem')
                ->url(fn ($record) => $record->url)
                ->extraAttributes(['target' => '_blank']),

            Actions\DeleteAction::make()
                ->label('Excluir'),
        ];
    }
}
