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
            // Campos principais da liquidação
            $table->string('liquidation_numero_calculo')->nullable()->after('analyzed_calculation_id_fls');
            $table->date('liquidation_data')->nullable();
            $table->string('liquidation_status')->nullable();
            $table->text('liquidation_mensagem')->nullable();

            // Valores totais
            $table->decimal('liquidation_valor_total', 15, 2)->nullable();
            $table->decimal('liquidation_valor_principal', 15, 2)->nullable();
            $table->decimal('liquidation_valor_juros', 15, 2)->nullable();
            $table->decimal('liquidation_valor_correcao', 15, 2)->nullable();

            // Itens da liquidação (JSON)
            $table->json('liquidation_itens')->nullable();

            // Alertas e erros (JSON)
            $table->json('liquidation_alertas')->nullable();
            $table->json('liquidation_erros')->nullable();

            // Timestamp da última atualização
            $table->timestamp('liquidation_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn([
                'liquidation_numero_calculo',
                'liquidation_data',
                'liquidation_status',
                'liquidation_mensagem',
                'liquidation_valor_total',
                'liquidation_valor_principal',
                'liquidation_valor_juros',
                'liquidation_valor_correcao',
                'liquidation_itens',
                'liquidation_alertas',
                'liquidation_erros',
                'liquidation_updated_at',
            ]);
        });
    }
};
