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
        Schema::table('offices', function (Blueprint $table) {
            // Campos Cadastrais
            $table->string('custom_name')->nullable()->after('name');
            $table->string('contract_status')->nullable()->after('custom_name');
            $table->string('legal_name')->nullable()->after('contract_status');
            $table->string('cnpj')->unique()->nullable()->after('legal_name');

            // Endereço Completo
            $table->string('zip_code')->nullable()->after('address');
            $table->string('address_number')->nullable()->after('zip_code');
            $table->string('complement')->nullable()->after('address_number');
            $table->string('state')->nullable()->after('complement');
            $table->string('city')->nullable()->after('state');

            // Dados do Responsável Principal
            $table->string('responsible_name')->nullable()->after('city');
            $table->string('responsible_cpf')->nullable()->after('responsible_name');
            $table->string('responsible_phone')->nullable()->after('responsible_cpf');
            $table->string('responsible_email')->nullable()->after('responsible_phone');
            $table->string('responsible_position')->nullable()->after('responsible_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn([
                'custom_name',
                'contract_status',
                'legal_name',
                'cnpj',
                'zip_code',
                'address_number',
                'complement',
                'state',
                'city',
                'responsible_name',
                'responsible_cpf',
                'responsible_phone',
                'responsible_email',
                'responsible_position',
            ]);
        });
    }
};
