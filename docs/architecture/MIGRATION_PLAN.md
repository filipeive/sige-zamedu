# MIGRATION_PLAN.md
## Plano de Migração — Sprints, Branches e Critérios de Saída

Pré-requisito de leitura: todos os documentos em `docs/architecture/`. Este documento
sequencia as 16 fases do mandato (`agents/AGENT_GUIDELINES.md` / documento mestre) em
sprints concretos e executáveis, cada um numa branch própria.

**Regra fixa em todo este plano:** um sprint = uma branch = um PR. Nunca misturar duas
fases no mesmo PR. Nunca commitar diretamente em `main`. Ver `agents/BRANCH_WORKFLOW.md`
para o formato exato de cada PR/sprint.

---

## Sprint 0 — Fundação documental (ESTA branch: `docs/multi-tenant-foundations`)
**Estado: em curso.**
- Auditoria (`MULTI_TENANCY_AUDIT.md`), documentos de arquitetura, pasta `agents/`.
- Nenhum código de produção alterado (exceto a correção pontual da view
  `grades/grade-sheet.blade.php`, já registada como não relacionada em
  `MULTI_TENANCY_AUDIT.md` secção 3.4).
- **Critério de saída:** Filipe revê e aprova os documentos antes de qualquer sprint de
  código começar.

## Sprint 1 — Modelo territorial (branch `feature/territorial-model`)
Fase 1 do mandato.
- Migrations + models: `Province`, `District`, `Zip`, `School` (nullable `zip_id` em
  `schools` só se necessário; caso contrário `NOT NULL` desde o início, já que `schools`
  é uma tabela nova, não existente — não há dados antigos a proteger aqui).
- Seed real: Província da Zambézia + distritos reais + pelo menos 1 ZIP real de
  Namacurra (ver `TERRITORIAL_HIERARCHY.md` secção 4).
- **Critério de saída:** `php artisan migrate:fresh --seed` cria a hierarquia territorial
  correta; testes de relação (`Province hasMany District`, etc.) passam.

## Sprint 2 — School como tenant + colunas aditivas (branch `feature/school-tenant-columns`)
Fase 2 do mandato.
- Adicionar `school_id` **nullable** a todas as tabelas listadas na tabela da secção 4
  de `MULTI_TENANCY_AUDIT.md`.
- Comando `schools:backfill` — cria (ou associa a) uma `School` representando a escola
  piloto e preenche `school_id` em todos os registos existentes.
- Corrigir `LicenseCheckMiddleware`/`licenses` conforme `MULTI_TENANCY.md` secção 4
  (nesta mesma branch, porque estão diretamente ligados).
- **Critério de saída:** `schools:backfill` corre sem erros sobre uma cópia dos dados
  reais da escola piloto; `php artisan test` passa 100%; nenhuma coluna ainda é
  obrigatória, logo nada quebra para quem já usa o sistema.

## Sprint 3 — Mecanismo de isolamento (branch `feature/school-scoping`)
Fase 3 e parte da Fase 4 do mandato.
- `App\Traits\BelongsToSchool`, `App\Scopes\SchoolScope`, `App\Services\SchoolContext`,
  middleware `IdentifySchool`.
- Aplicar a trait aos models de negócio, um grupo de cada vez, seguindo a ordem de
  prioridade da Fase 11 do mandato (Users → Schools → Students → Teachers → Classes →
  Academic records → Attendance → Finance → Communications → Reports → Portais → APIs →
  Exports).
- Testes de isolamento básicos (ver `SECURITY_MODEL.md` secção 4) para cada grupo migrado
  antes de avançar para o próximo — **não avançar se houver regressão crítica** (regra
  explícita do mandato, Fase 11).
- **Critério de saída:** todo o fluxo de pauta/notas (o que foi demonstrado e premiado)
  continua a funcionar de forma idêntica para a escola piloto; testes de isolamento
  passam para todos os grupos migrados nesta fase.

## Sprint 4 — `school_id` obrigatório (branch `feature/school-id-not-null`)
Fase 3 (fecho). **Ponto de não-retorno — exige backup e confirmação explícita do Filipe
antes de aplicar em qualquer ambiente com dados reais** (ver `agents/AGENT_GUIDELINES.md`
regra 10).
- Migração que torna `school_id` `NOT NULL` + `foreignId()->constrained()` em todas as
  tabelas já migradas no Sprint 3.
- **Critério de saída:** aprovação explícita do Filipe registada em `DECISIONS.md` antes
  do merge.

## Sprint 5 — Roles e Scopes territoriais (branch `feature/territorial-roles`)
Fase 5 do mandato + `DATA_SCOPING.md`.
- Novos roles Spatie: `zip_admin`, `district_admin`, `province_admin` (nomes a confirmar
  contra os já existentes — ver `MULTI_TENANCY_AUDIT.md` secção 2).
- `HierarchyScopeResolver`.
- **Critério de saída:** testes confirmando que cada role territorial só resolve os
  `school_id`s dentro do seu próprio scope (ZIP/Distrito/Província).

## Sprint 6 — Dashboards territoriais (branch `feature/territorial-dashboards`)
Fase 6 do mandato.
- Dashboard de Escola (já existe, confirmar que continua igual), ZIP, Distrito,
  Província — conteúdo conforme `docs/strategy/REPORTS_BY_ROLE.md`.
- **Critério de saída:** os 4 dashboards mostram dados corretos e coerentes usando dados
  reais/demo claramente identificados (ver `TERRITORIAL_HIERARCHY.md` secção 4).

## Sprint 7 — Motor de agregação e drill-down (branch `feature/aggregation-drilldown`)
Fases 7 e 8 do mandato + `DATA_AGGREGATION.md`.
- Camada de indicadores agregados (Sprint separado do Sprint 6 porque os dashboards da
  Fase 6 podem começar com queries simples; este sprint introduz o motor reutilizável de
  `Report Definition` descrito em `DATA_AGGREGATION.md`).
- Drill-down Província → Distrito → ZIP → Escola → Turma, com verificação de autorização
  em cada passo.
- **Critério de saída:** o "momento wow" da apresentação
  (`docs/strategy/PROVINCIAL_PRESENTATION.md` secção 7) funciona de ponta a ponta.

## Sprint 8 — Testes de segurança multi-tenant completos (branch `feature/tenant-security-tests`)
Fase 12 do mandato. Cobre a matriz completa de `SECURITY_MODEL.md` secção 4 para todos
os módulos migrados, incluindo manipulação de URL, API e exports.
- **Critério de saída:** suite completa de testes de isolamento verde, incluindo
  isolamento administrativo (ZIP/Distrito/Província).

## Sprint 9 — Performance (branch `feature/tenant-performance`)
Fase 13 do mandato — índices, eager loading, paginação nos pontos identificados em
`DATA_AGGREGATION.md` secção 6.

## Sprint 10 — Onboarding multi-escola (branch `feature/school-onboarding`, opcional
pós-apresentação provincial)
Fase 11 (fecho) + reescrita do `TrialRegisterController` (ver `MULTI_TENANCY_AUDIT.md`
secção 3.2) para criar de facto uma `School` associada a um `Zip` real, em vez de uma
"nova instalação" mental.

---

## Regra de sequenciamento

Não iniciar um sprint sem o anterior estar mergeado e com os seus critérios de saída
cumpridos. Se, ao trabalhar num sprint, um agente descobrir que uma fase anterior ficou
incompleta, **parar, documentar em `DECISIONS.md`, e resolver isso antes de continuar** —
não empilhar dívida técnica de tenancy, é precisamente o tipo de dívida mais caro de
corrigir depois.
