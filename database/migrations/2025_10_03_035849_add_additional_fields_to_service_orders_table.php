<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // === GERENCIADOR ===
            $table->string('team')->nullable()->after('process_id'); // Equipe
            $table->json('diligences')->nullable(); // Diligências (múltiplas)
            $table->json('purposes')->nullable(); // Finalidades (múltiplas)

            // === DADOS DO CÁLCULO ANALISADO ===
            $table->string('analyzed_calculation_id_fls')->nullable(); // ID./Fls.
            $table->string('analyzed_index_type')->nullable(); // Índice Adotado
            $table->string('analyzed_index_other')->nullable(); // Índice Diverso
            $table->date('analyzed_date_updated')->nullable(); // Data Atualizado
            $table->decimal('analyzed_value_updated', 15, 2)->nullable(); // Valor Atualizado

            // === PAGAMENTOS EFETUADOS (Array JSON) ===
            $table->json('payments_made')->nullable(); // [{payment_method, date, value}]

            // === PRAZOS ===
            $table->date('publication_date')->nullable(); // Data Publicação
            $table->integer('deadline_days')->nullable(); // Dias de Prazo (dias úteis)
            $table->date('judicial_deadline')->nullable(); // Prazo Judicial (calculado)
            $table->date('internal_deadline')->nullable(); // Prazo Interno

            // === TÉCNICO ===
            $table->boolean('client_is_first_defendant')->nullable(); // Cliente é 01ª Reclamada
            $table->integer('number_of_substitutes')->nullable(); // Nº de Substituídos
            $table->string('work_providence')->nullable(); // Providência do Trabalho

            // === DADOS DO CÁLCULO EFETUADO ===
            $table->string('performed_index_type')->nullable(); // Índice Adotado
            $table->string('performed_index_other')->nullable(); // Índice Diverso
            $table->date('performed_date_updated')->nullable(); // Data Atualizado
            $table->decimal('performed_value_updated', 15, 2)->nullable(); // Valor Atualizado

            // === PAGAMENTOS CONSIDERADOS (Array JSON) ===
            $table->json('payments_considered')->nullable(); // [{payment_method, date, value}]

            // === FATURAMENTO ===
            $table->string('billing_contract_type')->nullable(); // Tipo de Contrato
            $table->string('billing_economic_group')->nullable(); // Grupo Econômico
            $table->string('billing_requester_company_name')->nullable(); // Razão Social do Solicitante
            $table->string('billing_requester_cnpj')->nullable(); // CNPJ do Solicitante
            $table->string('billing_issuer_company_name')->nullable(); // Razão Social do Emitente
            $table->string('billing_issuer_cnpj')->nullable(); // CNPJ do Emitente
            $table->string('billing_invoice_number')->nullable(); // Nº Nota Fiscal
            $table->date('billing_issue_date')->nullable(); // Data Emissão
            $table->decimal('billing_gross_value', 15, 2)->nullable(); // Valor Bruto
            $table->decimal('billing_internal_technical_cost', 15, 2)->nullable(); // Custo Técnico Interno
            $table->decimal('billing_external_technical_cost', 15, 2)->nullable(); // Custo Técnico Externo
            $table->decimal('billing_other_costs', 15, 2)->nullable(); // Outros Custos
            $table->decimal('billing_tax', 15, 2)->nullable(); // Imposto
            $table->decimal('billing_net_total', 15, 2)->nullable(); // Total Líquido
            $table->string('billing_invoice_status')->nullable(); // Situação do Nota Fiscal
            $table->string('billing_reconciliation_status')->nullable(); // Situação da Conciliação
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn([
                // Gerenciador
                'team',
                'diligences',
                'purposes',
                // Dados do Cálculo Analisado
                'analyzed_calculation_id_fls',
                'analyzed_index_type',
                'analyzed_index_other',
                'analyzed_date_updated',
                'analyzed_value_updated',
                // Pagamentos Efetuados
                'payments_made',
                // Prazos
                'publication_date',
                'deadline_days',
                'judicial_deadline',
                'internal_deadline',
                // Técnico
                'client_is_first_defendant',
                'number_of_substitutes',
                'work_providence',
                // Dados do Cálculo Efetuado
                'performed_index_type',
                'performed_index_other',
                'performed_date_updated',
                'performed_value_updated',
                // Pagamentos Considerados
                'payments_considered',
                // Faturamento
                'billing_contract_type',
                'billing_economic_group',
                'billing_requester_company_name',
                'billing_requester_cnpj',
                'billing_issuer_company_name',
                'billing_issuer_cnpj',
                'billing_invoice_number',
                'billing_issue_date',
                'billing_gross_value',
                'billing_internal_technical_cost',
                'billing_external_technical_cost',
                'billing_other_costs',
                'billing_tax',
                'billing_net_total',
                'billing_invoice_status',
                'billing_reconciliation_status',
            ]);
        });
    }
};
