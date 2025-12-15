<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('service_orders', 'email_id')) {
            Schema::table('service_orders', function (Blueprint $table) {
                $table->foreignId('email_id')->nullable()->after('lawyer_id')->constrained('emails')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_orders', 'email_id')) {
            Schema::table('service_orders', function (Blueprint $table) {
                $table->dropForeign(['email_id']);
                $table->dropColumn('email_id');
            });
        }
    }
};

