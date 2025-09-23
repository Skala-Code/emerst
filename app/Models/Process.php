<?php

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Process extends Model
{
    use HasDocuments;

    protected $fillable = [
        'company_id',
        'office_id',
        'lawyer_id',
        'number',
        'linked_process_number',
        'title',
        'description',
        'status',
        'start_date',
        'deadline',
        'custom_data',
        // Dados do Funcionário
        'employee_function',
        'city',
        'state',
        'admission_date',
        'termination_date',
        // Controle Processual
        'folder_number',
        'procedural_phase',
        'observations',
        'interest_rate',
        'monthly_movements',
        'previous_phase',
        'interest_rate_diff',
        'law_firm',
        // Controle de Tempo
        'termination_to_filing_tr',
        'filing_to_current_tr',
        'termination_to_filing_ipca',
        'filing_to_current_ipca',
        'prescription_months',
        // Tipo de Caso
        'case_type',
        'procedure_type',
        // Provisões por Fase
        'initial_provision',
        'sentence_provision',
        'trt_provision',
        'tst_provision',
        'settlement_provision',
        'current_provision',
        // Depósitos e Pagamentos
        'appeal_deposits',
        'judicial_deposits',
        'releases_payments',
        // Provisões Atualizadas
        'current_provision_tr',
        'previous_month_provision_tr',
        'current_provision_ipca',
        // Status de Perda
        'loss_status_previous',
        'loss_status_current',
        // Previsões
        'disbursement_forecast',
        'probable_status_change',
        'procedural_progress',
        // Decisões
        'decision_phase',
        'situation',
        'defendant_type',
        // Campos do Órgão Julgador
        'court_name',
        'court_state',
        'distributed_at',
        'filed_at',
        'case_value',
        'free_justice_granted',
        'subjects',
        'process_class',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'custom_data' => 'array',
        'admission_date' => 'date',
        'termination_date' => 'date',
        'disbursement_forecast' => 'date',
        'interest_rate' => 'decimal:4',
        'interest_rate_diff' => 'decimal:4',
        'termination_to_filing_tr' => 'decimal:4',
        'filing_to_current_tr' => 'decimal:4',
        'termination_to_filing_ipca' => 'decimal:4',
        'filing_to_current_ipca' => 'decimal:4',
        'initial_provision' => 'decimal:2',
        'sentence_provision' => 'decimal:2',
        'trt_provision' => 'decimal:2',
        'tst_provision' => 'decimal:2',
        'settlement_provision' => 'decimal:2',
        'current_provision' => 'decimal:2',
        'appeal_deposits' => 'decimal:2',
        'judicial_deposits' => 'decimal:2',
        'releases_payments' => 'decimal:2',
        'current_provision_tr' => 'decimal:2',
        'previous_month_provision_tr' => 'decimal:2',
        'current_provision_ipca' => 'decimal:2',
        // Novos campos
        'distributed_at' => 'datetime',
        'filed_at' => 'datetime',
        'case_value' => 'decimal:2',
        'free_justice_granted' => 'boolean',
        'subjects' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function parties(): HasMany
    {
        return $this->hasMany(ProcessParty::class);
    }

    public function activeParties(): HasMany
    {
        return $this->hasMany(ProcessParty::class)->where('party_type', 'active');
    }

    public function passiveParties(): HasMany
    {
        return $this->hasMany(ProcessParty::class)->where('party_type', 'passive');
    }

    public function interestedParties(): HasMany
    {
        return $this->hasMany(ProcessParty::class)->where('party_type', 'interested');
    }
}
