<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomField;
use App\Models\CustomTab;
use App\Models\Lawyer;
use App\Models\Office;
use App\Models\Process;
use App\Models\ServiceOrder;
use Illuminate\Database\Seeder;

class DeadlineManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create Companies
        $company1 = Company::create([
            'name' => 'Skala Tecnologia',
            'cnpj' => '12.345.678/0001-90',
            'email' => 'contato@skala.com.br',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua da Tecnologia, 123 - São Paulo, SP',
            'active' => true,
        ]);

        $company2 = Company::create([
            'name' => 'Advocacia & Associados',
            'cnpj' => '98.765.432/0001-10',
            'email' => 'contato@advocacia.com.br',
            'phone' => '(11) 88888-8888',
            'address' => 'Av. Paulista, 456 - São Paulo, SP',
            'active' => true,
        ]);

        // Create Offices
        $office1 = Office::create([
            'company_id' => $company1->id,
            'name' => 'Escritório Central',
            'email' => 'central@skala.com.br',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua da Tecnologia, 123 - São Paulo, SP',
            'active' => true,
        ]);

        $office2 = Office::create([
            'company_id' => $company1->id,
            'name' => 'Filial Campinas',
            'email' => 'campinas@skala.com.br',
            'phone' => '(19) 99999-9999',
            'address' => 'Rua das Flores, 789 - Campinas, SP',
            'active' => true,
        ]);

        $office3 = Office::create([
            'company_id' => $company2->id,
            'name' => 'Sede Principal',
            'email' => 'sede@advocacia.com.br',
            'phone' => '(11) 88888-8888',
            'address' => 'Av. Paulista, 456 - São Paulo, SP',
            'active' => true,
        ]);

        // Create Lawyers
        $lawyer1 = Lawyer::create([
            'office_id' => $office1->id,
            'name' => 'Dr. João Silva',
            'oab' => 'SP123456',
            'email' => 'joao@skala.com.br',
            'phone' => '(11) 91111-1111',
            'active' => true,
        ]);

        $lawyer2 = Lawyer::create([
            'office_id' => $office1->id,
            'name' => 'Dra. Maria Santos',
            'oab' => 'SP654321',
            'email' => 'maria@skala.com.br',
            'phone' => '(11) 92222-2222',
            'active' => true,
        ]);

        $lawyer3 = Lawyer::create([
            'office_id' => $office2->id,
            'name' => 'Dr. Pedro Oliveira',
            'oab' => 'SP789012',
            'email' => 'pedro@skala.com.br',
            'phone' => '(19) 93333-3333',
            'active' => true,
        ]);

        $lawyer4 = Lawyer::create([
            'office_id' => $office3->id,
            'name' => 'Dra. Ana Costa',
            'oab' => 'SP345678',
            'email' => 'ana@advocacia.com.br',
            'phone' => '(11) 94444-4444',
            'active' => true,
        ]);

        // Create Custom Tabs for Process
        $processInfoTab = CustomTab::create([
            'model_type' => 'process',
            'name' => 'informacoes_gerais',
            'label' => 'Informações Gerais',
            'sort_order' => 1,
            'active' => true,
            'permissions' => ['admin', 'advogado'],
        ]);

        $processDocumentsTab = CustomTab::create([
            'model_type' => 'process',
            'name' => 'documentos',
            'label' => 'Documentos',
            'sort_order' => 2,
            'active' => true,
            'permissions' => ['admin', 'advogado', 'colaborador'],
        ]);

        // Create Custom Fields for Process
        CustomField::create([
            'model_type' => 'process',
            'custom_tab_id' => $processInfoTab->id,
            'name' => 'valor_causa',
            'label' => 'Valor da Causa',
            'type' => 'number',
            'sort_order' => 1,
            'required' => false,
            'active' => true,
        ]);

        CustomField::create([
            'model_type' => 'process',
            'custom_tab_id' => $processInfoTab->id,
            'name' => 'area_direito',
            'label' => 'Área do Direito',
            'type' => 'select',
            'options' => [
                'civil' => 'Direito Civil',
                'trabalhista' => 'Direito Trabalhista',
                'penal' => 'Direito Penal',
                'tributario' => 'Direito Tributário',
                'empresarial' => 'Direito Empresarial',
            ],
            'sort_order' => 2,
            'required' => true,
            'active' => true,
        ]);

        CustomField::create([
            'model_type' => 'process',
            'custom_tab_id' => $processDocumentsTab->id,
            'name' => 'peticao_inicial',
            'label' => 'Petição Inicial',
            'type' => 'file',
            'sort_order' => 1,
            'required' => false,
            'active' => true,
        ]);

        // Create Custom Tabs for Service Order
        $serviceOrderTaskTab = CustomTab::create([
            'model_type' => 'service_order',
            'name' => 'detalhes_tarefa',
            'label' => 'Detalhes da Tarefa',
            'sort_order' => 1,
            'active' => true,
            'permissions' => ['admin', 'advogado'],
        ]);

        $serviceOrderFollowUpTab = CustomTab::create([
            'model_type' => 'service_order',
            'name' => 'acompanhamento',
            'label' => 'Acompanhamento',
            'sort_order' => 2,
            'active' => true,
            'permissions' => ['admin', 'advogado', 'colaborador'],
        ]);

        // Create Custom Fields for Service Order
        CustomField::create([
            'model_type' => 'service_order',
            'custom_tab_id' => $serviceOrderTaskTab->id,
            'name' => 'tipo_servico',
            'label' => 'Tipo de Serviço',
            'type' => 'select',
            'options' => [
                'audiencia' => 'Audiência',
                'peticao' => 'Petição',
                'recurso' => 'Recurso',
                'analise' => 'Análise Documental',
                'pesquisa' => 'Pesquisa Jurisprudencial',
            ],
            'sort_order' => 1,
            'required' => true,
            'active' => true,
        ]);

        CustomField::create([
            'model_type' => 'service_order',
            'custom_tab_id' => $serviceOrderTaskTab->id,
            'name' => 'tempo_estimado',
            'label' => 'Tempo Estimado (horas)',
            'type' => 'number',
            'sort_order' => 2,
            'required' => false,
            'active' => true,
        ]);

        CustomField::create([
            'model_type' => 'service_order',
            'custom_tab_id' => $serviceOrderFollowUpTab->id,
            'name' => 'observacoes',
            'label' => 'Observações',
            'type' => 'textarea',
            'sort_order' => 1,
            'required' => false,
            'active' => true,
        ]);

        CustomField::create([
            'model_type' => 'service_order',
            'custom_tab_id' => $serviceOrderFollowUpTab->id,
            'name' => 'aprovado_cliente',
            'label' => 'Aprovado pelo Cliente',
            'type' => 'checkbox',
            'sort_order' => 2,
            'required' => false,
            'active' => true,
        ]);

        // Create Sample Processes
        $process1 = Process::create([
            'company_id' => $company1->id,
            'office_id' => $office1->id,
            'lawyer_id' => $lawyer1->id,
            'number' => '1001234-56.2024.8.26.0001',
            'title' => 'Ação Trabalhista - Horas Extras',
            'description' => 'Ação trabalhista para cobrança de horas extras não pagas.',
            'status' => 'active',
            'start_date' => now()->subDays(30),
            'deadline' => now()->addDays(60),
            'custom_data' => [
                'valor_causa' => 50000.00,
                'area_direito' => 'trabalhista',
            ],
        ]);

        $process2 = Process::create([
            'company_id' => $company1->id,
            'office_id' => $office1->id,
            'lawyer_id' => $lawyer2->id,
            'number' => '2001234-56.2024.8.26.0002',
            'title' => 'Ação Cível - Indenização por Danos Morais',
            'description' => 'Ação cível para indenização por danos morais.',
            'status' => 'active',
            'start_date' => now()->subDays(15),
            'deadline' => now()->addDays(45),
            'custom_data' => [
                'valor_causa' => 30000.00,
                'area_direito' => 'civil',
            ],
        ]);

        $process3 = Process::create([
            'company_id' => $company2->id,
            'office_id' => $office3->id,
            'lawyer_id' => $lawyer4->id,
            'number' => '3001234-56.2024.8.26.0003',
            'title' => 'Defesa Criminal - Art. 155 CP',
            'description' => 'Defesa criminal em processo por furto.',
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'deadline' => now()->addDays(30),
            'custom_data' => [
                'area_direito' => 'penal',
            ],
        ]);

        // Create Sample Service Orders with labor claims data
        ServiceOrder::create([
            'process_id' => $process1->id,
            'lawyer_id' => $lawyer1->id,
            'current_responsible_id' => $lawyer1->id,
            'number' => 'OS-2024-001',
            'title' => 'Cálculo de Liquidação - Verbas Trabalhistas',
            'description' => 'Cálculo completo das verbas devidas ao reclamante.',
            'priority' => 'high',
            'status' => 'completed',
            'workflow_stage' => 'completed',
            'due_date' => now()->subDays(25),
            'estimated_hours' => 8.0,
            'actual_hours' => 7.5,
            'started_at' => now()->subDays(30),
            'completed_at' => now()->subDays(25),
            // Verbas trabalhistas
            'he_excedent_8_daily' => 5500.00,
            'he_sundays_holidays' => 2200.00,
            'night_shift_bonus' => 1800.00,
            'unhealthiness_bonus' => 1200.00,
            'thirteenth_salary' => 3500.00,
            'prior_notice' => 3000.00,
            'double_vacation_one_third' => 4200.00,
            'subtotal' => 21400.00,
            'fgts_contract_diff' => 1712.00,
            'gross_total' => 23112.00,
            'inss_employee' => 1848.96,
            'net_total' => 21263.04,
            'attorney_fees_percentage' => 20.0,
            'attorney_fees_amount' => 4622.40,
            'total_due_by_defendant' => 25885.44,
            // Controles
            'calculation_situation' => 'concluido',
            'validation_passed' => true,
            'calculation_phase' => 'sentenca',
            'calculation_date' => now()->subDays(25),
            'custom_data' => [
                'tipo_servico' => 'peticao',
                'tempo_estimado' => 8,
                'observacoes' => 'Cálculo elaborado e aprovado.',
                'aprovado_cliente' => true,
            ],
        ]);

        ServiceOrder::create([
            'process_id' => $process1->id,
            'lawyer_id' => $lawyer1->id,
            'current_responsible_id' => $lawyer1->id,
            'number' => 'OS-2024-002',
            'title' => 'Preparar Audiência',
            'description' => 'Preparar documentos e estratégia para audiência.',
            'priority' => 'urgent',
            'status' => 'in_progress',
            'workflow_stage' => 'in_progress',
            'due_date' => now()->addDays(7),
            'estimated_hours' => 4.0,
            'started_at' => now()->subDays(2),
            'custom_data' => [
                'tipo_servico' => 'audiencia',
                'tempo_estimado' => 4,
                'observacoes' => 'Revisar depoimentos das testemunhas.',
                'aprovado_cliente' => true,
            ],
        ]);

        ServiceOrder::create([
            'process_id' => $process2->id,
            'lawyer_id' => $lawyer2->id,
            'current_responsible_id' => $lawyer2->id,
            'number' => 'OS-2024-003',
            'title' => 'Análise Documental',
            'description' => 'Analisar documentos apresentados pela parte contrária.',
            'priority' => 'medium',
            'status' => 'pending',
            'workflow_stage' => 'assigned',
            'due_date' => now()->addDays(14),
            'estimated_hours' => 6.0,
            'custom_data' => [
                'tipo_servico' => 'analise',
                'tempo_estimado' => 6,
                'observacoes' => 'Verificar autenticidade dos documentos.',
                'aprovado_cliente' => false,
            ],
        ]);

        ServiceOrder::create([
            'process_id' => $process3->id,
            'lawyer_id' => $lawyer4->id,
            'current_responsible_id' => $lawyer4->id,
            'number' => 'OS-2024-004',
            'title' => 'Pesquisa Jurisprudencial',
            'description' => 'Pesquisar jurisprudência sobre casos similares.',
            'priority' => 'low',
            'status' => 'pending',
            'workflow_stage' => 'created',
            'due_date' => now()->addDays(21),
            'estimated_hours' => 12.0,
            'custom_data' => [
                'tipo_servico' => 'pesquisa',
                'tempo_estimado' => 12,
                'observacoes' => 'Focar em decisões do STJ.',
                'aprovado_cliente' => true,
            ],
        ]);
    }
}
