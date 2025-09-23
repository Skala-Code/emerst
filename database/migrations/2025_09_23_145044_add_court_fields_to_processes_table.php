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
            // Campos do Órgão Julgador
            $table->string('court_name')->nullable()->after('custom_data'); // Órgão julgador
            $table->string('court_state')->nullable(); // Estado da vara

            // Datas importantes
            $table->timestamp('distributed_at')->nullable(); // Data de distribuição
            $table->timestamp('filed_at')->nullable(); // Data de autuação
            $table->decimal('case_value', 15, 2)->nullable(); // Valor da causa

            // Processo com justiça gratuita
            $table->boolean('free_justice_granted')->default(false); // Justiça gratuita deferida

            // Assuntos (será JSON para múltiplos assuntos)
            $table->json('subjects')->nullable(); // Assuntos do processo

            // Classe do processo
            $table->string('process_class')->nullable(); // Classe do processo (ação trabalhista, etc.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn([
                'court_name',
                'court_state',
                'distributed_at',
                'filed_at',
                'case_value',
                'free_justice_granted',
                'subjects',
                'process_class'
            ]);
        });
    }
};