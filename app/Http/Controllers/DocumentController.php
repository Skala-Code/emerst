<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function download(Document $document): Response|StreamedResponse
    {
        $user = auth()->user();

        if (! $user) {
            abort(401, 'Não autorizado');
        }

        if (! $document->canBeDownloadedBy($user)) {
            abort(403, 'Acesso negado ao documento');
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type,
                'Content-Length' => $document->file_size,
            ]
        );
    }

    public function view(Document $document): Response|StreamedResponse
    {
        $user = auth()->user();

        if (! $user) {
            abort(401, 'Não autorizado');
        }

        if (! $document->canBeDownloadedBy($user)) {
            abort(403, 'Acesso negado ao documento');
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'Arquivo não encontrado');
        }

        // Only allow viewing of certain file types
        $viewableTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain',
        ];

        if (! in_array($document->mime_type, $viewableTypes)) {
            return $this->download($document);
        }

        return response()->file(
            Storage::disk('private')->path($document->file_path),
            [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="'.$document->original_name.'"',
            ]
        );
    }
}
