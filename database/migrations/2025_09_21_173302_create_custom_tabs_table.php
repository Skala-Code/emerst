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
        Schema::create('custom_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // 'process' or 'service_order'
            $table->string('name');
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->json('permissions')->nullable(); // which user types can see this tab
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_tabs');
    }
};
