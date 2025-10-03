<?php

namespace App\Jobs;

use App\Models\Process;
use App\Services\TrtApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessTrtData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Process $process
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TrtApiService $trtService): void
    {
        try {
            Log::info("Iniciando consulta TRT para processo {$this->process->number}");

            // Extrai o número do TRT do número do processo
            $trtNumber = $trtService->extractTrtNumber($this->process->number);
            if (! $trtNumber) {
                throw new \Exception('Não foi possível extrair o número do TRT do processo');
            }

            // Consulta a API
            $data = $trtService->consultarProcesso($this->process->number);

            if (! $data) {
                throw new \Exception('API retornou dados vazios ou inválidos');
            }

            // Atualiza o processo com os dados da API
            $this->process->update([
                'trt_number' => $trtNumber,
                'trt_api_data' => $data,
                'trt_api_synced_at' => now(),
                'trt_api_error' => null,

                // Extrai e salva os dados principais
                'trt_reclamantes' => $data['reclamantes'] ?? null,
                'trt_reclamados' => $data['reclamados'] ?? null,
                'trt_outros_interessados' => $data['outrosInteressados'] ?? null,

                // Atualiza campos do processo com dados da API
                'classe' => $data['classe'] ?? $this->process->classe,
                'orgao_julgador' => $data['orgaoJulgador'] ?? $this->process->orgao_julgador,
                'segredo_justica' => $data['segredoJustica'] ?? $this->process->segredo_justica,
                'justica_gratuita' => $data['justicaGratuita'] ?? $this->process->justica_gratuita,
                'valor_da_causa' => isset($data['valorCausa']) ? $trtService->formatarValorCausa($data['valorCausa']) : $this->process->valor_da_causa,
                'juizo_digital' => $data['juizoDigital'] ?? $this->process->juizo_digital,

                // Datas
                'distribuido_em' => isset($data['distribuidoEm']) ? \Carbon\Carbon::parse($data['distribuidoEm']) : $this->process->distribuido_em,
                'autuado_em' => isset($data['autuadoEm']) ? \Carbon\Carbon::parse($data['autuadoEm']) : $this->process->autuado_em,

                // Muda o status para ativo após sincronização bem-sucedida
                'status' => 'active',
            ]);

            Log::info("Processo {$this->process->number} sincronizado com sucesso com a API TRT");
        } catch (\Exception $e) {
            Log::error("Erro ao processar dados TRT para processo {$this->process->number}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Incrementa tentativas e salva o erro
            $this->process->update([
                'trt_api_attempts' => $this->process->trt_api_attempts + 1,
                'trt_api_error' => $e->getMessage(),
            ]);

            // Se ultrapassar 3 tentativas, muda status para error
            if ($this->process->trt_api_attempts >= 3) {
                $this->process->update([
                    'status' => 'suspended',
                ]);
                Log::warning("Processo {$this->process->number} suspenso após 3 tentativas falhadas");
            }

            throw $e;
        }
    }
}
