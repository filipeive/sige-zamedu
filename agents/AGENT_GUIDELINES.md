# DIRECTRIZES DE ENGENHARIA PARA AGENTES IA (SIGE / ZAMEDU)
## `agents/AGENT_GUIDELINES.md`

Este documento define as regras inegociáveis para qualquer agente de IA (Claude Code,
ChatGPT, Cursor, Gemini, Copilot, etc.) ou humano que trabalhar no repositório
**sige-zamedu**. É o primeiro ficheiro a ler, sempre.

**Ordem de leitura obrigatória ao iniciar qualquer sessão de trabalho:**
1. Este ficheiro.
2. `agents/PROJECT_CONTEXT.md` — o que é o projeto, estado atual.
3. `agents/HANDOFF.md` — o que o agente/sessão anterior fez e deixou por fazer. **Não
   saltar este passo** — é o que torna isto uma equipa, não uma série de sessões isoladas.
4. Os documentos em `docs/architecture/` relevantes para a tarefa em mãos (ver índice em
   `PROJECT_CONTEXT.md`).

---

## 1. PAPEL DO AGENTE

Atuar como **Chief Software Architect, Laravel Architect, Database Architect,
Multi-Tenant Architect, Security Architect, Product Architect, QA Engineer e DevOps
Engineer** — não como um simples gerador de código. Cada alteração relevante é precedida
de análise arquitetural, não só de implementação.

Contexto do produto: **SIGE (ZAMEDU)** — sistema de gestão escolar que venceu a fase
distrital do Prémio Jovem Criativo (Namacurra) e está a evoluir de "sistema de uma
escola" para "plataforma multi-tenant e territorial" antes da fase provincial
(Zambézia). Ver `docs/strategy/MASTER_MANDATE.md` para a visão completa e
`docs/strategy/PROVINCIAL_PRESENTATION.md` para o que vai ser demonstrado ao júri.

## 2. REGRA FUNDAMENTAL — NÃO NEGOCIÁVEL

**NÃO REESCREVER O SISTEMA. NÃO SUBSTITUIR FUNCIONALIDADES FUNCIONAIS SEM NECESSIDADE.
NÃO FAZER MIGRAÇÃO BIG-BANG. NÃO ALTERAR comportamento existente só para "melhorar a
arquitetura".**

Antes de alterar qualquer módulo, nesta ordem: analisar a arquitetura existente →
identificar dependências, tabelas, controllers, models, policies, middleware, queries,
views, testes → documentar o impacto → só depois implementar.

## 3. REGRAS DE OURO (INVIOLÁVEIS)

1. **Nunca quebrar o fluxo de avaliação/notas já premiado.** Antes de qualquer alteração
   estrutural, correr o fluxo completo: lançar nota → `Grade::calculateMACS()` →
   `calculateMT()` → `calculateMFD()` → pauta (`grades/index.blade.php`) → boletim
   (`report-card.blade.php`). Se uma alteração muda qualquer resultado deste fluxo para
   a escola piloto, PARAR e sinalizar.

2. **Nunca misturar isolamento de escrita com visibilidade agregada.** Ver
   `docs/architecture/DATA_SCOPING.md` — é o documento de maior risco de segurança do
   projeto. CRUD normal de uma escola usa sempre `SchoolScope`/`current_school_id()`.
   Painéis de ZIP/Distrito/Província usam `HierarchyScopeResolver` e são só-leitura.

3. **Nunca remover ou contornar o `SchoolScope`** exceto num comando administrativo
   explicitamente autorizado e documentado como tal.

4. **Migrações aditivas antes de disruptivas.** `school_id` começa sempre `nullable()`.
   Só se torna `NOT NULL`/`constrained()` numa migração separada e posterior, depois do
   backfill confirmado e dos testes de isolamento passarem (ver
   `docs/architecture/MIGRATION_PLAN.md`, Sprint 4 — ponto de não retorno).

5. **Um sprint = uma branch = um PR.** Nunca commitar diretamente em `main`. Nunca
   misturar duas fases/sprints diferentes no mesmo PR. Ver `agents/BRANCH_WORKFLOW.md`.

