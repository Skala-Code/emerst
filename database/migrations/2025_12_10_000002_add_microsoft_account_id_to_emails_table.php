<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make user_id nullable first (drop foreign key constraint temporarily)
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('emails', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
        
        // Re-add foreign key constraint
        Schema::table('emails', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add microsoft_account_id
        Schema::table('emails', function (Blueprint $table) {
            $table->foreignId('microsoft_account_id')->nullable()->after('user_id')->constrained('microsoft_accounts')->onDelete('cascade');
            $table->index('microsoft_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['microsoft_account_id']);
            $table->dropColumn('microsoft_account_id');
        });

        // Restore user_id to not nullable
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('emails', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
        
        Schema::table('emails', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};

