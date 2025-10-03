<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessSubject extends Model
{
    protected $fillable = [
        'process_id',
        'api_id',
        'codigo',
        'descricao',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
