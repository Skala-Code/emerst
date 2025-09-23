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

            // Tipo da parte no processo
            $table->enum('party_type', ['active', 'passive', 'interested']); // Polo ativo, passivo, outros interessados

            // Tipo de pessoa
            $table->enum('person_type', ['individual', 'legal']); // Pessoa física ou jurídica

            // Dados da parte
            $table->string('name'); // Nome
            $table->string('document')->nullable(); // CPF/CNPJ
            $table->string('registration_number')->nullable(); // RG/Inscrição Estadual
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Advogado representante
            $table->string('lawyer_name')->nullable(); // Nome do advogado
            $table->string('lawyer_oab')->nullable(); // OAB do advogado

            // Classificação específica (reclamante, reclamado, perito, etc.)
            $table->string('role')->nullable(); // Ex: "reclamante", "reclamado", "perito"

            $table->timestamps();
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