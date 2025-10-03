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
            // Campos básicos do processo judicial
            $table->string('classe')->nullable()->after('number'); // Classe processual (ex: ATSum)
            $table->string('orgao_julgador')->nullable(); // Órgão julgador
            $table->boolean('segredo_justica')->default(false); // Se corre em segredo de justiça
            $table->boolean('justica_gratuita')->default(false); // Se tem justiça gratuita
            $table->datetime('distribuido_em')->nullable(); // Data/hora da distribuição
            $table->datetime('autuado_em')->nullable(); // Data/hora da autuação
            $table->decimal('valor_da_causa', 15, 2)->nullable(); // Valor econômico da causa
            $table->boolean('juizo_digital')->default(false); // Se tramita em juízo digital
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn([
                'classe',
                'orgao_julgador',
                'segredo_justica',
                'justica_gratuita',
                'distribuido_em',
                'autuado_em',
                'valor_da_causa',
                'juizo_digital',
            ]);
        });
    }
};
