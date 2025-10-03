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
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn([
                'email_original',
                'editable_original_email',
                'pre_analysis_edited_email',
                'client_email_suggestion',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->text('email_original')->nullable();
            $table->text('editable_original_email')->nullable();
            $table->text('pre_analysis_edited_email')->nullable();
            $table->text('client_email_suggestion')->nullable();
        });
    }
};
