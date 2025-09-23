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
        Schema::create('process_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');

            // Data e hora do evento
            $table->datetime('event_date');
            $table->time('event_time')->nullable();

            // Informações do evento
            $table->string('event_type'); // Ex: "Juntada", "Publicado", "Expedido", "Despacho", etc.
            $table->text('description'); // Descrição completa do evento
            $table->string('reference_number')->nullable(); // Número de referência (ex: c35e8fb)
            $table->string('responsible_party')->nullable(); // Pessoa/entidade responsável

            // Ordenação
            $table->integer('order')->default(0);

            $table->timestamps();

            // Índices
            $table->index(['process_id', 'event_date', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_timelines');
    }
};
