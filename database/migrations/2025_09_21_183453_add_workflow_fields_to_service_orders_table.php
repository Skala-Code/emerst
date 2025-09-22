<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Campo para o responsável atual (pode ser diferente do lawyer_id original)
            $table->foreignId('current_responsible_id')->nullable()->constrained('lawyers')->after('lawyer_id');

            // Histórico de workflow em JSON
            $table->json('workflow_history')->nullable()->after('custom_data');

            // Status mais detalhado do workflow
            $table->string('workflow_stage')->default('created')->after('status'); // created, assigned, in_progress, review, completed, rejected

            // Data de início efetivo (quando alguém começou a trabalhar)
            $table->timestamp('started_at')->nullable()->after('due_date');

            // Data de conclusão efetiva
            $table->timestamp('completed_at')->nullable()->after('started_at');

            // Estimativa de tempo em horas
            $table->decimal('estimated_hours', 8, 2)->nullable()->after('completed_at');

            // Tempo real gasto em horas
            $table->decimal('actual_hours', 8, 2)->nullable()->after('estimated_hours');

            // Observações do responsável atual
            $table->text('current_notes')->nullable()->after('actual_hours');
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['current_responsible_id']);
            $table->dropColumn([
                'current_responsible_id',
                'workflow_history',
                'workflow_stage',
                'started_at',
                'completed_at',
                'estimated_hours',
                'actual_hours',
                'current_notes',
            ]);
        });
    }
};
