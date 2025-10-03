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
        Schema::table('process_parties', function (Blueprint $table) {
            // Adicionar parent_id para representantes
            $table->foreignId('parent_id')->nullable()->after('process_id')->constrained('process_parties')->onDelete('cascade');

            // Dados da API
            $table->unsignedBigInteger('api_id')->nullable()->after('parent_id');
            $table->unsignedBigInteger('api_pessoa_id')->nullable()->after('api_id');

            // Novos campos da API
            $table->string('nome')->nullable()->after('name');
            $table->string('login')->nullable()->after('nome');
            $table->string('tipo')->nullable()->after('login'); // RECLAMANTE, RECLAMADO, TERCEIRO INTERESSADO, ADVOGADO
            $table->string('documento')->nullable()->after('document');
            $table->string('tipo_documento')->nullable()->after('documento'); // CPF, CPJ

            // Endereço como JSON
            $table->json('endereco')->nullable()->after('address');

            // Classificação
            $table->string('polo')->nullable()->after('party_type'); // ATIVO, PASSIVO, TERCEIROS
            $table->string('situacao')->default('ATIVO')->after('polo');

            // Papéis (JSON) - pode ter múltiplos papéis
            $table->json('papeis')->nullable()->after('role');

            // Controle
            $table->boolean('utiliza_login_senha')->default(false)->after('papeis');

            // Índices
            $table->index('parent_id');
            $table->index('tipo');
            $table->index('polo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('process_parties', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'api_id',
                'api_pessoa_id',
                'nome',
                'login',
                'tipo',
                'documento',
                'tipo_documento',
                'endereco',
                'polo',
                'situacao',
                'papeis',
                'utiliza_login_senha',
            ]);
        });
    }
};
