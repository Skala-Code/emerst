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
                'request_datetime',
                'requester_name',
                'requester_phone',
                'requester_office',
                'requester_email',
                'requester_cc_email',
                'fixed_cc_emails',
                'requester_client',
                'client_is_main_party',
                'interested_party_request',
                'client_deadline',
                'requested_time',
                'requester_observation',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dateTime('request_datetime')->nullable();
            $table->string('requester_name')->nullable();
            $table->string('requester_phone')->nullable();
            $table->string('requester_office')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requester_cc_email')->nullable();
            $table->text('fixed_cc_emails')->nullable();
            $table->string('requester_client')->nullable();
            $table->boolean('client_is_main_party')->nullable();
            $table->string('interested_party_request')->nullable();
            $table->date('client_deadline')->nullable();
            $table->time('requested_time')->nullable();
            $table->text('requester_observation')->nullable();
        });
    }
};
