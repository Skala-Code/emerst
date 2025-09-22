<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function uploadDocument(
        UploadedFile $file,
        ?string $category = null,
        ?string $description = null,
        bool $isPublic = false,
        ?\DateTime $expiresAt = null
    ): Document {
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $fileName, 'private');

        return $this->documents()->create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $category,
            'description' => $description,
            'is_public' => $isPublic,
            'uploaded_by' => auth()->id(),
            'expires_at' => $expiresAt,
        ]);
    }

    public function uploadDocumentVersion(Document $originalDocument, UploadedFile $file): Document
    {
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $fileName, 'private');

        $newVersion = $originalDocument->versions()->max('version') + 1;

        return $this->documents()->create([
            'name' => $originalDocument->name,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $originalDocument->category,
            'description' => $originalDocument->description,
            'is_public' => $originalDocument->is_public,
            'version' => $newVersion,
            'parent_id' => $originalDocument->id,
            'uploaded_by' => auth()->id(),
            'expires_at' => $originalDocument->expires_at,
        ]);
    }

    public function getDocumentsByCategory(?string $category = null)
    {
        $query = $this->documents()->where('is_active', true);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function canBeViewedBy($user): bool
    {
        // Default implementation - can be overridden in each model
        if ($user->hasPermissionTo('view_processes') || $user->hasPermissionTo('view_service_orders')) {
            return true;
        }

        // Check if user is the lawyer responsible
        if (isset($this->lawyer_id) && $this->lawyer_id === $user->id) {
            return true;
        }

        return false;
    }
}
