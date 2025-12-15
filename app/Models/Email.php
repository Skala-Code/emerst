<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    protected $fillable = [
        'user_id',
        'microsoft_account_id',
        'service_order_id',
        'message_id',
        'subject',
        'body_html',
        'body_text',
        'from_email',
        'from_name',
        'to',
        'cc',
        'bcc',
        'attachments',
        'received_at',
        'is_read',
        'is_archived',
        'outlook_id',
        'metadata',
    ];

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'received_at' => 'datetime',
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function microsoftAccount(): BelongsTo
    {
        return $this->belongsTo(MicrosoftAccount::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    public function unarchive(): void
    {
        $this->update(['is_archived' => false]);
    }
}

