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
        Schema::table('process_parties', function (Blueprint $table) {
            // Remove campos de advogado individuais
            $table->dropColumn(['lawyer_name', 'lawyer_oab']);

            // Adicionar campo para múltiplos advogados (JSON)
            $table->json('lawyers')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('process_parties', function (Blueprint $table) {
            // Restaurar campos originais
            $table->string('lawyer_name')->nullable();
            $table->string('lawyer_oab')->nullable();

            // Remover campo JSON
            $table->dropColumn('lawyers');
        });
    }
};