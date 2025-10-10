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
        Schema::create('service_order_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->onDelete('cascade');

            // Dados básicos do relatório
            $table->string('numero_calculo');
            $table->string('tipo_relatorio')->default('COMPLETO'); // COMPLETO, RESUMO, DEMONSTRATIVO, etc
            $table->string('formato')->default('JSON'); // HTML, JSON
            $table->string('status')->default('GERADO'); // GERADO, ERRO
            $table->timestamp('data_geracao')->nullable();

            // HTML do relatório
            $table->longText('html_content')->nullable();

            // Dados estruturados (opcional, se vier em JSON)
            $table->json('dados_estruturados')->nullable();

            // Metadados
            $table->integer('tamanho_bytes')->nullable();
            $table->string('url_direta')->nullable();
            $table->text('mensagem_erro')->nullable();

            $table->timestamps();

            // Índices
            $table->index('service_order_id');
            $table->index('numero_calculo');
            $table->index('tipo_relatorio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_reports');
    }
};
