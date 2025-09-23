<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessTimeline extends Model
{
    protected $fillable = [
        'process_id',
        'event_date',
        'event_type',
        'description',
        'reference_number',
        'responsible_party',
        'order',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date->format('d/m/Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->event_date->format('H:i');
    }
}
