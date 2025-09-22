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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // 'process' or 'service_order'
            $table->string('name');
            $table->string('label');
            $table->string('type'); // text, textarea, select, date, number, checkbox, etc.
            $table->json('options')->nullable(); // for select fields, validation rules, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('required')->default(false);
            $table->boolean('active')->default(true);
            $table->foreignId('custom_tab_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
