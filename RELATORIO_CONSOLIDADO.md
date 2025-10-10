# Relatório Consolidado de Ordens de Serviço

## Visão Geral

Este módulo permite gerar relatórios consolidados de múltiplas ordens de serviço, incluindo dados do processo, liquidação e relatórios de cálculo do PJeCalc.

## Acesso

No painel administrativo do Filament:
- Menu: **Relatórios** → **Relatórios Consolidados**
- URL: `/admin/consolidated-reports`

## Funcionalidades

### 1. Listagem de Ordens de Serviço

A tela principal exibe uma tabela com todas as ordens de serviço disponíveis, mostrando:

- **Nº OS** - Número da ordem de serviço
- **Nº Processo** - Número do processo vinculado
- **TRT** - Tribunal Regional do Trabalho
- **Classe** - Classe processual
- **Título** - Título da ordem de serviço
- **Status** - Status atual do workflow
- **Liquidação** - Ícone indicando se possui dados de liquidação
- **Relatório** - Ícone indicando se possui relatórios gerados
- **Criado em** - Data de criação

### 2. Filtros Disponíveis

#### Filtro por TRT
- Filtrar por um ou múltiplos TRTs
- Busca dinâmica
- Seleção múltipla

#### Filtro por Período
- **Criado de** - Data inicial
- **Criado até** - Data final
- Formato: dd/mm/aaaa

#### Filtro por Status
- Múltipla seleção de status:
  - Criada
  - Atribuída
  - Em Andamento
  - Em Revisão
  - Concluída
  - Rejeitada

#### Filtros Especiais
- **Com Liquidação** - Apenas OSs que possuem dados de liquidação
- **Com Relatório** - Apenas OSs que possuem relatórios gerados

### 3. Seleção em Massa

- **Selecionar Tudo** - Checkbox no cabeçalho da tabela
- **Seleção Individual** - Checkbox em cada linha
- **Seleção Parcial** - Selecionar apenas as OSs desejadas

### 4. Gerar Relatório Consolidado

Após selecionar as ordens de serviço:

1. Clicar no botão **"Gerar Relatório Consolidado"** (aparece quando há seleção)
2. O sistema redirecionará para a página de visualização
3. O relatório será exibido com todos os dados consolidados

## Estrutura do Relatório Consolidado

Para cada ordem de serviço selecionada, o relatório exibe:

### Cabeçalho da OS
- Número da OS
- Título
- Status (com cor diferenciada)
- Data de criação

### Dados do Processo
- Número do processo
- TRT
- Classe
- Órgão julgador
- Valor da causa
- Reclamante (primeiro da lista)
- Reclamado (primeiro da lista)

### Dados da Liquidação (se existir)

#### Informações Gerais
- Número do cálculo
- Data da liquidação
- Status
- Mensagem
- Data da última atualização

#### Valores
- **Valor Total** (destacado em verde)
- Valor Principal
- Juros
- Correção Monetária

#### Itens da Liquidação (tabela)
Para cada item:
- Descrição
- Valor
- Juros
- Correção
- Total
- Tipo (PRINCIPAL ou REFLEXO)

### Relatórios de Cálculo (se existirem)

Para cada relatório gerado:
- Número do cálculo
- Tipo de relatório
- Status
- Data de geração
- Botão "Ver" (abre o HTML em nova aba)

## Ações Disponíveis no Relatório

### Botão "Voltar"
- Retorna para a lista de ordens de serviço
- Mantém filtros aplicados

### Botão "Imprimir"
- Aciona a função de impressão do navegador
- Remove elementos de navegação na impressão
- Otimizado para papel A4

### Botão "Exportar Excel"
- Funcionalidade em desenvolvimento
- Exportará os dados em formato XLSX

## Tabelas no Banco de Dados

### service_order_reports
Armazena os relatórios gerados do PJeCalc:
- `service_order_id` - FK para service_orders
- `numero_calculo` - Número do cálculo
- `tipo_relatorio` - COMPLETO, RESUMO, DEMONSTRATIVO, etc
- `formato` - HTML ou JSON
- `status` - GERADO ou ERRO
- `data_geracao` - Timestamp da geração
- `html_content` - Conteúdo HTML completo (longText)
- `dados_estruturados` - Dados JSON
- `tamanho_bytes` - Tamanho do relatório
- `url_direta` - URL para acesso direto ao PJeCalc
- `mensagem_erro` - Mensagens de erro (se houver)

## APIs Utilizadas

### API Interna - Fetch Report
**Endpoint:** `POST /api/service-orders/fetch-report`

**Payload:**
```json
{
  "service_order_id": 1,
  "numero_calculo": "15267",
  "tipo_relatorio": "COMPLETO",
  "formato": "JSON"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Relatório gerado e salvo com sucesso",
  "data": {
    "report_id": 1,
    "service_order_id": 1,
    "numero_calculo": "15267",
    "tipo_relatorio": "COMPLETO",
    "data_geracao": "10/10/2025 09:30:00"
  }
}
```

### API Externa - PJeCalc
**Endpoint:** `POST http://calculo.emerst.com.br:9257/pjecalc/relatorio-api.jsp`

**Parâmetros:**
- `numeroCalculo` - Número do cálculo
- `tipoRelatorio` - Tipo do relatório
- `formato` - Formato da resposta

## Fluxo de Uso Típico

1. Acesse **Relatórios** → **Relatórios Consolidados**
2. Aplique filtros conforme necessário:
   - Selecione o TRT desejado
   - Defina o período (de/até)
   - Filtre por status
3. Selecione as ordens de serviço:
   - Use o checkbox do cabeçalho para selecionar tudo
   - Ou selecione individualmente
4. Clique em **"Gerar Relatório Consolidado"**
5. Visualize o relatório gerado com todos os dados
6. Opções:
   - Imprimir o relatório
   - Exportar para Excel (em desenvolvimento)
   - Visualizar HTML dos relatórios individuais
   - Voltar e selecionar outras OSs

## Observações Importantes

- Os dados de liquidação precisam ser buscados previamente na aba "Cálculo Analisado" de cada OS
- Os relatórios de cálculo precisam ser gerados na aba "Relatório Cálculo" de cada OS
- O relatório consolidado apenas agrupa e exibe os dados já existentes
- Cada OS pode ter múltiplos relatórios de cálculo associados
- Os relatórios são exibidos em ordem cronológica (mais recente primeiro)
- A funcionalidade de auto-refresh está ativa (30 segundos)

## Permissões

- Acesso disponível para todos os usuários autenticados no Filament
- Não é possível criar, editar ou deletar através deste módulo
- Apenas visualização e geração de relatórios consolidados

## Estilos e Layout

- CSS customizado em `resources/css/consolidated-report.css`
- Otimizado para impressão
- Responsivo para mobile, tablet e desktop
- Suporte a tema escuro (dark mode)
- Cores diferenciadas por seção:
  - Processo: Cinza
  - Liquidação: Azul
  - Relatórios: Verde
