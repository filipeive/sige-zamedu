# CONTEXTO RÁPIDO DO PROJETO SIGE (ZAMEDU) PARA AGENTES
## `agents/PROJECT_CONTEXT.md`

### 1. O que é o SIGE / ZAMEDU?
Sistema Integrado de Gestão Escolar — vencedor da fase distrital (Namacurra) do Prémio
Jovem Criativo XII Edição 2026, categoria Inovação Tecnológica. Em transformação de
"sistema de uma escola" para "Plataforma Integrada de Gestão, Monitoria e Inteligência
Educacional Multi-Tenant" antes da fase provincial (Zambézia).

Módulos já existentes e funcionais (não recriar — ver `docs/MODULES_AND_FEATURES.md`):
gestão académica, estudantes, professores, turmas, avaliações (motor MACS/MT/MFD/MF
segundo o Diploma Ministerial nº 59/2015), relatórios, finanças, comunicações
(SMS via SIM real), portais, controlo de acesso (portaria com QR), PWA, RBAC.

### 2. Estado atual — resumo (detalhe completo em `docs/architecture/MULTI_TENANCY_AUDIT.md`)
* 100% single-tenant: zero `school_id`/`tenant_id` em qualquer tabela.
* Configuração da escola vive numa tabela `settings` genérica (chave-valor, sem âmbito).
* `License::first()` assume uma única licença global — tem de ser corrigido junto com a
  introdução de `School` (ver auditoria secção 3.1).
* Já existe um fluxo de onboarding (`TrialRegisterController`, "conta de teste de 15
  dias") que já captura província/distrito como texto livre — boa base para a Fase 1/11.
* RBAC via `spatie/laravel-permission`. Roles existentes:
  `super_admin, admin, secretary, pedagogy, teacher, parent, security`.

### 3. Transformação em curso: SaaS multi-escola hierárquico
Ler nesta ordem, dentro de `docs/architecture/`:
1. `MULTI_TENANCY_AUDIT.md` — estado atual e riscos (Fase 0, já feita).
2. `MULTI_TENANCY.md` — decisão do modelo de tenant (`School`, single DB + `school_id`).
3. `TERRITORIAL_HIERARCHY.md` — `Province → District → Zip → School` (estrutura real do
   MINEDH, não inventada).
4. `DATA_SCOPING.md` — **o documento mais importante**: isolamento de escrita vs.
   visibilidade agregada territorial. Ler antes de tocar em autorização/scopes.
5. `DATA_AGGREGATION.md` — motor de indicadores e relatórios por escopo.
6. `SECURITY_MODEL.md` — camadas de segurança e matriz de testes de isolamento.
7. `MIGRATION_PLAN.md` — sprints concretos, um por branch, com critérios de saída.
8. `DECISIONS.md` — registo vivo de decisões arquiteturais (ADR). Ler o histórico antes
   de decidir algo que possa já ter sido decidido; acrescentar entradas próprias.

Contexto estratégico e de produto, em `docs/strategy/` (fornecido pelo Filipe,
preservado verbatim):
* `MASTER_MANDATE.md` — o mandato original completo, com mapeamento para os documentos
  executáveis acima.
* `VISION_AND_ARCHITECTURE.md` — visão de produto e princípios de arquitetura.
* `REPORTS_BY_ROLE.md` — o que cada perfil (professor, diretor, ZIP, distrito,
  província) deve ver nos seus relatórios/dashboards.
* `PROVINCIAL_PRESENTATION.md` — guião da apresentação ao júri; útil para entender
  **porquê** certas decisões de UX/prioridade fazem sentido (ex: o "drill-down" tem de
  funcionar de ponta a ponta porque é o momento "wow" da demo).

### 4. Continuidade entre sessões — LER SEMPRE
`agents/HANDOFF.md` — o que a última sessão/agente fez, decidiu e deixou por fazer.
Atualizar no fim de cada sessão de trabalho relevante. `agents/BRANCH_WORKFLOW.md` — como
nomear branches e estruturar PRs.

### 5. Principais diretórios
* `app/Models/Grade.php` — motor de cálculo académico, **não alterar a fórmula**.
* `app/Http/Controllers/GradeController.php` — pautas, boletins, relatórios de turma.
* `resources/views/grades/` e `resources/views/pautas/` — UI da avaliação (usada na demo
  distrital e provincial).
* `app/Http/Middleware/LicenseCheckMiddleware.php` — a corrigir junto com `School`
  (auditoria secção 3.1).
* `app/Http/Controllers/TrialRegisterController.php` — onboarding atual, a evoluir na
  Fase 11/Sprint 10 do plano de migração.
* `docs/` — ver árvore completa acima.
* `agents/` — este ficheiro + `AGENT_GUIDELINES.md` (ler primeiro) + `HANDOFF.md` +
  `BRANCH_WORKFLOW.md`.

### 6. Repositório irmão de referência
`github.com/filipeive/ZBIZ_PLUS` — mesmo autor, já tem multi-tenancy single-database
funcional (`BelongsToTenant`/`TenantScope`/`IdentifyTenant`) e uma pasta `agents/`
equivalente. Usar como referência de padrão de código, não copiar a estrutura de
hierarquia (o ZBIZ+ é plano: Tenant→Branch; o SIGE precisa de 4 níveis territoriais,
ver `TERRITORIAL_HIERARCHY.md`).
