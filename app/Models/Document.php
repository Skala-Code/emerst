<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'name',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'category',
        'description',
        'is_public',
        'version',
        'parent_id',
        'uploaded_by',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'file_size' => 'integer',
        'version' => 'integer',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this->id);
    }

    public function getStoragePathAttribute(): string
    {
        return Storage::path($this->file_path);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeDownloadedBy(User $user): bool
    {
        if ($this->isExpired() || ! $this->is_active) {
            return false;
        }

        if ($this->is_public) {
            return true;
        }

        // Check if user has permission to view documents
        if (! $user->hasPermissionTo('view_documents')) {
            return false;
        }

        // Check if user uploaded the document
        if ($this->uploaded_by === $user->id) {
            return true;
        }

        // Check if user has access to the related model
        $documentable = $this->documentable;
        if ($documentable && method_exists($documentable, 'canBeViewedBy')) {
            return $documentable->canBeViewedBy($user);
        }

        return true;
    }
}
