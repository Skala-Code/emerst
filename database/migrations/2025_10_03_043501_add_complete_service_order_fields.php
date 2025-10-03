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
            // === DADOS DA SOLICITAÇÃO ===
            $table->text('email_original')->nullable(); // Email original recebido
            $table->dateTime('request_datetime')->nullable(); // Data/Hora Solicitação
            $table->string('requester_name')->nullable(); // Nome do Solicitante
            $table->string('requester_phone')->nullable(); // Telefone Solicitante
            $table->string('requester_office')->nullable(); // Escritório Solicitante
            $table->string('requester_email')->nullable(); // E-mail do Solicitante
            $table->string('requester_cc_email')->nullable(); // E-mail cópia Solicitante
            $table->text('fixed_cc_emails')->nullable(); // E-mails em cópia fixos
            $table->string('requester_client')->nullable(); // Cliente Solicitante
            $table->boolean('client_is_main_party')->nullable(); // Cliente é Parte Principal
            $table->string('interested_party_request')->nullable(); // Parte Interessada
            $table->date('client_deadline')->nullable(); // Prazo Cliente
            $table->time('requested_time')->nullable(); // Horário Solicitado
            $table->text('requester_observation')->nullable(); // Observação do Solicitante

            // === DISTRIBUIDOR - PRÉ ANÁLISE ===
            $table->text('editable_original_email')->nullable(); // Email editável pela pré análise
            $table->text('pre_analysis_text')->nullable(); // Pré-Análise texto livre

            // === DECISÕES ===
            $table->string('decision_type')->nullable(); // Sentença/Acórdão/etc
            $table->string('decision_summary')->nullable(); // Procedente/Parcial/Negado
            $table->string('decision_id_reference')->nullable(); // Qual ID?
            $table->text('decision_disposition')->nullable(); // Dispositivo da decisão

            // === ATUALIZAÇÃO/JUROS DEFERIDO ===
            $table->boolean('interest_granted')->nullable(); // Sim ou Não
            $table->string('interest_type')->nullable(); // ADC 58 / TR + 1% / IPCA-E
            $table->string('interest_id_reference')->nullable(); // Qual ID?

            // === PRESCRIÇÃO DEFERIDA ===
            $table->boolean('prescription_granted')->nullable(); // Sim ou Não
            $table->string('prescription_type')->nullable(); // Quinquenal/Bienal/Ambas
            $table->string('prescription_id_reference')->nullable(); // Qual ID?

            // === RESPONSABILIDADE CLIENTE ===
            $table->string('client_responsibility_type')->nullable(); // Improcedente/Subsidiária/Solidária
            $table->date('client_responsibility_period_start')->nullable(); // Período De
            $table->date('client_responsibility_period_end')->nullable(); // Período Até
            $table->string('client_responsibility_decision_type')->nullable(); // Sentença/Acórdão
            $table->string('client_responsibility_id_reference')->nullable(); // Qual ID?

            // === TRÂNSITO EM JULGADO ===
            $table->boolean('final_judgment')->nullable(); // Sim ou Não
            $table->date('final_judgment_date')->nullable(); // Qual Data?
            $table->string('final_judgment_id_reference')->nullable(); // Qual ID?

            // === VERBAS DEFERIDAS ===
            $table->json('granted_benefits')->nullable(); // Array de verbas deferidas

            // === DEFENDER CÁLCULO ANTERIOR ===
            $table->boolean('defend_previous_calculation')->nullable(); // Sim ou Não
            $table->date('previous_calculation_date')->nullable(); // Data
            $table->decimal('previous_calculation_value', 15, 2)->nullable(); // Valor
            $table->string('previous_calculation_id_reference')->nullable(); // Qual ID?

            // === NECESSITA DOCUMENTOS ===
            $table->string('requires_documents')->nullable(); // Sim/Não/Analisar
            $table->json('required_document_types')->nullable(); // Tipos de documentos
            $table->string('required_documents_period_start')->nullable(); // mm/aaaa
            $table->string('required_documents_period_end')->nullable(); // mm/aaaa

            // === OBSERVAÇÕES ===
            $table->text('pre_analysis_observation')->nullable(); // Observação Pré-Análise
            $table->text('calculation_observation')->nullable(); // Observação sobre o cálculo
            $table->text('payments_observation')->nullable(); // Observação sobre os pagamentos

            // === TÉCNICO - CONCORDÂNCIA ===
            $table->text('pre_analysis_edited_email')->nullable(); // Email editado pela pré análise
            $table->boolean('agreement_with_adverse_party')->nullable(); // Concordância Sim ou Não
            $table->date('agreement_date')->nullable(); // Data da concordância
            $table->decimal('agreement_value', 15, 2)->nullable(); // Valor da concordância
            $table->string('agreement_id_reference')->nullable(); // Qual ID?

            // === VALORES SEPARADOS - CÁLCULO ANALISADO ===
            $table->decimal('analyzed_gross_value', 15, 2)->nullable(); // Valor Bruto Analisado
            $table->decimal('analyzed_net_value', 15, 2)->nullable(); // Valor Líquido Analisado

            // === VALORES SEPARADOS - CÁLCULO APRESENTADO ===
            $table->decimal('presented_gross_value', 15, 2)->nullable(); // Valor Bruto Apresentado
            $table->decimal('presented_net_value', 15, 2)->nullable(); // Valor Líquido Apresentado
            $table->text('presented_calculation_observation')->nullable(); // Observação trabalho entregue

            // === COMUNICAÇÃO ===
            $table->text('client_email_suggestion')->nullable(); // Sugestão de Email para o cliente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn([
                // Dados da Solicitação
                'email_original',
                'request_datetime',
                'requester_name',
                'requester_phone',
                'requester_office',
                'requester_email',
                'requester_cc_email',
                'fixed_cc_emails',
                'requester_client',
                'client_is_main_party',
                'interested_party_request',
                'client_deadline',
                'requested_time',
                'requester_observation',
                // Distribuidor - Pré Análise
                'editable_original_email',
                'pre_analysis_text',
                // Decisões
                'decision_type',
                'decision_summary',
                'decision_id_reference',
                'decision_disposition',
                // Atualização/Juros
                'interest_granted',
                'interest_type',
                'interest_id_reference',
                // Prescrição
                'prescription_granted',
                'prescription_type',
                'prescription_id_reference',
                // Responsabilidade Cliente
                'client_responsibility_type',
                'client_responsibility_period_start',
                'client_responsibility_period_end',
                'client_responsibility_decision_type',
                'client_responsibility_id_reference',
                // Trânsito em Julgado
                'final_judgment',
                'final_judgment_date',
                'final_judgment_id_reference',
                // Verbas Deferidas
                'granted_benefits',
                // Defender Cálculo Anterior
                'defend_previous_calculation',
                'previous_calculation_date',
                'previous_calculation_value',
                'previous_calculation_id_reference',
                // Necessita Documentos
                'requires_documents',
                'required_document_types',
                'required_documents_period_start',
                'required_documents_period_end',
                // Observações
                'pre_analysis_observation',
                'calculation_observation',
                'payments_observation',
                // Técnico - Concordância
                'pre_analysis_edited_email',
                'agreement_with_adverse_party',
                'agreement_date',
                'agreement_value',
                'agreement_id_reference',
                // Valores Separados
                'analyzed_gross_value',
                'analyzed_net_value',
                'presented_gross_value',
                'presented_net_value',
                'presented_calculation_observation',
                // Comunicação
                'client_email_suggestion',
            ]);
        });
    }
};
