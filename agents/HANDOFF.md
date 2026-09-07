# HANDOFF.md
## Registo de Continuidade Entre Sessões e Agentes

**Regra de uso:** todo agente (IA ou humano) lê a entrada mais recente ANTES de começar
a trabalhar, e acrescenta uma nova entrada no TOPO (mais recente primeiro) antes de
terminar a sessão. Nunca apagar entradas antigas — isto é um diário de bordo, não um
estado atual reescrito. Se uma entrada antiga ficar desatualizada, a entrada nova diz
isso explicitamente ("Nota: a Sprint 2 mencionada abaixo foi revista, ver
`DECISIONS.md` de 2026-XX-XX").

Formato de cada entrada:
```
## [DATA] — [Sprint/Branch] — [Agente/Autor]
**Feito:** ...
**Decisões tomadas:** (com link para a entrada correspondente em DECISIONS.md, se aplicável)
**Testado:** ...
**Não feito / bloqueado:** ...
**Próximo passo recomendado:** ...
```

## 2026-09-07 — Sprint 5 (`feature/territorial-roles`) — Antigravity AI Agent

**Feito:**
- **Papéis Territoriais Spatie & Scope Resolver (`feature/territorial-roles`)**:
  - **Branch**: `feature/territorial-roles`.
  - **Migração Concluída (`2026_09_07_000012_add_territorial_columns_to_users_table.php`)**: Adicionou as colunas chave estrangeira nulas `province_id`, `district_id`, `zip_id` na tabela `users`.
  - **Model User (`app/Models/User.php`)**: Atualizado com `$fillable` (`province_id`, `district_id`, `zip_id`) e relacionamentos Eloquent (`province()`, `district()`, `zip()`).
  - **Serviço `HierarchyScopeResolver` (`app/Services/HierarchyScopeResolver.php`)**: Criado serviço centralizado para determinar a lista de `school_ids` visíveis por utilizador conforme a sua hierarquia territorial (`super_admin` -> `null` global; `province_inspector` -> escolas da província; `district_inspector` -> escolas do distrito; `zip_coordinator` -> escolas do ZIP; utilizador escolar -> `[school_id]`).
  - **Seeder de Permissões e Roles (`database/seeders/PermissionSeeder.php` e `TerritorialRolesSeeder.php`)**: Adicionadas permissões territoriais (`view_territorial_dashboard`, `view_province_reports`, `view_district_reports`, `view_zip_reports`), roles territoriais Spatie (`province_admin`, `province_inspector`, `district_admin`, `district_inspector`, `zip_coordinator`, `zip_admin`) e utilizadores de teste territoriais associados à Zambézia/Namacurra.
  - **Testes de Funcionalidade (`tests/Feature/TerritorialRolesTest.php`)**: Suite completa de 5 testes unitários/funcionais validando atribuição de papéis Spatie e resolução hierárquica por escopo.

**Decisões tomadas:**
- `HierarchyScopeResolver` é o serviço único e desacoplado para queries de relatórios e painéis territoriais de leitura agregada (sem misturar com `SchoolScope` de escrita operacional das escolas).

**Testado:**
- `vendor/bin/phpunit tests/Feature/TerritorialRolesTest.php` — 100% PASS (5 testes, 20 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (73 testes, 248 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 6 (`feature/territorial-dashboards`)**: Implementação dos painéis e rotas para visualização de métricas de ZIP, Distrito e Província.

---

## 2026-09-07 — Sprint 4 (`feature/school-id-not-null`) — Antigravity AI Agent

**Feito:**
- **Tornar `school_id` NOT NULL (Ponto de Não Retorno do Multi-Tenancy)**:
  - **Branch**: `feature/school-id-not-null`.
  - **Migração Concluída (`2026_09_07_000011_make_school_id_not_null_on_domain_tables.php`)**: Alterou a restrição nula da coluna `school_id` para `NOT NULL` em 27 tabelas do domínio de negócio (`students`, `teachers`, `classes`, `subjects`, `class_schedules`, `class_subjects`, `enrollments`, `enrollment_applications`, `enrollment_documents`, `grades`, `assessments`, `attendances`, `payments`, `expenses`, `fee_types`, `financial_categories`, `communications`, `conversations`, `messages`, `sms_logs`, `sms_inbox`, `events`, `gate_logs`, `staff_leave_requests`, `material_lists`, `settings`, `licenses`).
  - **Auditoria & Migração Segura**: Incluído salvaguarda de backfill automático previo na migração para garantir que qualquer registo órfão residual seja associado à escola padrão antes da alteração de constraint.
  - **Coluna `users.school_id`**: Mantida `nullable()` intencionalmente para utilizadores territoriais (Inspectores ZIP/Distrito/Província e Super Admins).

**Decisões tomadas:**
- A coluna `school_id` é agora estritamente obrigatória a nível de motor de base de dados (DB constraint `NOT NULL`).
- Tentativas de inserção de dados do domínio sem `school_id` e sem `SchoolContext` ativo agora falham a nível de BD (`SQLSTATE[23000]`).

**Testado:**
- `vendor/bin/phpunit --filter=SchoolIdNotNullTest` — 100% PASS (2 testes, 3 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (68 testes, 228 asserções).

**Próximo passo recomendado:**
1. Iniciar **Sprint 5 (`feature/territorial-roles`)**: Implementação dos papéis e permissões territoriais Spatie para Província, Distrito, ZIP e Escola.

---

## 2026-09-07 — Sprint 3 (`feature/school-scoping`) — Antigravity AI Agent

**Feito:**
- **Mecanismo de Isolamento Multi-Tenant & Scoping Eloquent Concluído**:
  - **Branch**: `feature/school-scoping`.
  - **`App\Services\SchoolContext`**: Criado serviço mestre de gestão do contexto de escola ativa com suporte a `setSchoolId()`, `getSchoolId()`, `hasSchoolContext()`, `clear()` e `runInContext()`, com auto-cura de verificação de existência no banco de dados para evitar inconsistências em execuções de testes ou plano de fundo.
  - **`App\Scopes\SchoolScope`**: Criado Eloquent Global Scope com filtro automático `where('table.school_id', activeSchoolId)` evitando colisão de colunas em JOINs.
  - **`App\Traits\BelongsToSchool`**: Criada Trait universal com atribuição automática do `school_id` no evento `creating`, relacionamento `$model->school()`, e métodos de bypass de escopo (`scopeForSchool`, `scopeWithoutSchoolScope`).
  - **`App\Http\Middleware\IdentifySchool`**: Registado em `Kernel.php` (grupos `web` e `api`) para identificar automaticamente o contexto da escola via `user->school_id`, cabeçalho `X-School-Id` ou sessão ativa.
  - **Modelos Atualizados (28 Tabelas)**: `User`, `Student`, `Teacher`, `ClassRoom`, `Subject`, `Grade`, `Attendance`, `Enrollment`, `Payment`, `Expense`, `FeeType`, `FinancialCategory`, `Communication`, `Event`, `GateLog`, `StaffLeaveRequest`, `MaterialList`, `EnrollmentApplication`, `EnrollmentDocument`, `Assessment`, `ClassSchedule`, `ClassSubject`, `Conversation`, `Message`, `SmsLog`, `SmsInbox`, `Setting`, `License`.
  - **`TrialRegisterController`**: Integrado para instanciar e vincular automaticamente a nova escola no Modelo Territorial (`Province` / `District`).

**Decisões tomadas:**
- A auto-cura do `SchoolContext::hasSchoolContext()` valida se a escola existe no banco antes de aplicar o escopo, garantindo retrocompatibilidade com scripts CLI, super administradores e instalações limpas.
- Mantido o `school_id` como `nullable()` na BD até o Sprint 4 (Ponto de Não Retorno).

**Testado:**
- `vendor/bin/phpunit --filter=SchoolScopingTest` — 100% PASS (5 testes, 11 asserções).
- Suite completa `vendor/bin/phpunit` — 100% PASS (66 testes, 225 asserções).

**Não feito / bloqueado:**
- Alteração da coluna `school_id` para `NOT NULL` reservada para o **Sprint 4 (`feature/school-id-not-null`)**.

**Próximo passo recomendado:**
1. Iniciar **Sprint 4 (`feature/school-id-not-null`)**: Ponto de não-retorno onde a coluna `school_id` passa a `NOT NULL` após verificação final de integridade de dados.

---

## 2026-09-07 — Sprint 2 (`feature/school-tenant-columns`) — Antigravity AI Agent

**Feito:**
- **Colunas Aditivas `school_id` (Nullable) & Backfill de Tenancy Concluídos**:
  - **Branch**: `feature/school-tenant-columns`.
  - **Migrações Aditivas**:
    - `2026_09_07_000005_add_school_id_to_academic_tables.php` (`students`, `teachers`, `classes`, `subjects`, `class_schedules`, `class_subjects`)
    - `2026_09_07_000006_add_school_id_to_enrollments_tables.php` (`enrollments`, `enrollment_applications`, `enrollment_documents`)
    - `2026_09_07_000007_add_school_id_to_grades_and_attendances_tables.php` (`grades`, `assessments`, `attendances`)
    - `2026_09_07_000008_add_school_id_to_financial_tables.php` (`payments`, `expenses`, `fee_types`, `financial_categories`)
    - `2026_09_07_000009_add_school_id_to_communication_and_support_tables.php` (`communications`, `conversations`, `messages`, `sms_logs`, `sms_inbox`, `events`, `gate_logs`, `staff_leave_requests`, `material_lists`, `users`)
    - `2026_09_07_000010_add_school_id_to_settings_and_licenses_tables.php` (`settings`, `licenses`)
  - **Comando Artisan `schools:backfill`**: Criado e executado com sucesso sobre a base de dados do sistema — **15.113 registos associados** à escola piloto *Escola Comunitária Escola da Paz* (`ESG-PAZ`) sem qualquer perda ou quebra de dados.
  - **`LicenseCheckMiddleware`**: Refatorado para consultar e avaliar licenças por `school_id` com fallback gracioso.

**Decisões tomadas:**
- Todas as colunas `school_id` permanecem `nullable()` nesta fase (Sprint 2) para garantir zero paragens e 100% de retrocompatibilidade com código legado antes do Sprint 4.
- Registada a execução limpa de `php artisan schools:backfill` sobre 15.113 registos existentes.

**Testado:**
- `vendor/bin/phpunit --filter=SchoolTenantColumnsTest` — 100% PASS (2 testes, 34 asserções).
- `TerritorialModelTest`, `SNEPedagogicalDocTest`, `RoleAccessAndWorkflowTest`, `MenuVisibilityTest` — 100% PASS.

**Não feito / bloqueado:**
- Trait `BelongsToSchool` e `SchoolScope` reservados para o **Sprint 3 (`feature/school-scoping`)**.

**Próximo passo recomendado:**
1. Iniciar **Sprint 3 (`feature/school-scoping`)**: Criar a Trait `BelongsToSchool`, o `SchoolScope`, o `SchoolContext` service e o middleware `IdentifySchool` para isolamento de dados de leitura/escrita.

---

## 2026-09-06 — Sprint 1 (`feature/territorial-model`) — Antigravity AI Agent

**Feito:**
- **Modelo Territorial Hierárquico de Moçambique Criado**:
  - Migrações: `2026_09_07_000001_create_provinces_table.php`, `2026_09_07_000002_create_districts_table.php`, `2026_09_07_000003_create_zips_table.php`, `2026_09_07_000004_create_schools_table.php`.
  - Eloquent Models: `App\Models\Province`, `App\Models\District`, `App\Models\Zip`, `App\Models\School` com relacionamentos completos (`hasMany`, `belongsTo`, `hasManyThrough`).
  - Seeder Real (`TerritorialSeeder`): Povoamento das Províncias (Zambézia, Maputo, Sofala, Nampula), Distritos (Namacurra, Nicoadala, Quelimane, Mocuba), ZIPs da região (ZIP Sede, Malei, Macuse) e Escolas Piloto (Escola Secundária de Namacurra, Escola Comunitária Escola da Paz, EPC de Namacurra).
  - Integrado em `DatabaseSeeder.php` como seeder core/estrutural.

**Decisões tomadas:**
- A hierarquia `Province` $\rightarrow$ `District` $\rightarrow$ `Zip` $\rightarrow$ `School` segue estritamente a especificação de `docs/architecture/TERRITORIAL_HIERARCHY.md`.
- `zip_id` na tabela `schools` é `nullable()` para acomodar escolas sem ZIP formal atribuído.

**Testado:**
- `vendor/bin/phpunit --filter=TerritorialModelTest` — 100% PASS (2 testes, 13 asserções).
- `SNEPedagogicalDocTest`, `RoleAccessAndWorkflowTest`, `MenuVisibilityTest` — 100% PASS.

**Não feito / bloqueado:**
- Nenhuma coluna `school_id` foi adicionada ainda às tabelas existentes (reservado para o **Sprint 2**).

**Próximo passo recomendado:**
1. Iniciar **Sprint 2 (`feature/school-tenant-columns`)**: Adicionar coluna `school_id` `nullable()` às tabelas de negócio (`students`, `classes`, `grades`, `attendances`, `payments`, etc.) + comando `schools:backfill`.

---

## 2026-09-06 — Implementação SNE MINEDH + Preparação Sprint 1 — Antigravity AI Agent

**Feito:**
- **Módulos Oficiais MINEDH (Lei 18/2018 SNE)** totalmente implementados:
  1. **Mapa de Aproveitamento Pedagógico por Género (`/reports/pedagogical-map`)**: Tabela estatística com desagregação por disciplina em **HM (Total)** e **M (Mulheres)** (Avaliados, Positivas, Negativas, % Aproveitamento) + Exportação CSV/Excel.
  2. **Pauta de Exames com Júris (`/pautas/exame/{class}`)**: Suporte oficial a turmas de Exame Nacional (9ª, 10ª e 12ª classes) com cálculo de $MAF$, $NE\_Eficaz = \max(NE, ACF)$ e $NFD = \text{round}\left(\frac{MAF + NE\_Eficaz}{2}\right)$, modal de atribuição de Júris (Presidente, 1º e 2º Vogais) e termo de homologação com assinaturas.
  3. **Certificado Oficial da 12ª e 10ª Classe com Notas por Extenso (`/certificates/{student}/official`)**: Emissão em PDF com conversão de notas numéricas em prosa oficial em português (ex: *Catorze valores*, *Quinze valores e cinco décimas*), moldura dupla e marcas oficiais MINEDH.
  4. **Navegação & Endpoints**: Adicionados os links de acesso em todas as vistas principais (Sidebar em *Avaliações & Documentos*, Central de Relatórios `/reports`, Ficha da Turma `/classes/{id}`, Índice de Certificados `/certificates` e botões de cabeçalho de pautas).
  5. **Modal de Regras de Cálculo (`calculation-modal.blade.php`)**: Atualizado com cartão explicativo das fórmulas oficiais do SNE para Exames Nacionais.
- **Preparação da Fundação Multi-tenant**:
  - Incorporada e verificada a branch `docs/multi-tenant-foundations` a partir de `sige-multi-tenant-foundations.bundle`.
  - Criado o ficheiro mestre de orientações para agentes em `.agents/AGENTS.md`.

**Decisões tomadas:**
- Aprovado o Plano de Implementação SNE e o Roadmap de 10 Sprints Multi-Tenant.
- Os modelos de documentos SNE mantêm compatibilidade total com o motor de cálculo existente (`Grade::calculateMACS()`, `calculateMT()`, `calculateMFD()`).

**Testado:**
- `vendor/bin/phpunit --filter=SNEPedagogicalDocTest` — 100% PASS (4 testes, 13 asserções).
- `RoleAccessAndWorkflowTest`, `MenuVisibilityTest` — 100% PASS.
- Inspeção visual de páginas e modais realizada via subagente de navegação.

**Não feito / bloqueado:**
- Próximo passo direto: Iniciar o **Sprint 1 (`feature/territorial-model`)** para criação da hierarquia territorial (`Province`, `District`, `Zip`, `School`).

**Próximo passo recomendado:**
1. Criar branch `feature/territorial-model` (Sprint 1 do `MIGRATION_PLAN.md`).
2. Criar migrations e models: `Province`, `District`, `Zip`, `School`.
3. Adicionar seeds reais para a Província da Zambézia, Distrito de Namacurra e ZIPs.

---

## 2026-09-06 — Sprint 0 (`docs/multi-tenant-foundations`) — Claude (chat, a pedido do Filipe)

**Feito:**
- Auditoria completa da Fase 0 do mandato: `docs/architecture/MULTI_TENANCY_AUDIT.md`,
  baseada em leitura direta do código (não suposições).
- Criados todos os documentos de arquitetura exigidos pela Fase 15 do mandato:
  `MULTI_TENANCY.md`, `TERRITORIAL_HIERARCHY.md`, `DATA_SCOPING.md`,
  `DATA_AGGREGATION.md`, `SECURITY_MODEL.md`, `MIGRATION_PLAN.md`, `DECISIONS.md`.
- Copiados os 3 documentos estratégicos do Filipe para `docs/strategy/`
  (`VISION_AND_ARCHITECTURE.md`, `REPORTS_BY_ROLE.md`, `PROVINCIAL_PRESENTATION.md`) e o
  mandato mestre completo para `docs/strategy/MASTER_MANDATE.md`.
- Reescritos `agents/AGENT_GUIDELINES.md` e `agents/PROJECT_CONTEXT.md` para refletirem
  a árvore de documentação final e consolidarem as regras do mandato mestre.
- Criado este ficheiro (`agents/HANDOFF.md`) e `agents/BRANCH_WORKFLOW.md`.
- Correção pontual não relacionada com tenancy: `resources/views/grades/grade-sheet.blade.php`
  estava em falta (rota `GradeController@gradeSheet` apontava para uma view inexistente)
  — criada, seguindo o padrão visual já usado em `batch-create.blade.php`. Não estava
  ligada a nenhum link na interface, logo não era um bug visível na demo distrital, mas
  ficaria visível se alguém acedesse à URL diretamente.

**Decisões tomadas:** ver as 3 entradas de 2026-09-06 em `docs/architecture/DECISIONS.md`
(modelo single-DB + `school_id`; hierarquia de 4 níveis; separação isolamento/agregação).

**Testado:** Nada de código de produção foi alterado nesta sessão além da view em falta
(que não tem lógica de negócio nova, só reutiliza dados já existentes). Não foram
corridos testes automatizados porque não há ainda código de tenancy a testar.

**Não feito / bloqueado:**
- Nenhum código de Sprint 1 em diante foi escrito. Esta sessão é 100% Sprint 0
  (fundação documental), conforme a regra "um sprint = uma branch = um PR".
- Esta branch (`docs/multi-tenant-foundations`) **não foi enviada para o GitHub** — foi
  criada e commitada localmente no ambiente onde o Claude trabalhou, mas o Claude não
  tem credenciais para fazer `git push` para o repositório do Filipe. **O Filipe precisa
  de aplicar esta branch manualmente** (instruções dadas na conversa em que este trabalho
  foi produzido) antes de qualquer agente (incluindo Claude Code) poder continuar a
  partir daqui.
- Os nomes finais dos roles territoriais (`zip_admin`, `district_admin`,
  `province_admin`) ainda não foram confirmados com o Filipe — ficou como item aberto em
  `DATA_SCOPING.md` secção 5.

**Próximo passo recomendado:**
1. Filipe aplica esta branch no repositório real e revê os documentos.
2. Só depois: iniciar Sprint 1 (`feature/territorial-model`) — ver
   `docs/architecture/MIGRATION_PLAN.md`. O primeiro agente a pegar nisso deve começar
   por ler este ficheiro, `AGENT_GUIDELINES.md` e `MIGRATION_PLAN.md`, por esta ordem.
