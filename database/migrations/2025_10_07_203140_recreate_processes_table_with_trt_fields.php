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
        // Primeiro, desabilita verificação de foreign keys temporariamente
        Schema::disableForeignKeyConstraints();

        // Remove tabelas dependentes primeiro
        Schema::dropIfExists('process_timelines');
        Schema::dropIfExists('process_documents');
        Schema::dropIfExists('process_notes');
        Schema::dropIfExists('process_activities');

        // Remove a tabela existente se houver
        Schema::dropIfExists('processes');

        // Habilita verificação de foreign keys novamente
        Schema::enableForeignKeyConstraints();

        // Cria a nova tabela com os campos exatos da API TRT
        Schema::create('processes', function (Blueprint $table) {
            $table->id();

            // Campos principais do processo
            $table->string('processo')->unique()->index(); // Número do processo (ex: 0020019-44.2020.5.04.0663)
            $table->string('trt', 2)->nullable(); // TRT (ex: "04")
            $table->string('classe')->nullable(); // Classe (ex: "ATOrd")
            $table->string('orgao_julgador')->nullable(); // Órgão julgador
            $table->string('valor_causa')->nullable(); // Valor da causa em string como vem da API
            $table->dateTime('autuado')->nullable(); // Data de autuação
            $table->dateTime('distribuido')->nullable(); // Data de distribuição
            $table->text('assuntos')->nullable(); // Assuntos do processo

            // Campos JSON para armazenar arrays
            $table->json('reclamantes')->nullable(); // Array de reclamantes
            $table->json('reclamados')->nullable(); // Array de reclamados
            $table->json('outros_interessados')->nullable(); // Array de outros interessados

            // Campos adicionais da resposta da API
            $table->json('pdfs')->nullable(); // Array de PDFs se houver
            $table->text('error')->nullable(); // Erro retornado pela API, se houver

            // Campos de controle
            $table->timestamp('ultima_atualizacao_api')->nullable(); // Última vez que foi atualizado via API
            $table->boolean('sincronizado')->default(false); // Se está sincronizado com a API

            $table->timestamps();

            // Índices para melhor performance
            $table->index('trt');
            $table->index('classe');
            $table->index('autuado');
            $table->index('distribuido');
            $table->index('sincronizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};