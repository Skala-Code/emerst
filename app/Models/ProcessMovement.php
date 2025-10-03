<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessMovement extends Model
{
    protected $fillable = [
        'process_id',
        'parent_id',
        'api_id',
        'id_unico_documento',
        'titulo',
        'tipo',
        'tipo_conteudo',
        'data',
        'ativo',
        'documento_sigiloso',
        'usuario_perito',
        'documento',
        'publico',
        'mostrar_header_data',
        'polo_usuario',
        'usuario_juntada',
        'usuario_criador',
        'instancia',
    ];

    protected $casts = [
        'data' => 'datetime',
        'ativo' => 'boolean',
        'documento_sigiloso' => 'boolean',
        'usuario_perito' => 'boolean',
        'documento' => 'boolean',
        'publico' => 'boolean',
        'mostrar_header_data' => 'boolean',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProcessMovement::class, 'parent_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ProcessMovement::class, 'parent_id');
    }

    /**
     * Get icon based on movement type
     */
    public function getIcon(): string
    {
        return match ($this->tipo) {
            'Sentença', 'Acórdão' => 'heroicon-o-scale',
            'Despacho', 'Decisão' => 'heroicon-o-document-text',
            'Intimação' => 'heroicon-o-bell',
            'Certidão' => 'heroicon-o-document-check',
            'Petição Inicial' => 'heroicon-o-document-plus',
            'Contestação' => 'heroicon-o-shield-check',
            'Manifestação' => 'heroicon-o-chat-bubble-left-right',
            'Recurso Ordinário' => 'heroicon-o-arrow-trending-up',
            'Mandado' => 'heroicon-o-identification',
            'Alvará' => 'heroicon-o-clipboard-document-check',
            default => 'heroicon-o-document',
        };
    }

    /**
     * Get color based on movement type
     */
    public function getColor(): string
    {
        return match ($this->tipo) {
            'Sentença', 'Acórdão' => 'success',
            'Intimação' => 'warning',
            'Despacho', 'Decisão' => 'info',
            'Certidão' => 'gray',
            default => 'primary',
        };
    }
}
