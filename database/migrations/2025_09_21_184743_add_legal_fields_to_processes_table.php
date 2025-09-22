<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            // Dados do Reclamante/Funcionário
            $table->string('employee_function')->nullable()->after('description');
            $table->string('city')->nullable()->after('employee_function');
            $table->string('state')->nullable()->after('city');
            $table->date('admission_date')->nullable()->after('state');
            $table->date('termination_date')->nullable()->after('admission_date');

            // Controle Processual
            $table->string('folder_number')->nullable()->after('termination_date');
            $table->string('procedural_phase')->nullable()->after('folder_number');
            $table->text('observations')->nullable()->after('procedural_phase');
            $table->decimal('interest_rate', 8, 4)->nullable()->after('observations');
            $table->text('monthly_movements')->nullable()->after('interest_rate');
            $table->string('previous_phase')->nullable()->after('monthly_movements');
            $table->decimal('interest_rate_diff', 8, 4)->nullable()->after('previous_phase');
            $table->string('law_firm')->nullable()->after('interest_rate_diff');

            // Controle de Tempo (TR = Taxa Referencial)
            $table->decimal('termination_to_filing_tr', 8, 4)->nullable()->after('law_firm');
            $table->decimal('filing_to_current_tr', 8, 4)->nullable()->after('termination_to_filing_tr');
            $table->decimal('termination_to_filing_ipca', 8, 4)->nullable()->after('filing_to_current_tr');
            $table->decimal('filing_to_current_ipca', 8, 4)->nullable()->after('termination_to_filing_ipca');
            $table->integer('prescription_months')->nullable()->after('filing_to_current_ipca');

            // Tipo de Caso
            $table->string('case_type')->nullable()->after('prescription_months');
            $table->string('procedure_type')->nullable()->after('case_type'); // Rito

            // Controle de Provisões por Fase
            $table->decimal('initial_provision', 15, 2)->nullable()->after('procedure_type');
            $table->decimal('sentence_provision', 15, 2)->nullable()->after('initial_provision');
            $table->decimal('trt_provision', 15, 2)->nullable()->after('sentence_provision');
            $table->decimal('tst_provision', 15, 2)->nullable()->after('trt_provision');
            $table->decimal('settlement_provision', 15, 2)->nullable()->after('tst_provision');
            $table->decimal('current_provision', 15, 2)->nullable()->after('settlement_provision');

            // Depósitos e Pagamentos
            $table->decimal('appeal_deposits', 15, 2)->nullable()->after('current_provision');
            $table->decimal('judicial_deposits', 15, 2)->nullable()->after('appeal_deposits');
            $table->decimal('releases_payments', 15, 2)->nullable()->after('judicial_deposits');

            // Provisões Atualizadas
            $table->decimal('current_provision_tr', 15, 2)->nullable()->after('releases_payments');
            $table->decimal('previous_month_provision_tr', 15, 2)->nullable()->after('current_provision_tr');
            $table->decimal('current_provision_ipca', 15, 2)->nullable()->after('previous_month_provision_tr');

            // Status de Perda
            $table->string('loss_status_previous')->nullable()->after('current_provision_ipca');
            $table->string('loss_status_current')->nullable()->after('loss_status_previous');

            // Previsões
            $table->date('disbursement_forecast')->nullable()->after('loss_status_current');
            $table->string('probable_status_change')->nullable()->after('disbursement_forecast');
            $table->string('procedural_progress')->nullable()->after('probable_status_change');

            // Decisões
            $table->string('decision_phase')->nullable()->after('procedural_progress');
            $table->string('situation')->nullable()->after('decision_phase');

            // Tipo de Reclamada
            $table->string('defendant_type')->nullable()->after('situation');
        });
    }

    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn([
                'employee_function', 'city', 'state', 'admission_date', 'termination_date',
                'folder_number', 'procedural_phase', 'observations', 'interest_rate',
                'monthly_movements', 'previous_phase', 'interest_rate_diff', 'law_firm',
                'termination_to_filing_tr', 'filing_to_current_tr', 'termination_to_filing_ipca',
                'filing_to_current_ipca', 'prescription_months', 'case_type', 'procedure_type',
                'initial_provision', 'sentence_provision', 'trt_provision', 'tst_provision',
                'settlement_provision', 'current_provision', 'appeal_deposits', 'judicial_deposits',
                'releases_payments', 'current_provision_tr', 'previous_month_provision_tr',
                'current_provision_ipca', 'loss_status_previous', 'loss_status_current',
                'disbursement_forecast', 'probable_status_change', 'procedural_progress',
                'decision_phase', 'situation', 'defendant_type'
            ]);
        });
    }
};