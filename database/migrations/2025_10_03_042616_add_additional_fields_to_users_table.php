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
        Schema::table('users', function (Blueprint $table) {
            $table->string('custom_name')->nullable()->after('name');
            $table->string('cpf')->nullable()->after('custom_name');
            $table->date('admission_date')->nullable()->after('cpf');
            $table->date('termination_date')->nullable()->after('admission_date');
            $table->string('phone')->nullable()->after('termination_date');
            $table->string('contract_status')->nullable()->after('phone');
            $table->string('team')->nullable()->after('contract_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'custom_name',
                'cpf',
                'admission_date',
                'termination_date',
                'phone',
                'contract_status',
                'team',
            ]);
        });
    }
};
