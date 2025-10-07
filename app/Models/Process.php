<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Process extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'processo',
        'trt',
        'classe',
        'orgao_julgador',
        'valor_causa',
        'autuado',
        'distribuido',
        'assuntos',
        'reclamantes',
        'reclamados',
        'outros_interessados',
        'pdfs',
        'error',
        'ultima_atualizacao_api',
        'sincronizado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'autuado' => 'datetime',
        'distribuido' => 'datetime',
        'ultima_atualizacao_api' => 'datetime',
        'sincronizado' => 'boolean',
        'reclamantes' => 'array',
        'reclamados' => 'array',
        'outros_interessados' => 'array',
        'pdfs' => 'array',
    ];

    /**
     * Obtém o valor da causa formatado como número
     */
    public function getValorCausaNumericoAttribute(): ?float
    {
        if (!$this->valor_causa) {
            return null;
        }

        // Remove "R$", espaços e pontos de milhar, converte vírgula para ponto
        $valor = str_replace(['R$', ' ', '.'], '', $this->valor_causa);
        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }

    /**
     * Obtém lista de nomes dos reclamantes
     */
    public function getNomesReclamantesAttribute(): array
    {
        if (!$this->reclamantes || !is_array($this->reclamantes)) {
            return [];
        }

        return collect($this->reclamantes)->pluck('nome')->toArray();
    }

    /**
     * Obtém lista de nomes dos reclamados
     */
    public function getNomesReclamadosAttribute(): array
    {
        if (!$this->reclamados || !is_array($this->reclamados)) {
            return [];
        }

        return collect($this->reclamados)->pluck('nome')->toArray();
    }

    /**
     * Obtém todos os advogados do processo (reclamantes e reclamados)
     */
    public function getTodosAdvogadosAttribute(): array
    {
        $advogados = [];

        // Advogados dos reclamantes
        if ($this->reclamantes && is_array($this->reclamantes)) {
            foreach ($this->reclamantes as $reclamante) {
                if (isset($reclamante['advogados']) && is_array($reclamante['advogados'])) {
                    $advogados = array_merge($advogados, $reclamante['advogados']);
                }
            }
        }

        // Advogados dos reclamados
        if ($this->reclamados && is_array($this->reclamados)) {
            foreach ($this->reclamados as $reclamado) {
                if (isset($reclamado['advogados']) && is_array($reclamado['advogados'])) {
                    $advogados = array_merge($advogados, $reclamado['advogados']);
                }
            }
        }

        return array_unique($advogados);
    }

    /**
     * Verifica se precisa atualizar dados da API
     */
    public function precisaAtualizacao(): bool
    {
        if (!$this->sincronizado) {
            return true;
        }

        if (!$this->ultima_atualizacao_api) {
            return true;
        }

        // Atualiza se a última atualização foi há mais de 7 dias
        return $this->ultima_atualizacao_api->diffInDays(now()) > 7;
    }

    /**
     * Scope para processos não sincronizados
     */
    public function scopeNaoSincronizados($query)
    {
        return $query->where('sincronizado', false);
    }

    /**
     * Scope para processos que precisam atualização
     */
    public function scopePrecisandoAtualizacao($query)
    {
        return $query->where('sincronizado', false)
            ->orWhere('ultima_atualizacao_api', '<', now()->subDays(7))
            ->orWhereNull('ultima_atualizacao_api');
    }

    /**
     * Scope para buscar por TRT específico
     */
    public function scopeDeTrt($query, string $trt)
    {
        return $query->where('trt', $trt);
    }

    /**
     * Scope para buscar por classe específica
     */
    public function scopeDeClasse($query, string $classe)
    {
        return $query->where('classe', $classe);
    }

    /**
     * Atualiza dados do processo com informações da API
     */
    public function atualizarDaApi(array $dadosApi): void
    {
        $this->fill([
            'trt' => $dadosApi['trt'] ?? null,
            'classe' => $dadosApi['classe'] ?? null,
            'orgao_julgador' => $dadosApi['orgao_julgador'] ?? null,
            'valor_causa' => $dadosApi['valor_causa'] ?? null,
            'autuado' => isset($dadosApi['autuado']) ? Carbon::parse($dadosApi['autuado']) : null,
            'distribuido' => isset($dadosApi['distribuido']) ? Carbon::parse($dadosApi['distribuido']) : null,
            'assuntos' => $dadosApi['assuntos'] ?? null,
            'reclamantes' => $dadosApi['reclamantes'] ?? [],
            'reclamados' => $dadosApi['reclamados'] ?? [],
            'outros_interessados' => $dadosApi['outros_interessados'] ?? [],
            'pdfs' => $dadosApi['pdfs'] ?? [],
            'error' => null,
            'ultima_atualizacao_api' => now(),
            'sincronizado' => true,
        ]);

        $this->save();
    }

    /**
     * Marca o processo como erro ao sincronizar
     */
    public function marcarErroSincronizacao(string $erro): void
    {
        $this->update([
            'error' => $erro,
            'ultima_atualizacao_api' => now(),
            'sincronizado' => false,
        ]);
    }
}