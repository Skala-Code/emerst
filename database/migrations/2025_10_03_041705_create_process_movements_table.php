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
        Schema::create('process_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('process_movements')->onDelete('cascade'); // Para anexos

            // Dados da API
            $table->unsignedBigInteger('api_id')->nullable();
            $table->string('id_unico_documento')->nullable();

            // Dados básicos
            $table->string('titulo');
            $table->string('tipo')->nullable(); // Certidão, Despacho, Sentença, etc
            $table->string('tipo_conteudo')->nullable(); // HTML, PDF
            $table->datetime('data')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('documento_sigiloso')->default(false);
            $table->boolean('usuario_perito')->default(false);
            $table->boolean('documento')->default(false);
            $table->boolean('publico')->default(false);
            $table->boolean('mostrar_header_data')->default(false);

            // Informações de usuário
            $table->string('polo_usuario')->nullable(); // A, P
            $table->string('usuario_juntada')->nullable();
            $table->unsignedBigInteger('usuario_criador')->nullable();

            // Controle
            $table->string('instancia')->nullable(); // 1, 2

            $table->timestamps();

            // Índices
            $table->index('process_id');
            $table->index('parent_id');
            $table->index('tipo');
            $table->index('data');
            $table->index('documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_movements');
    }
};
