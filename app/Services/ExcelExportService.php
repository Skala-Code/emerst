<?php

namespace App\Services;

use App\Models\Process;
use App\Models\ServiceOrder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelExportService
{
    public function exportProcesses(Collection $processes): string
    {
        $spreadsheet = new Spreadsheet();

        // Criar todas as abas conforme o template UNIMED
        $this->createProcessSummarySheet($spreadsheet, $processes);
        $this->createEmployeeDataSheet($spreadsheet, $processes);
        $this->createLaborBenefitsSheet($spreadsheet, $processes);
        $this->createFinancialControlSheet($spreadsheet, $processes);
        $this->createProvisionsByPhaseSheet($spreadsheet, $processes);

        // Salvar arquivo temporário
        $filename = 'relatorio_processos_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);

        // Criar diretório se não existir
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    private function createProcessSummarySheet(Spreadsheet $spreadsheet, Collection $processes): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumo Processos');

        // Cabeçalho
        $headers = [
            'A1' => 'Número do Processo',
            'B1' => 'Título',
            'C1' => 'Empresa',
            'D1' => 'Escritório',
            'E1' => 'Advogado',
            'F1' => 'Status',
            'G1' => 'Fase Processual',
            'H1' => 'Data Início',
            'I1' => 'Prazo',
            'J1' => 'Provisão Atual',
            'K1' => 'Total OS',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Estilizar cabeçalho
        $this->styleHeader($sheet, 'A1:K1');

        // Dados
        $row = 2;
        foreach ($processes as $process) {
            $sheet->setCellValue('A' . $row, $process->number);
            $sheet->setCellValue('B' . $row, $process->title);
            $sheet->setCellValue('C' . $row, $process->company?->name);
            $sheet->setCellValue('D' . $row, $process->office?->name);
            $sheet->setCellValue('E' . $row, $process->lawyer?->name);
            $sheet->setCellValue('F' . $row, $this->getStatusLabel($process->status));
            $sheet->setCellValue('G' . $row, $process->procedural_phase);
            $sheet->setCellValue('H' . $row, $process->start_date?->format('d/m/Y'));
            $sheet->setCellValue('I' . $row, $process->deadline?->format('d/m/Y'));
            $sheet->setCellValue('J' . $row, $process->current_provision ?? 0);
            $sheet->setCellValue('K' . $row, $process->serviceOrders->count());

            $row++;
        }

        // Auto-ajustar colunas
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function createEmployeeDataSheet(Spreadsheet $spreadsheet, Collection $processes): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Dados Funcionários');

        // Cabeçalho
        $headers = [
            'A1' => 'Processo',
            'B1' => 'Função',
            'C1' => 'Cidade',
            'D1' => 'Estado',
            'E1' => 'Data Admissão',
            'F1' => 'Data Demissão',
            'G1' => 'Tipo Reclamada',
            'H1' => 'Tipo de Caso',
            'I1' => 'Situação',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        $this->styleHeader($sheet, 'A1:I1');

        // Dados
        $row = 2;
        foreach ($processes as $process) {
            $sheet->setCellValue('A' . $row, $process->number);
            $sheet->setCellValue('B' . $row, $process->employee_function);
            $sheet->setCellValue('C' . $row, $process->city);
            $sheet->setCellValue('D' . $row, $process->state);
            $sheet->setCellValue('E' . $row, $process->admission_date?->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, $process->termination_date?->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $process->defendant_type);
            $sheet->setCellValue('H' . $row, $process->case_type);
            $sheet->setCellValue('I' . $row, $process->situation);

            $row++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function createLaborBenefitsSheet(Spreadsheet $spreadsheet, Collection $processes): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Verbas Trabalhistas');

        // Cabeçalho principal
        $laborFields = [
            'A1' => 'Processo',
            'B1' => 'OS',
            'C1' => 'Operadores Intervalo Especial',
            'D1' => 'HE Int. Intrajornada 384',
            'E1' => 'HE Excedente 6h Diárias',
            'F1' => 'HE Excedente 8h Diárias',
            'G1' => 'HE Int. Entrejornadas 66',
            'H1' => 'HE In Itinere',
            'I1' => 'HE Int. Intrajornada 71',
            'J1' => 'HE Banco de Horas',
            'K1' => 'HE Sobreaviso',
            'L1' => 'HE Domingos e Feriados',
            'M1' => 'Adicional Noturno',
            'N1' => 'Integração Salarial Natura',
            'O1' => 'Integração VR/VA',
            'P1' => 'Salary Plus',
            'Q1' => 'Prêmio Produtividade',
            'R1' => '13º Salário',
            'S1' => 'Aviso Prévio',
            'T1' => 'Férias Dobradas + 1/3',
            'U1' => 'Subtotal',
            'V1' => 'Total Bruto',
            'W1' => 'Total Líquido',
            'X1' => 'Honorários Advocatícios',
            'Y1' => 'Total Devido pela Ré',
        ];

        foreach ($laborFields as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        $this->styleHeader($sheet, 'A1:Y1');

        // Dados das verbas trabalhistas
        $row = 2;
        foreach ($processes as $process) {
            foreach ($process->serviceOrders as $serviceOrder) {
                $sheet->setCellValue('A' . $row, $process->number);
                $sheet->setCellValue('B' . $row, $serviceOrder->number);
                $sheet->setCellValue('C' . $row, $serviceOrder->special_interval_operators ?? 0);
                $sheet->setCellValue('D' . $row, $serviceOrder->he_int_intrajornada_384 ?? 0);
                $sheet->setCellValue('E' . $row, $serviceOrder->he_excedent_6_daily ?? 0);
                $sheet->setCellValue('F' . $row, $serviceOrder->he_excedent_8_daily ?? 0);
                $sheet->setCellValue('G' . $row, $serviceOrder->he_int_entrejornadas_66 ?? 0);
                $sheet->setCellValue('H' . $row, $serviceOrder->he_in_itinere ?? 0);
                $sheet->setCellValue('I' . $row, $serviceOrder->he_int_intrajornada_71 ?? 0);
                $sheet->setCellValue('J' . $row, $serviceOrder->he_time_bank ?? 0);
                $sheet->setCellValue('K' . $row, $serviceOrder->he_standby ?? 0);
                $sheet->setCellValue('L' . $row, $serviceOrder->he_sundays_holidays ?? 0);
                $sheet->setCellValue('M' . $row, $serviceOrder->night_shift_bonus ?? 0);
                $sheet->setCellValue('N' . $row, $serviceOrder->salary_integration_natura ?? 0);
                $sheet->setCellValue('O' . $row, $serviceOrder->vr_va_integration ?? 0);
                $sheet->setCellValue('P' . $row, $serviceOrder->salary_plus ?? 0);
                $sheet->setCellValue('Q' . $row, $serviceOrder->productivity_bonus ?? 0);
                $sheet->setCellValue('R' . $row, $serviceOrder->thirteenth_salary ?? 0);
                $sheet->setCellValue('S' . $row, $serviceOrder->prior_notice ?? 0);
                $sheet->setCellValue('T' . $row, $serviceOrder->double_vacation_one_third ?? 0);
                $sheet->setCellValue('U' . $row, $serviceOrder->subtotal ?? 0);
                $sheet->setCellValue('V' . $row, $serviceOrder->gross_total ?? 0);
                $sheet->setCellValue('W' . $row, $serviceOrder->net_total ?? 0);
                $sheet->setCellValue('X' . $row, $serviceOrder->attorney_fees_amount ?? 0);
                $sheet->setCellValue('Y' . $row, $serviceOrder->total_due_by_defendant ?? 0);

                $row++;
            }
        }

        // Formato monetário para colunas de valores
        $valueColumns = range('C', 'Y');
        foreach ($valueColumns as $column) {
            $sheet->getStyle($column . '2:' . $column . $row)
                  ->getNumberFormat()
                  ->setFormatCode('_("R$"* #,##0.00_);_("R$"* \(#,##0.00\);_("R$"* "-"??_);_(@_)');
        }

        foreach (range('A', 'Y') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function createFinancialControlSheet(Spreadsheet $spreadsheet, Collection $processes): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Controle Financeiro');

        $headers = [
            'A1' => 'Processo',
            'B1' => 'Taxa de Juros (%)',
            'C1' => 'Demissão até Ajuizamento TR (%)',
            'D1' => 'Ajuizamento até Atual TR (%)',
            'E1' => 'Demissão até Ajuizamento IPCA (%)',
            'F1' => 'Ajuizamento até Atual IPCA (%)',
            'G1' => 'Meses de Prescrição',
            'H1' => 'Provisão Atual TR',
            'I1' => 'Provisão Atual IPCA',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        $this->styleHeader($sheet, 'A1:I1');

        $row = 2;
        foreach ($processes as $process) {
            $sheet->setCellValue('A' . $row, $process->number);
            $sheet->setCellValue('B' . $row, $process->interest_rate ?? 0);
            $sheet->setCellValue('C' . $row, $process->termination_to_filing_tr ?? 0);
            $sheet->setCellValue('D' . $row, $process->filing_to_current_tr ?? 0);
            $sheet->setCellValue('E' . $row, $process->termination_to_filing_ipca ?? 0);
            $sheet->setCellValue('F' . $row, $process->filing_to_current_ipca ?? 0);
            $sheet->setCellValue('G' . $row, $process->prescription_months ?? 0);
            $sheet->setCellValue('H' . $row, $process->current_provision_tr ?? 0);
            $sheet->setCellValue('I' . $row, $process->current_provision_ipca ?? 0);

            $row++;
        }

        // Formato percentual para colunas de percentual
        $percentColumns = ['B', 'C', 'D', 'E', 'F'];
        foreach ($percentColumns as $column) {
            $sheet->getStyle($column . '2:' . $column . $row)
                  ->getNumberFormat()
                  ->setFormatCode('0.0000%');
        }

        // Formato monetário para provisões
        $valueColumns = ['H', 'I'];
        foreach ($valueColumns as $column) {
            $sheet->getStyle($column . '2:' . $column . $row)
                  ->getNumberFormat()
                  ->setFormatCode('_("R$"* #,##0.00_);_("R$"* \(#,##0.00\);_("R$"* "-"??_);_(@_)');
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function createProvisionsByPhaseSheet(Spreadsheet $spreadsheet, Collection $processes): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Provisões por Fase');

        $headers = [
            'A1' => 'Processo',
            'B1' => 'Provisão Inicial',
            'C1' => 'Provisão Sentença',
            'D1' => 'Provisão TRT',
            'E1' => 'Provisão TST',
            'F1' => 'Provisão Acordo',
            'G1' => 'Provisão Atual',
            'H1' => 'Depósitos Recursais',
            'I1' => 'Depósitos Judiciais',
            'J1' => 'Liberações/Pagamentos',
            'K1' => 'Status Perda Anterior',
            'L1' => 'Status Perda Atual',
            'M1' => 'Previsão Desembolso',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        $this->styleHeader($sheet, 'A1:M1');

        $row = 2;
        foreach ($processes as $process) {
            $sheet->setCellValue('A' . $row, $process->number);
            $sheet->setCellValue('B' . $row, $process->initial_provision ?? 0);
            $sheet->setCellValue('C' . $row, $process->sentence_provision ?? 0);
            $sheet->setCellValue('D' . $row, $process->trt_provision ?? 0);
            $sheet->setCellValue('E' . $row, $process->tst_provision ?? 0);
            $sheet->setCellValue('F' . $row, $process->settlement_provision ?? 0);
            $sheet->setCellValue('G' . $row, $process->current_provision ?? 0);
            $sheet->setCellValue('H' . $row, $process->appeal_deposits ?? 0);
            $sheet->setCellValue('I' . $row, $process->judicial_deposits ?? 0);
            $sheet->setCellValue('J' . $row, $process->releases_payments ?? 0);
            $sheet->setCellValue('K' . $row, $process->loss_status_previous);
            $sheet->setCellValue('L' . $row, $process->loss_status_current);
            $sheet->setCellValue('M' . $row, $process->disbursement_forecast?->format('d/m/Y'));

            $row++;
        }

        // Formato monetário para colunas de valores
        $valueColumns = range('B', 'J');
        foreach ($valueColumns as $column) {
            $sheet->getStyle($column . '2:' . $column . $row)
                  ->getNumberFormat()
                  ->setFormatCode('_("R$"* #,##0.00_);_("R$"* \(#,##0.00\);_("R$"* "-"??_);_(@_)');
        }

        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Ativo',
            'suspended' => 'Suspenso',
            'archived' => 'Arquivado',
            'completed' => 'Concluído',
            default => $status,
        };
    }
}