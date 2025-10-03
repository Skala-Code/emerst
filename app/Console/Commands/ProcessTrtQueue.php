<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTrtData;
use App\Models\Process;
use Illuminate\Console\Command;

class ProcessTrtQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:trt-queue
                            {--limit=10 : Número máximo de processos a processar}
                            {--force : Forçar reprocessamento de processos já sincronizados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa a fila de processos aguardando sincronização com a API TRT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $this->info("🔄 Iniciando processamento da fila TRT...");

        // Busca processos aguardando API TRT
        $query = Process::where('status', 'aguardando_api_trt');

        if (! $force) {
            // Não reprocessa processos que já foram sincronizados com sucesso
            $query->whereNull('trt_api_synced_at');
        }

        $processes = $query->limit($limit)->get();

        if ($processes->isEmpty()) {
            $this->warn('⚠️  Nenhum processo encontrado na fila.');

            return Command::SUCCESS;
        }

        $this->info("📋 {$processes->count()} processo(s) encontrado(s).");

        $bar = $this->output->createProgressBar($processes->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($processes as $process) {
            try {
                // Despacha o job para a fila
                ProcessTrtData::dispatch($process);
                $success++;

                $this->newLine();
                $this->line("✅ Job despachado para processo: {$process->number}");
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("❌ Erro ao despachar job para processo {$process->number}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('📊 Resumo:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total processado', $processes->count()],
                ['Jobs despachados', $success],
                ['Falhas', $failed],
            ]
        );

        if ($success > 0) {
            $this->info('✨ Jobs despachados com sucesso! Eles serão processados pela fila.');
            $this->comment('💡 Execute "php artisan queue:work" para processar os jobs imediatamente.');
        }

        return Command::SUCCESS;
    }
}
