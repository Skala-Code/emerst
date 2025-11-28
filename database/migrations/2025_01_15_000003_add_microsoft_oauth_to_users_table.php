<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('microsoft_token')->nullable()->after('remember_token');
            $table->text('microsoft_refresh_token')->nullable()->after('microsoft_token');
            $table->timestamp('microsoft_token_expires_at')->nullable()->after('microsoft_refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'microsoft_token',
                'microsoft_refresh_token',
                'microsoft_token_expires_at',
            ]);
        });
    }
};

