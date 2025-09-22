<?php

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrder extends Model
{
    use HasDocuments;

    protected $fillable = [
        'process_id',
        'lawyer_id',
        'current_responsible_id',
        'number',
        'title',
        'description',
        'priority',
        'status',
        'workflow_stage',
        'due_date',
        'started_at',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'current_notes',
        'custom_data',
        'workflow_history',
        // === VERBAS TRABALHISTAS ===
        // Horas Extras
        'special_interval_operators',
        'he_int_intrajornada_384',
        'he_excedent_6_daily',
        'he_excedent_8_daily',
        'he_int_entrejornadas_66',
        'he_in_itinere',
        'he_int_intrajornada_71',
        'he_time_bank',
        'he_standby',
        'he_sundays_holidays',
        // Adicionais
        'night_shift_bonus',
        'salary_integration_natura',
        'vr_va_integration',
        'salary_plus',
        'productivity_bonus',
        'life_risk_bonus',
        'transfer_bonus',
        'merit_promotion',
        'daycare_allowance',
        'sales_bonus',
        'commissions_differences',
        'unhealthiness_bonus',
        'danger_bonus',
        // Diferenças Salariais
        'salary_diff_equalization',
        'salary_diff_accumulated_function',
        'reframing_functional_deviation',
        'time_functional_promotion',
        'salary_diff_others',
        'salary_diff_substitution',
        'function_gratification',
        'salary_diff_category_adjustment',
        'commission_reversal',
        'reinstatement_absence_salaries',
        'service_time_bonus',
        // Verbas Rescisórias
        'thirteenth_salary',
        'prior_notice',
        'existential_damage',
        'moral_damages',
        'travel_allowances',
        'ir_indemnification',
        // Estabilidades
        'work_accident_stability',
        'cipa_provisional_stability',
        'mallmann_stability',
        'personal_cell_use_indemnification',
        'pregnant_provisional_stability',
        // Férias
        'double_vacation_one_third',
        'proportional_vacation_one_third',
        'accrued_vacation_one_third',
        // Outras Verbas
        'expense_reimbursement',
        'inss_employment_contract',
        'bad_faith_litigation_fine',
        'normative_fine',
        'art_467_clt_fine',
        'art_477_clt_fine',
        'profit_sharing',
        'life_pension_accrued_installments',
        'life_pension_future_installments',
        'km_expense_reimbursement',
        'inss_indemnification',
        'undue_discount_restitution',
        'other_benefits',
        'vr_va_managers_isonomia',
        'salary_balance',
        'unemployment_insurance',
        'meal_voucher',
        'transport_voucher',
        'food_voucher',
        // Totalizadores
        'subtotal',
        'fgts_contract_diff',
        'fgts_fine_diff',
        'interest_until_update',
        'gross_total',
        'inss_employee',
        'irrf',
        'net_total',
        'inss_employer',
        'attorney_fees_percentage',
        'attorney_fees_amount',
        'total_due_by_defendant',
        // Controles IRRF
        'irrf_base',
        'irrf_rate',
        'irrf_deduction',
        'spreadsheet_value',
        // Situação e Validação
        'calculation_situation',
        'errors_in_benefits_count',
        'validation_passed',
        // Campos de Controle
        'calculation_phase',
        'calculation_notes',
        'calculation_date',
        'calculated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'custom_data' => 'array',
        'workflow_history' => 'array',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'calculation_date' => 'datetime',
        'validation_passed' => 'boolean',
        // Todos os valores monetários
        'special_interval_operators' => 'decimal:2',
        'he_int_intrajornada_384' => 'decimal:2',
        'he_excedent_6_daily' => 'decimal:2',
        'he_excedent_8_daily' => 'decimal:2',
        'he_int_entrejornadas_66' => 'decimal:2',
        'he_in_itinere' => 'decimal:2',
        'he_int_intrajornada_71' => 'decimal:2',
        'he_time_bank' => 'decimal:2',
        'he_standby' => 'decimal:2',
        'he_sundays_holidays' => 'decimal:2',
        'night_shift_bonus' => 'decimal:2',
        'salary_integration_natura' => 'decimal:2',
        'vr_va_integration' => 'decimal:2',
        'salary_plus' => 'decimal:2',
        'productivity_bonus' => 'decimal:2',
        'life_risk_bonus' => 'decimal:2',
        'transfer_bonus' => 'decimal:2',
        'merit_promotion' => 'decimal:2',
        'daycare_allowance' => 'decimal:2',
        'sales_bonus' => 'decimal:2',
        'commissions_differences' => 'decimal:2',
        'unhealthiness_bonus' => 'decimal:2',
        'danger_bonus' => 'decimal:2',
        'salary_diff_equalization' => 'decimal:2',
        'salary_diff_accumulated_function' => 'decimal:2',
        'reframing_functional_deviation' => 'decimal:2',
        'time_functional_promotion' => 'decimal:2',
        'salary_diff_others' => 'decimal:2',
        'salary_diff_substitution' => 'decimal:2',
        'function_gratification' => 'decimal:2',
        'salary_diff_category_adjustment' => 'decimal:2',
        'commission_reversal' => 'decimal:2',
        'reinstatement_absence_salaries' => 'decimal:2',
        'service_time_bonus' => 'decimal:2',
        'thirteenth_salary' => 'decimal:2',
        'prior_notice' => 'decimal:2',
        'existential_damage' => 'decimal:2',
        'moral_damages' => 'decimal:2',
        'travel_allowances' => 'decimal:2',
        'ir_indemnification' => 'decimal:2',
        'work_accident_stability' => 'decimal:2',
        'cipa_provisional_stability' => 'decimal:2',
        'mallmann_stability' => 'decimal:2',
        'personal_cell_use_indemnification' => 'decimal:2',
        'pregnant_provisional_stability' => 'decimal:2',
        'double_vacation_one_third' => 'decimal:2',
        'proportional_vacation_one_third' => 'decimal:2',
        'accrued_vacation_one_third' => 'decimal:2',
        'expense_reimbursement' => 'decimal:2',
        'inss_employment_contract' => 'decimal:2',
        'bad_faith_litigation_fine' => 'decimal:2',
        'normative_fine' => 'decimal:2',
        'art_467_clt_fine' => 'decimal:2',
        'art_477_clt_fine' => 'decimal:2',
        'profit_sharing' => 'decimal:2',
        'life_pension_accrued_installments' => 'decimal:2',
        'life_pension_future_installments' => 'decimal:2',
        'km_expense_reimbursement' => 'decimal:2',
        'inss_indemnification' => 'decimal:2',
        'undue_discount_restitution' => 'decimal:2',
        'other_benefits' => 'decimal:2',
        'vr_va_managers_isonomia' => 'decimal:2',
        'salary_balance' => 'decimal:2',
        'unemployment_insurance' => 'decimal:2',
        'meal_voucher' => 'decimal:2',
        'transport_voucher' => 'decimal:2',
        'food_voucher' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fgts_contract_diff' => 'decimal:2',
        'fgts_fine_diff' => 'decimal:2',
        'interest_until_update' => 'decimal:2',
        'gross_total' => 'decimal:2',
        'inss_employee' => 'decimal:2',
        'irrf' => 'decimal:2',
        'net_total' => 'decimal:2',
        'inss_employer' => 'decimal:2',
        'attorney_fees_percentage' => 'decimal:4',
        'attorney_fees_amount' => 'decimal:2',
        'total_due_by_defendant' => 'decimal:2',
        'irrf_base' => 'decimal:2',
        'irrf_rate' => 'decimal:4',
        'irrf_deduction' => 'decimal:2',
        'spreadsheet_value' => 'decimal:2',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function currentResponsible(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class, 'current_responsible_id');
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'calculated_by');
    }

    // Métodos úteis para workflow
    public function assignTo(Lawyer $lawyer, ?string $stage = null, ?string $notes = null): void
    {
        $oldResponsible = $this->currentResponsible;
        $oldStage = $this->workflow_stage;

        // Atualizar responsável atual
        $this->current_responsible_id = $lawyer->id;

        if ($stage) {
            $this->workflow_stage = $stage;
        }

        if ($notes) {
            $this->current_notes = $notes;
        }

        // Adicionar ao histórico
        $history = $this->workflow_history ?? [];
        $history[] = [
            'timestamp' => now()->toISOString(),
            'action' => 'assigned',
            'from_responsible_id' => $oldResponsible?->id,
            'from_responsible_name' => $oldResponsible?->name,
            'to_responsible_id' => $lawyer->id,
            'to_responsible_name' => $lawyer->name,
            'from_stage' => $oldStage,
            'to_stage' => $this->workflow_stage,
            'notes' => $notes,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
        ];

        $this->workflow_history = $history;
        $this->save();
    }

    public function markAsStarted(): void
    {
        if (! $this->started_at) {
            $this->started_at = now();
            $this->workflow_stage = 'in_progress';

            $history = $this->workflow_history ?? [];
            $history[] = [
                'timestamp' => now()->toISOString(),
                'action' => 'started',
                'responsible_id' => $this->current_responsible_id,
                'responsible_name' => $this->currentResponsible?->name,
                'stage' => 'in_progress',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name,
            ];

            $this->workflow_history = $history;
            $this->save();
        }
    }

    public function markAsCompleted(): void
    {
        $this->completed_at = now();
        $this->workflow_stage = 'completed';
        $this->status = 'completed';

        $history = $this->workflow_history ?? [];
        $history[] = [
            'timestamp' => now()->toISOString(),
            'action' => 'completed',
            'responsible_id' => $this->current_responsible_id,
            'responsible_name' => $this->currentResponsible?->name,
            'stage' => 'completed',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
        ];

        $this->workflow_history = $history;
        $this->save();
    }

    // Scopes para filtros
    public function scopeByResponsible($query, $lawyerId)
    {
        return $query->where('current_responsible_id', $lawyerId);
    }

    public function scopeByWorkflowStage($query, $stage)
    {
        return $query->where('workflow_stage', $stage);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('workflow_stage', ['completed', 'rejected']);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               ! in_array($this->workflow_stage, ['completed', 'rejected']);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        return $this->due_date ? now()->diffInDays($this->due_date, false) : null;
    }

    public function getWorkflowStageLabelAttribute(): string
    {
        return match ($this->workflow_stage) {
            'created' => 'Criada',
            'assigned' => 'Atribuída',
            'in_progress' => 'Em Andamento',
            'review' => 'Em Revisão',
            'completed' => 'Concluída',
            'rejected' => 'Rejeitada',
            default => 'Desconhecido'
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'Urgente',
            'high' => 'Alta',
            'medium' => 'Média',
            'low' => 'Baixa',
            default => 'Não definida'
        };
    }
}
