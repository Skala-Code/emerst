<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessDispatch extends Model
{
    protected $fillable = [
        'process_id',
        'destinatario',
        'tipo',
        'meio',
        'data_criacao',
        'data_ciencia',
        'fechado',
    ];

    protected $casts = [
        'data_criacao' => 'date',
        'data_ciencia' => 'date',
        'fechado' => 'boolean',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * Check if pending (not closed)
     */
    public function isPending(): bool
    {
        return !$this->fechado;
    }

    /**
     * Check if was acknowledged
     */
    public function wasAcknowledged(): bool
    {
        return $this->data_ciencia !== null;
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        if ($this->fechado) {
            return 'success';
        }

        if ($this->data_ciencia) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        if ($this->fechado) {
            return 'Fechado';
        }

        if ($this->data_ciencia) {
            return 'Ciente';
        }

        return 'Pendente';
    }
}
