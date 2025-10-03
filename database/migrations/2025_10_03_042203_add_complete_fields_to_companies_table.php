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
        Schema::table('companies', function (Blueprint $table) {
            // Campos Cadastrais
            $table->string('custom_name')->nullable()->after('name');
            $table->string('economic_group')->nullable()->after('custom_name');
            $table->string('contract_status')->nullable()->after('economic_group');
            $table->string('legal_name')->nullable()->after('contract_status');

            // Endereço Completo
            $table->string('zip_code')->nullable()->after('address');
            $table->string('address_number')->nullable()->after('zip_code');
            $table->string('complement')->nullable()->after('address_number');
            $table->string('state')->nullable()->after('complement');
            $table->string('city')->nullable()->after('state');

            // Dados do Responsável Principal
            $table->string('responsible_name')->nullable()->after('city');
            $table->string('responsible_cpf_cnpj')->nullable()->after('responsible_name');
            $table->string('responsible_phone')->nullable()->after('responsible_cpf_cnpj');
            $table->string('responsible_email')->nullable()->after('responsible_phone');
            $table->string('responsible_position')->nullable()->after('responsible_email');

            // Dados Faturamento
            $table->string('contract_type')->nullable()->after('responsible_position');
            $table->string('interested_party')->nullable()->after('contract_type');
            $table->json('departments')->nullable()->after('interested_party');
            $table->string('sync_internal_system')->nullable()->after('departments');
            $table->date('contract_start_date')->nullable()->after('sync_internal_system');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->string('readjustment_month')->nullable()->after('contract_end_date');
            $table->string('readjustment_index')->nullable()->after('readjustment_month');
            $table->integer('cutoff_day')->nullable()->after('readjustment_index');
            $table->string('payment_modality')->nullable()->after('cutoff_day');

            // Tipos de Cálculo (JSON array para múltiplas entradas)
            $table->json('calculation_types')->nullable()->after('payment_modality');

            // Dados Técnicos
            $table->decimal('company_rate', 5, 2)->nullable()->after('calculation_types');
            $table->decimal('sat_rate', 5, 2)->nullable()->after('company_rate');
            $table->decimal('third_party_rate', 5, 2)->nullable()->after('sat_rate');
            $table->json('taxation_data')->nullable()->after('third_party_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'custom_name',
                'economic_group',
                'contract_status',
                'legal_name',
                'zip_code',
                'address_number',
                'complement',
                'state',
                'city',
                'responsible_name',
                'responsible_cpf_cnpj',
                'responsible_phone',
                'responsible_email',
                'responsible_position',
                'contract_type',
                'interested_party',
                'departments',
                'sync_internal_system',
                'contract_start_date',
                'contract_end_date',
                'readjustment_month',
                'readjustment_index',
                'cutoff_day',
                'payment_modality',
                'calculation_types',
                'company_rate',
                'sat_rate',
                'third_party_rate',
                'taxation_data',
            ]);
        });
    }
};
