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
        Schema::create('process_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('process_parties')->onDelete('cascade'); // Para representantes

            // Dados da API
            $table->unsignedBigInteger('api_id')->nullable(); // ID na API
            $table->unsignedBigInteger('api_pessoa_id')->nullable(); // ID da pessoa na API

            // Dados básicos
            $table->string('nome');
            $table->string('login')->nullable();
            $table->string('tipo'); // RECLAMANTE, RECLAMADO, TERCEIRO INTERESSADO, ADVOGADO
            $table->string('documento')->nullable();
            $table->string('tipo_documento')->nullable(); // CPF, CPJ

            // Endereço (JSON para flexibilidade)
            $table->json('endereco')->nullable();

            // Classificação
            $table->string('polo'); // ATIVO, PASSIVO, TERCEIROS
            $table->string('situacao')->default('ATIVO'); // ATIVO, INATIVO

            // Papéis (JSON) - pode ter múltiplos papéis
            $table->json('papeis')->nullable();

            // Controle
            $table->boolean('utiliza_login_senha')->default(false);

            $table->timestamps();

            // Índices
            $table->index('process_id');
            $table->index('parent_id');
            $table->index('tipo');
            $table->index('polo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_parties');
    }
};
