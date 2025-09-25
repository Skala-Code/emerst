# Sistema de Gerenciamento de Tasks

Este sistema organiza as tasks dos agentes TALL Stack em diferentes estágios:

## Estrutura de Pastas

### 📋 `/planejamento/`
- Tasks que estão sendo analisadas e planejadas
- Arquivos com escopo inicial e análise de requisitos
- Formato: `YYYY-MM-DD_HHMMSS_nome-do-projeto.md`

### ⚠️ `/em_andamento/`
- Tasks que estão sendo executadas ativamente
- Arquivos com progresso detalhado e status atual
- Movidas automaticamente de `/planejamento/` quando iniciadas

### ✅ `/concluido/`
- Tasks finalizadas com sucesso
- Arquivos com resumo final e resultados
- Movidas automaticamente de `/em_andamento/` quando concluídas

## Formato dos Arquivos de Task

```markdown
---
projeto: nome-do-projeto
agente: tall-stack-project-planner
status: planejamento|em_andamento|concluido
criado_em: 2025-01-21T10:30:00Z
atualizado_em: 2025-01-21T10:30:00Z
---

# [Nome do Projeto]

## Escopo Original
[Descrição do que foi solicitado]

## Status Atual
[Progresso atual da task]

## Próximos Passos
[O que precisa ser feito]

## Resultados
[Quando concluído, resumo dos resultados]
```

## Fluxo de Trabalho

1. **Planejamento**: Agente analisa escopo → cria task em `/planejamento/`
2. **Execução**: Task movida para `/em_andamento/` → agentes executam
3. **Conclusão**: Task movida para `/concluido/` → resultados documentados

## Benefícios

- ✅ Tracking visual do progresso
- ✅ Histórico completo de projetos
- ✅ Facilita debugging e continuação de work
- ✅ Permite múltiplas tasks simultâneas
- ✅ Documentação automática do processo