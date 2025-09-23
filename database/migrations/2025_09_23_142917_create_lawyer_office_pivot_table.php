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
        Schema::create('lawyer_office', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lawyer_id', 'office_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_office');
    }
};