6. **Testes antes de concluir qualquer sprint.** Testes de isolamento são obrigatórios a
   partir do Sprint 3 (`docs/architecture/SECURITY_MODEL.md` secção 4): um utilizador da
   Escola A nunca lê nem escreve dados da Escola B, por nenhuma via (UI, API, export,
   URL manipulada, relação indireta).

7. **Não avançar de módulo/sprint se houver regressão crítica.** Parar, documentar,
   resolver primeiro.

8. **Compatibilidade de base de dados.** Sem comandos raw exclusivos de um motor. Testes
   e migrações correm em qualquer motor suportado pelo projeto.

9. **Atualizar `agents/HANDOFF.md` no fim de cada sessão de trabalho** — não é opcional,
   é a regra que torna o trabalho de vários agentes/sessões coerente (ver secção 4).

10. **Nomenclatura de domínio.** `school_id`, `School`, `Zip`, `District`, `Province` —
    nunca "tenant" genérico no código do SIGE (o `ZBIZ_PLUS`, repositório irmão, usa
    "tenant" porque é um ERP genérico; o SIGE é um domínio educativo específico).

11. **Pontos de não-retorno exigem confirmação explícita do Filipe** antes de aplicar em
    qualquer ambiente com dados reais (ex: tornar `school_id` `NOT NULL`, apagar dados,
    qualquer migração destrutiva). Registar a aprovação em
    `docs/architecture/DECISIONS.md`.

12. **Código limpo, sempre.** Nomes descritivos, funções pequenas e com uma
    responsabilidade, sem duplicação de lógica de negócio (ex: o cálculo MACS/MT existe
    numa única fonte de verdade — `Grade.php` — nunca reimplementado numa view; ver
    achado já registado em `docs/architecture/MULTI_TENANCY_AUDIT.md` secção 3.5).
    Comentar o "porquê", não o "o quê" óbvio. Preferir explicitação (nomes, pequenas
    funções) a comentários extensos.

## 4. PROCESSO DE EXECUÇÃO — SPRINTS

Cada sprint (ver `docs/architecture/MIGRATION_PLAN.md` para a lista completa) segue esta
estrutura, registada no PR e resumida em `agents/HANDOFF.md`:

**Objetivo** → **Análise** (arquitetura existente relevante) → **Implementação** →
**Testes** → **Segurança** (verificação de isolamento) → **Regressão** (funcionalidades
existentes continuam a funcionar) → **Documentação** (atualizada) → **Resultado** →
**Próximo passo**.

## 5. REGRA DE OURO PESSOAL DO AGENTE (antes de qualquer alteração importante)

Responder internamente, por escrito no PR se a alteração for arquiteturalmente
relevante:
1. O que existe atualmente?
2. O que vai ser alterado?
3. Que dependências são afetadas?
4. Que dados podem quebrar?
5. Que permissões podem ser afetadas?
6. Que módulos podem sofrer regressão?
7. Como vamos testar?
8. Como podemos reverter?

Se a resposta revelar risco arquitetural significativo: **PARAR**, documentar o risco em
`docs/architecture/DECISIONS.md`, propor uma estratégia incremental. Nunca executar uma
refactorização destrutiva sem essa passagem.

## 6. STACK TECNOLÓGICO (confirmado por auditoria, não assumir versões diferentes)

* **Backend:** PHP 8.2+, Laravel 12.x
* **RBAC:** `spatie/laravel-permission` 6.21 (roles já existentes:
  `super_admin, admin, secretary, pedagogy, teacher, parent, security` — ver
  `docs/architecture/MULTI_TENANCY_AUDIT.md` secção 2 antes de criar/renomear roles)
  + middleware próprio `role`/`permission` (redundante com o Spatie — não duplicar mais)
* **Frontend:** Blade + Tailwind CSS
* **PDF/Excel:** `barryvdh/laravel-dompdf`, `maatwebsite/excel`
* **API:** `laravel/sanctum`
* **Auditoria:** `spatie/laravel-activitylog`
* **Testes:** PHPUnit (`tests/Feature`, `tests/Unit`)
* **Padrão de referência para multi-tenancy:** repositório irmão `ZBIZ_PLUS`
  (`app/Traits/BelongsToTenant.php`, `app/Scopes/TenantScope.php`,
  `app/Http/Middleware/IdentifyTenant.php`) — adaptar, não copiar cegamente: o SIGE
  precisa da camada hierárquica territorial que o ZBIZ+ não tem.
