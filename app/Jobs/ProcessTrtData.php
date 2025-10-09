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
            Log::info("Iniciando consulta TRT para processo {$this->process->processo}");

            // Extrai o número do TRT do número do processo
            $trtNumber = $trtService->extractTrtNumber($this->process->processo);
            if (! $trtNumber) {
                throw new \Exception('Não foi possível extrair o número do TRT do processo');
            }

            // Consulta a API
            $data = $trtService->consultarProcesso($this->process->processo);

            if (! $data) {
                throw new \Exception('API retornou dados vazios ou inválidos');
            }

            // Atualiza o processo com os dados da API
            $this->process->update([
                'trt' => $trtNumber,
                'ultima_atualizacao_api' => now(),
                'error' => null,
                'sincronizado' => true,

                // Extrai e salva os dados principais
                'reclamantes' => $data['reclamantes'] ?? null,
                'reclamados' => $data['reclamados'] ?? null,
                'outros_interessados' => $data['outrosInteressados'] ?? null,

                // Atualiza campos do processo com dados da API
                'classe' => $data['classe'] ?? $this->process->classe,
                'orgao_julgador' => $data['orgaoJulgador'] ?? $this->process->orgao_julgador,
                'valor_causa' => isset($data['valorCausa']) ? $trtService->formatarValorCausa($data['valorCausa']) : $this->process->valor_causa,

                // Datas
                'distribuido' => isset($data['distribuidoEm']) ? \Carbon\Carbon::parse($data['distribuidoEm']) : $this->process->distribuido,
                'autuado' => isset($data['autuadoEm']) ? \Carbon\Carbon::parse($data['autuadoEm']) : $this->process->autuado,

                // Assuntos
                'assuntos' => $data['assuntos'] ?? $this->process->assuntos,

                // PDFs se houver
                'pdfs' => $data['pdfs'] ?? $this->process->pdfs,
            ]);

            Log::info("Processo {$this->process->processo} sincronizado com sucesso com a API TRT");
        } catch (\Exception $e) {
            Log::error("Erro ao processar dados TRT para processo {$this->process->processo}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Salva o erro
            $this->process->update([
                'error' => $e->getMessage(),
                'ultima_atualizacao_api' => now(),
                'sincronizado' => false,
            ]);

            Log::warning("Erro ao processar {$this->process->processo}: {$e->getMessage()}");

            throw $e;
        }
    }
}
