<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessParty extends Model
{
    protected $fillable = [
        'process_id',
        'parent_id',
        'api_id',
        'api_pessoa_id',
        'nome',
        'login',
        'tipo',
        'documento',
        'tipo_documento',
        'endereco',
        'polo',
        'situacao',
        'papeis',
        'utiliza_login_senha',
    ];

    protected $casts = [
        'endereco' => 'array',
        'papeis' => 'array',
        'utiliza_login_senha' => 'boolean',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProcessParty::class, 'parent_id');
    }

    public function representantes(): HasMany
    {
        return $this->hasMany(ProcessParty::class, 'parent_id');
    }

    /**
     * Get the party type label
     */
    public function getPoloLabel(): string
    {
        return match ($this->polo) {
            'ATIVO' => 'Polo Ativo',
            'PASSIVO' => 'Polo Passivo',
            'TERCEIROS' => 'Terceiros Interessados',
            default => $this->polo,
        };
    }

    /**
     * Get the party type label
     */
    public function getTipoLabel(): string
    {
        return match ($this->tipo) {
            'RECLAMANTE' => 'Reclamante',
            'RECLAMADO' => 'Reclamado',
            'TERCEIRO INTERESSADO' => 'Terceiro Interessado',
            'ADVOGADO' => 'Advogado',
            default => $this->tipo,
        };
    }

    /**
     * Check if is an attorney
     */
    public function isAdvogado(): bool
    {
        return $this->tipo === 'ADVOGADO';
    }

    /**
     * Get formatted address
     */
    public function getEnderecoCompleto(): ?string
    {
        if (!$this->endereco) {
            return null;
        }

        $parts = [];

        if (isset($this->endereco['logradouro'])) {
            $parts[] = $this->endereco['logradouro'];
        }

        if (isset($this->endereco['numero'])) {
            $parts[] = $this->endereco['numero'];
        }

        if (isset($this->endereco['complemento'])) {
            $parts[] = $this->endereco['complemento'];
        }

        if (isset($this->endereco['bairro'])) {
            $parts[] = $this->endereco['bairro'];
        }

        if (isset($this->endereco['municipio']) && isset($this->endereco['estado'])) {
            $parts[] = $this->endereco['municipio'] . '/' . $this->endereco['estado'];
        }

        if (isset($this->endereco['cep'])) {
            $parts[] = 'CEP: ' . $this->endereco['cep'];
        }

        return implode(', ', array_filter($parts));
    }
}