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
        Schema::create('process_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');

            // Dados da API
            $table->unsignedBigInteger('api_id')->nullable(); // ID na API
            $table->string('codigo')->nullable(); // Código do assunto
            $table->string('descricao'); // Descrição do assunto
            $table->boolean('principal')->default(false); // Se é o assunto principal

            $table->timestamps();

            // Índices
            $table->index('process_id');
            $table->index('principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_subjects');
    }
};
