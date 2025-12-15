<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microsoft_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('microsoft_id')->nullable();
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->foreignId('connected_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('email');
            $table->index('token_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microsoft_accounts');
    }
};

