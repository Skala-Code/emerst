<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ServiceOrderCalculationController extends Controller
{
    /**
     * Salva o ID do cálculo na ordem de serviço
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveCalculation(Request $request): JsonResponse
    {
        // Validar requisição
        $validated = $request->validate([
            'service_order_id' => 'required|integer|exists:service_orders,id',
            'calculation_number' => 'required|string|max:255',
        ]);

        try {
            // Buscar ordem de serviço
            $serviceOrder = ServiceOrder::findOrFail($validated['service_order_id']);

            // Salvar o número do cálculo
            $serviceOrder->analyzed_calculation_id_fls = $validated['calculation_number'];
            $serviceOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'ID do cálculo salvo com sucesso',
                'data' => [
                    'service_order_id' => $serviceOrder->id,
                    'calculation_number' => $serviceOrder->analyzed_calculation_id_fls,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar ID do cálculo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Busca dados de liquidação do PJeCalc e salva na ordem de serviço
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchLiquidation(Request $request): JsonResponse
    {
        // Validar requisição
        $validated = $request->validate([
            'service_order_id' => 'required|integer|exists:service_orders,id',
            'numero_calculo' => 'required|string',
            'data_liquidacao' => 'required|string', // Formato: dd/mm/yyyy
        ]);

        try {
            // Buscar ordem de serviço
            $serviceOrder = ServiceOrder::findOrFail($validated['service_order_id']);

            // Construir URL do PJeCalc
            $baseUrl = 'http://calculo.emerst.com.br:9257';
            $url = $baseUrl . '/pjecalc/liquidacao.jsp';

            // Fazer requisição ao endpoint externo
            $response = Http::timeout(30)->get($url, [
                'numeroCalculo' => $validated['numero_calculo'],
                'dataLiquidacao' => $validated['data_liquidacao'],
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao buscar dados de liquidação',
                    'error' => 'O servidor do PJeCalc retornou um erro: ' . $response->status(),
                ], 500);
            }

            $data = $response->json();

            // Validar se a resposta tem os dados esperados
            if (! isset($data['status'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resposta inválida do PJeCalc',
                    'error' => 'Formato de resposta não reconhecido',
                ], 500);
            }

            // Salvar dados na ordem de serviço
            $serviceOrder->update([
                'liquidation_numero_calculo' => $data['numeroCalculo'] ?? null,
                'liquidation_data' => isset($data['dataLiquidacao'])
                    ? \Carbon\Carbon::createFromFormat('d/m/Y', $data['dataLiquidacao'])->format('Y-m-d')
                    : null,
                'liquidation_status' => $data['status'] ?? null,
                'liquidation_mensagem' => $data['mensagem'] ?? null,
                'liquidation_valor_total' => $data['valorTotal'] ?? null,
                'liquidation_valor_principal' => $data['valorPrincipal'] ?? null,
                'liquidation_valor_juros' => $data['valorJuros'] ?? null,
                'liquidation_valor_correcao' => $data['valorCorrecao'] ?? null,
                'liquidation_itens' => $data['itens'] ?? [],
                'liquidation_alertas' => $data['alertas'] ?? [],
                'liquidation_erros' => $data['erros'] ?? [],
                'liquidation_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dados de liquidação atualizados com sucesso',
                'data' => [
                    'service_order_id' => $serviceOrder->id,
                    'liquidation' => $data,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar liquidação',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Busca relatório do PJeCalc e salva na ordem de serviço
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchReport(Request $request): JsonResponse
    {
        // Validar requisição
        $validated = $request->validate([
            'service_order_id' => 'required|integer|exists:service_orders,id',
            'numero_calculo' => 'required|string',
            'tipo_relatorio' => 'nullable|string|in:COMPLETO,RESUMO,DEMONSTRATIVO,CONTRIBUICAO,JUSTIFICATIVA',
            'formato' => 'nullable|string|in:HTML,JSON',
        ]);

        try {
            // Buscar ordem de serviço
            $serviceOrder = ServiceOrder::findOrFail($validated['service_order_id']);

            // Parâmetros padrão
            $tipoRelatorio = $validated['tipo_relatorio'] ?? 'COMPLETO';
            $formato = $validated['formato'] ?? 'JSON';

            // Construir URL do PJeCalc
            $baseUrl = 'http://calculo.emerst.com.br:9257';
            $url = $baseUrl . '/pjecalc/relatorio-api.jsp';

            // Fazer requisição ao endpoint externo
            $response = Http::timeout(60)->post($url, [
                'numeroCalculo' => $validated['numero_calculo'],
                'tipoRelatorio' => $tipoRelatorio,
                'formato' => $formato,
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao buscar relatório do PJeCalc',
                    'error' => 'O servidor do PJeCalc retornou um erro: ' . $response->status(),
                ], 500);
            }

            $data = $response->json();

            // Validar se a resposta tem os dados esperados
            if (! isset($data['status'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resposta inválida do PJeCalc',
                    'error' => 'Formato de resposta não reconhecido',
                ], 500);
            }

            // Criar registro do relatório
            $report = ServiceOrderReport::create([
                'service_order_id' => $serviceOrder->id,
                'numero_calculo' => $data['numeroCalculo'] ?? $validated['numero_calculo'],
                'tipo_relatorio' => $data['tipoRelatorio'] ?? $tipoRelatorio,
                'formato' => $data['formato'] ?? $formato,
                'status' => $data['status'] ?? 'GERADO',
                'data_geracao' => isset($data['dataGeracao'])
                    ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['dataGeracao'])
                    : now(),
                'html_content' => $data['html'] ?? null,
                'dados_estruturados' => $data,
                'tamanho_bytes' => $data['tamanho'] ?? null,
                'url_direta' => $data['urlDireta'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Relatório gerado e salvo com sucesso',
                'data' => [
                    'report_id' => $report->id,
                    'service_order_id' => $serviceOrder->id,
                    'numero_calculo' => $report->numero_calculo,
                    'tipo_relatorio' => $report->tipo_relatorio,
                    'data_geracao' => $report->data_geracao->format('d/m/Y H:i:s'),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar relatório',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
