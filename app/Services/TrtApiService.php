<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrtApiService
{
    private string $baseUrl = 'http://24.152.38.77:6400';

    /**
     * Consulta dados de um processo na API TRT
     */
    public function consultarProcesso(string $numeroProcesso): ?array
    {
        try {
            $response = Http::timeout(30)
                ->get("{$this->baseUrl}/api/processo/{$numeroProcesso}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error']) && $data['error'] !== null) {
                    Log::warning("TRT API Error for process {$numeroProcesso}", [
                        'error' => $data['error'],
                    ]);

                    return null;
                }

                return $data['data'] ?? null;
            }

            Log::error("TRT API HTTP Error for process {$numeroProcesso}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("TRT API Exception for process {$numeroProcesso}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Extrai número do TRT do número do processo
     * Exemplo: 0020019-44.2020.5.04.0663 -> 04
     */
    public function extractTrtNumber(string $numeroProcesso): ?string
    {
        // Padrão: NNNNNNN-DD.AAAA.J.TR.OOOO
        // TR = Tribunal Regional (2 dígitos)
        if (preg_match('/\.\d\.(\d{2})\./', $numeroProcesso, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Formata valor da causa removendo "R$" e espaços
     */
    public function formatarValorCausa(?string $valorCausa): ?float
    {
        if (! $valorCausa) {
            return null;
        }

        // Remove "R$", espaços e converte vírgula para ponto
        $valor = str_replace(['R$', ' ', '.'], '', $valorCausa);
        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }
}
