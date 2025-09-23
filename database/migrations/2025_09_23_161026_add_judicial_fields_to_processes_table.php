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
        Schema::table('processes', function (Blueprint $table) {
            // Campos jurídicos essenciais
            $table->string('judiciary_type')->nullable()->after('number'); // Tipo de Justiça
            $table->string('process_nature')->nullable()->after('judiciary_type'); // Natureza do processo
            $table->string('tribunal')->nullable()->after('process_nature'); // Tribunal/Instância
            $table->string('city_district')->nullable()->after('court_state'); // Cidade/Comarca
            $table->date('citation_date')->nullable()->after('filed_at'); // Data da citação
            $table->string('process_format')->nullable()->after('process_class'); // Formato (Físico/Eletrônico)
            $table->string('old_process_number')->nullable()->after('linked_process_number'); // Número antigo

            // Índices para consultas
            $table->index(['judiciary_type', 'tribunal']);
            $table->index('citation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropIndex(['judiciary_type', 'tribunal']);
            $table->dropIndex(['citation_date']);
            $table->dropColumn([
                'judiciary_type',
                'process_nature',
                'tribunal',
                'city_district',
                'citation_date',
                'process_format',
                'old_process_number'
            ]);
        });
    }
};
