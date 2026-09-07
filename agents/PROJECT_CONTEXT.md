# CONTEXTO RÁPIDO DO PROJETO SIGE (ZAMEDU) PARA AGENTES
## `agents/PROJECT_CONTEXT.md`

### 1. O que é o SIGE / ZAMEDU?
Sistema Integrado de Gestão Escolar & Inteligência Educacional — vencedor da fase distrital (Namacurra) do Prémio Jovem Criativo XII Edição 2026, categoria Inovação Tecnológica. Plataforma Integrada de Gestão, Monitoria e Inteligência Educacional Multi-Tenant preparada para a fase provincial (Zambézia) e implantação nacional.

Módulos existentes e totalmente funcionais:
- Gestão académica, estudantes, professores, turmas, avaliações (motor MACS/MT/MFD/NFD segundo o Diploma Ministerial nº 59/2015 e Lei 18/2018 SNE).
- Relatórios pedagógicos com desagregação por género (**HM** / **M**).
- Finanças (propinas, taxas, despesas, resultado líquido, recibos PDF).
- Comunicações (SMS em massa com tags dinâmicas).
- Portaria Digital com controlo de acessos por QR Code / Barcode (`/gatekeeper`).
- Assistente IA (`ZAMEDU AI Assistant`), PWA e RBAC completo.

### 2. Estado Atual da Arquitetura — Multi-Tenant Territorial (CONCLUÍDO)
- **100% Multi-Tenant Hierárquico (Província -> Distrito -> ZIP -> Escola)**:
  - 28 tabelas com `school_id` e integridade referencial com chaves estrangeiras (`foreignId('school_id')`).
  - Trait `BelongsToSchool` e `SchoolScope` aplicados em todos os modelos Eloquent de domínio.
  - `HierarchyScopeResolver` para leitura agregada e monitoria territorial por Inspectores e Coordenadores de ZIP.
  - Onboarding Wizard autónomo de novas escolas (`/onboarding/school`).
  - Suíte de 94 testes automatizados em PHPUnit (100% PASS).

### 3. Modelo Territoral Hierárquico MINEDH
Ler os documentos em `docs/architecture/`:
1. `MULTI_TENANCY.md` — Decisão do modelo de tenant (`School`, single DB + `school_id`).
2. `TERRITORIAL_HIERARCHY.md` — `Province → District → Zip → School` (estrutura real do MINEDH).
3. `DATA_SCOPING.md` — **Isolamento de escrita vs. visibilidade agregada territorial**.
4. `DATA_AGGREGATION.md` — Motor de indicadores e relatórios por escopo.
5. `SECURITY_MODEL.md` — Camadas de segurança e matriz de testes de penetração cross-school (`MultiTenantSecurityTest`).
6. `MIGRATION_PLAN.md` — Registo dos 10 Sprints concluídos.
7. `DECISIONS.md` — Registo vivo de decisões arquiteturais (ADR).

### 4. Continuidade entre sessões — LER SEMPRE
`agents/HANDOFF.md` — o que a última sessão/agente fez, decidiu e deixou por fazer.
Atualizar no fim de cada sessão de trabalho relevante. `agents/BRANCH_WORKFLOW.md` — como nomear branches e estruturar PRs.

### 5. Principais Diretórios & Ficheiros
* `app/Models/Grade.php` — motor de cálculo académico SNE, **não alterar as fórmulas (`calculateMACS`, `calculateMT`, `calculateMFD`)**.
* `app/Models/Scopes/SchoolScope.php` & `app/Traits/BelongsToSchool.php` — isolamento global de escolas.
* `app/Services/HierarchyScopeResolver.php` — escopo de leitura agregada territorial.
* `app/Http/Controllers/TerritorialDashboardController.php` & `TerritorialAggregationController.php` — dashboards de inspeção e mapa pedagógico MINEDH.
* `app/Http/Controllers/SchoolOnboardingController.php` — assistente de onboarding multi-escola.
* `resources/views/territorial/` — interfaces do dashboard e mapa pedagógico.
* `resources/views/onboarding/` — wizard de registo de novas escolas.
* `docs/` — documentação técnica e de arquitetura.
* `agents/` — diretrizes, contexto e diário de bordo (`HANDOFF.md`).
