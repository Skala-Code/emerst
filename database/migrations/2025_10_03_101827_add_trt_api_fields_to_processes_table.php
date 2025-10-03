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
            // === CAMPOS DA API TRT ===
            $table->string('trt_number')->nullable()->after('tribunal'); // Número do TRT (ex: "04")
            $table->json('trt_api_data')->nullable(); // JSON completo da resposta da API
            $table->timestamp('trt_api_synced_at')->nullable(); // Data da última sincronização
            $table->json('trt_reclamantes')->nullable(); // Array de reclamantes da API
            $table->json('trt_reclamados')->nullable(); // Array de reclamados da API
            $table->json('trt_outros_interessados')->nullable(); // Array de outros interessados
            $table->integer('trt_api_attempts')->default(0); // Número de tentativas de sincronização
            $table->text('trt_api_error')->nullable(); // Último erro da API (se houver)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn([
                'trt_number',
                'trt_api_data',
                'trt_api_synced_at',
                'trt_reclamantes',
                'trt_reclamados',
                'trt_outros_interessados',
                'trt_api_attempts',
                'trt_api_error',
            ]);
        });
    }
};
