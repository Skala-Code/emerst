<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Todas as 88 verbas trabalhistas do Excel UNIMED

            // === HORAS EXTRAS ===
            $table->decimal('special_interval_operators', 15, 2)->nullable()->after('current_notes');
            $table->decimal('he_int_intrajornada_384', 15, 2)->nullable();
            $table->decimal('he_excedent_6_daily', 15, 2)->nullable();
            $table->decimal('he_excedent_8_daily', 15, 2)->nullable();
            $table->decimal('he_int_entrejornadas_66', 15, 2)->nullable();
            $table->decimal('he_in_itinere', 15, 2)->nullable();
            $table->decimal('he_int_intrajornada_71', 15, 2)->nullable();
            $table->decimal('he_time_bank', 15, 2)->nullable();
            $table->decimal('he_standby', 15, 2)->nullable();
            $table->decimal('he_sundays_holidays', 15, 2)->nullable();

            // === ADICIONAIS ===
            $table->decimal('night_shift_bonus', 15, 2)->nullable();
            $table->decimal('salary_integration_natura', 15, 2)->nullable();
            $table->decimal('vr_va_integration', 15, 2)->nullable();
            $table->decimal('salary_plus', 15, 2)->nullable();
            $table->decimal('productivity_bonus', 15, 2)->nullable();
            $table->decimal('life_risk_bonus', 15, 2)->nullable();
            $table->decimal('transfer_bonus', 15, 2)->nullable();
            $table->decimal('merit_promotion', 15, 2)->nullable();
            $table->decimal('daycare_allowance', 15, 2)->nullable();
            $table->decimal('sales_bonus', 15, 2)->nullable();
            $table->decimal('commissions_differences', 15, 2)->nullable();
            $table->decimal('unhealthiness_bonus', 15, 2)->nullable();
            $table->decimal('danger_bonus', 15, 2)->nullable();

            // === DIFERENÇAS SALARIAIS ===
            $table->decimal('salary_diff_equalization', 15, 2)->nullable();
            $table->decimal('salary_diff_accumulated_function', 15, 2)->nullable();
            $table->decimal('reframing_functional_deviation', 15, 2)->nullable();
            $table->decimal('time_functional_promotion', 15, 2)->nullable();
            $table->decimal('salary_diff_others', 15, 2)->nullable();
            $table->decimal('salary_diff_substitution', 15, 2)->nullable();
            $table->decimal('function_gratification', 15, 2)->nullable();
            $table->decimal('salary_diff_category_adjustment', 15, 2)->nullable();
            $table->decimal('commission_reversal', 15, 2)->nullable();
            $table->decimal('reinstatement_absence_salaries', 15, 2)->nullable();
            $table->decimal('service_time_bonus', 15, 2)->nullable();

            // === VERBAS RESCISÓRIAS ===
            $table->decimal('thirteenth_salary', 15, 2)->nullable();
            $table->decimal('prior_notice', 15, 2)->nullable();
            $table->decimal('existential_damage', 15, 2)->nullable();
            $table->decimal('moral_damages', 15, 2)->nullable();
            $table->decimal('travel_allowances', 15, 2)->nullable();
            $table->decimal('ir_indemnification', 15, 2)->nullable();

            // === ESTABILIDADES ===
            $table->decimal('work_accident_stability', 15, 2)->nullable();
            $table->decimal('cipa_provisional_stability', 15, 2)->nullable();
            $table->decimal('mallmann_stability', 15, 2)->nullable();
            $table->decimal('personal_cell_use_indemnification', 15, 2)->nullable();
            $table->decimal('pregnant_provisional_stability', 15, 2)->nullable();

            // === FÉRIAS ===
            $table->decimal('double_vacation_one_third', 15, 2)->nullable();
            $table->decimal('proportional_vacation_one_third', 15, 2)->nullable();
            $table->decimal('accrued_vacation_one_third', 15, 2)->nullable();

            // === OUTRAS VERBAS ===
            $table->decimal('expense_reimbursement', 15, 2)->nullable();
            $table->decimal('inss_employment_contract', 15, 2)->nullable();
            $table->decimal('bad_faith_litigation_fine', 15, 2)->nullable();
            $table->decimal('normative_fine', 15, 2)->nullable();
            $table->decimal('art_467_clt_fine', 15, 2)->nullable();
            $table->decimal('art_477_clt_fine', 15, 2)->nullable();
            $table->decimal('profit_sharing', 15, 2)->nullable();
            $table->decimal('life_pension_accrued_installments', 15, 2)->nullable();
            $table->decimal('life_pension_future_installments', 15, 2)->nullable();
            $table->decimal('km_expense_reimbursement', 15, 2)->nullable();
            $table->decimal('inss_indemnification', 15, 2)->nullable();
            $table->decimal('undue_discount_restitution', 15, 2)->nullable();
            $table->decimal('other_benefits', 15, 2)->nullable();
            $table->decimal('vr_va_managers_isonomia', 15, 2)->nullable();
            $table->decimal('salary_balance', 15, 2)->nullable();
            $table->decimal('unemployment_insurance', 15, 2)->nullable();
            $table->decimal('meal_voucher', 15, 2)->nullable();
            $table->decimal('transport_voucher', 15, 2)->nullable();
            $table->decimal('food_voucher', 15, 2)->nullable();

            // === TOTALIZADORES ===
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('fgts_contract_diff', 15, 2)->nullable();
            $table->decimal('fgts_fine_diff', 15, 2)->nullable();
            $table->decimal('interest_until_update', 15, 2)->nullable();
            $table->decimal('gross_total', 15, 2)->nullable();
            $table->decimal('inss_employee', 15, 2)->nullable();
            $table->decimal('irrf', 15, 2)->nullable();
            $table->decimal('net_total', 15, 2)->nullable();
            $table->decimal('inss_employer', 15, 2)->nullable();
            $table->decimal('attorney_fees_percentage', 8, 4)->nullable();
            $table->decimal('attorney_fees_amount', 15, 2)->nullable();
            $table->decimal('total_due_by_defendant', 15, 2)->nullable();

            // === CONTROLES IRRF ===
            $table->decimal('irrf_base', 15, 2)->nullable();
            $table->decimal('irrf_rate', 8, 4)->nullable();
            $table->decimal('irrf_deduction', 15, 2)->nullable();
            $table->decimal('spreadsheet_value', 15, 2)->nullable();

            // === SITUAÇÃO E VALIDAÇÃO ===
            $table->string('calculation_situation')->nullable();
            $table->integer('errors_in_benefits_count')->nullable();
            $table->boolean('validation_passed')->default(false);

            // === CAMPOS DE CONTROLE ===
            $table->string('calculation_phase')->nullable(); // INICIAL, SENTENÇA, ACÓRDÃO TRT, etc.
            $table->text('calculation_notes')->nullable();
            $table->timestamp('calculation_date')->nullable();
            $table->foreignId('calculated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['calculated_by']);
            $table->dropColumn([
                'special_interval_operators', 'he_int_intrajornada_384', 'he_excedent_6_daily',
                'he_excedent_8_daily', 'he_int_entrejornadas_66', 'he_in_itinere',
                'he_int_intrajornada_71', 'he_time_bank', 'he_standby', 'he_sundays_holidays',
                'night_shift_bonus', 'salary_integration_natura', 'vr_va_integration',
                'salary_plus', 'productivity_bonus', 'life_risk_bonus', 'transfer_bonus',
                'merit_promotion', 'daycare_allowance', 'sales_bonus', 'commissions_differences',
                'unhealthiness_bonus', 'danger_bonus', 'salary_diff_equalization',
                'salary_diff_accumulated_function', 'reframing_functional_deviation',
                'time_functional_promotion', 'salary_diff_others', 'salary_diff_substitution',
                'function_gratification', 'salary_diff_category_adjustment', 'commission_reversal',
                'reinstatement_absence_salaries', 'service_time_bonus', 'thirteenth_salary',
                'prior_notice', 'existential_damage', 'moral_damages', 'travel_allowances',
                'ir_indemnification', 'work_accident_stability', 'cipa_provisional_stability',
                'mallmann_stability', 'personal_cell_use_indemnification',
                'pregnant_provisional_stability', 'double_vacation_one_third',
                'proportional_vacation_one_third', 'accrued_vacation_one_third',
                'expense_reimbursement', 'inss_employment_contract', 'bad_faith_litigation_fine',
                'normative_fine', 'art_467_clt_fine', 'art_477_clt_fine', 'profit_sharing',
                'life_pension_accrued_installments', 'life_pension_future_installments',
                'km_expense_reimbursement', 'inss_indemnification', 'undue_discount_restitution',
                'other_benefits', 'vr_va_managers_isonomia', 'salary_balance',
                'unemployment_insurance', 'meal_voucher', 'transport_voucher', 'food_voucher',
                'subtotal', 'fgts_contract_diff', 'fgts_fine_diff', 'interest_until_update',
                'gross_total', 'inss_employee', 'irrf', 'net_total', 'inss_employer',
                'attorney_fees_percentage', 'attorney_fees_amount', 'total_due_by_defendant',
                'irrf_base', 'irrf_rate', 'irrf_deduction', 'spreadsheet_value',
                'calculation_situation', 'errors_in_benefits_count', 'validation_passed',
                'calculation_phase', 'calculation_notes', 'calculation_date', 'calculated_by'
            ]);
        });
    }
};