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
        Schema::create('process_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');

            // Dados básicos
            $table->string('destinatario');
            $table->string('tipo'); // Mandado, Intimação, Alvará, etc
            $table->string('meio'); // Central de Mandados, Diário Eletrônico, Em Mãos

            // Datas
            $table->date('data_criacao')->nullable();
            $table->date('data_ciencia')->nullable();

            // Controle
            $table->boolean('fechado')->default(false);

            $table->timestamps();

            // Índices
            $table->index('process_id');
            $table->index('tipo');
            $table->index('fechado');
            $table->index('data_criacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_dispatches');
    }
};
