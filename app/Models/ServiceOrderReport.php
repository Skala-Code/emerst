<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderReport extends Model
{
    protected $fillable = [
        'service_order_id',
        'numero_calculo',
        'tipo_relatorio',
        'formato',
        'status',
        'data_geracao',
        'html_content',
        'dados_estruturados',
        'tamanho_bytes',
        'url_direta',
        'mensagem_erro',
    ];

    protected $casts = [
        'data_geracao' => 'datetime',
        'dados_estruturados' => 'array',
        'tamanho_bytes' => 'integer',
    ];

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
