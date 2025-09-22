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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->foreignId('lawyer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'suspended', 'archived', 'completed'])->default('active');
            $table->date('start_date');
            $table->date('deadline')->nullable();
            $table->json('custom_data')->nullable(); // stores custom field values
            $table->timestamps();
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
