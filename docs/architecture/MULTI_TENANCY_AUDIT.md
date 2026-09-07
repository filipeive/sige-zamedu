# MULTI_TENANCY_AUDIT.md
## Auditoria de Arquitetura — Fase 0 (obrigatória antes de qualquer implementação)

Data: 2026-09-06
Âmbito: repositório `sige-zamedu`, tag/estado imediatamente após a vitória na fase
distrital do Prémio Jovem Criativo (Namacurra).
Método: leitura direta do código-fonte (models, controllers, migrations, middleware,
seeders, testes) — não é uma auditoria por amostragem ou por documentação secundária.

Esta auditoria satisfaz a exigência da Fase 0 do mandato (`docs/architecture/DECISIONS.md`
regista a decisão de prosseguir com base nela). **Nenhuma implementação de multi-tenancy
foi feita nesta fase**, conforme exigido.

---

## 1. Arquitetura atual (resumo executivo)

O SIGE é hoje uma aplicação Laravel 12 **single-tenant por instalação**: cada escola
corre a sua própria base de dados. Não existe nenhum conceito de tenant, escola-como-entidade,
ou hierarquia territorial no código. Isto é confirmado por:

- **37 models, 49 controllers, 57 migrations, 196 views, 15 ficheiros de teste** — busca
  exaustiva por `school_id` ou `tenant_id` em `app/`, `database/migrations/` e testes:
  **zero ocorrências**.
- Configuração da "escola" vive numa tabela `settings` (chave-valor global, sem qualquer
  âmbito) — `App\Models\Setting::get('school_name')`, `Setting::get('school_province')`, etc.
- `App\Models\License` licencia a instalação inteira (`client_name`, `license_key`, datas
  de expiração) — não licencia uma escola dentro de uma base partilhada.

## 2. Modelo de autenticação e RBAC

- Autenticação: Laravel padrão (`Auth`) + Laravel Sanctum para tokens de API.
- RBAC: `spatie/laravel-permission` (`^6.21`), via trait `HasRoles` no `User`.
- Roles atualmente seedados (`database/seeders/PermissionSeeder.php`):
  `super_admin`, `admin`, `secretary` (secretaria), `pedagogy` (coordenação pedagógica),
  `teacher`, `parent`, `security` (portaria).
  **Não existem ainda** `director`, `student`, nem qualquer role territorial
  (`zip_admin`, `district_admin`, `province_admin`).
- Existe também um `RoleMiddleware` e `CheckPermission` **próprios da aplicação**,
  paralelos aos que o próprio Spatie já fornece — redundância a resolver ou documentar
  intencionalmente na Fase 5 (Roles e Scopes), para não duplicar lógica de autorização.
- `Permission.php` é um model próprio da app — confirmar se coexiste com o model
  `Permission` do Spatie sem colisão de nomes/tabelas antes de mexer em RBAC.

## 3. Achados críticos com risco directo para a migração multi-tenant

### 3.1 `LicenseCheckMiddleware` assume uma única licença global
```php
$license = License::first(); // não filtra por escola
```
Em modo multi-tenant, isto teria de resolver a licença **da escola do contexto atual**,
não a primeira licença da tabela. Risco: **alto** — se não for corrigido antes da Fase 3
(Data Isolation), uma escola pode ficar bloqueada/desbloqueada pelo estado da licença de
outra escola. Ação recomendada: tratar na mesma fase em que `School` for criado (Fase 1/2
do mandato), migrando `licenses` para `school_id` nullable e adaptando o middleware a
resolver pelo `SchoolContext` em vez de `first()`.

### 3.2 Fluxo de onboarding já existe — e já captura dados territoriais como texto livre
`App\Http\Controllers\TrialRegisterController` (rota `public.trial-register`) já cria uma
"conta de teste de 15 dias" e grava:
```php
Setting::set('school_province', $validated['province'] ?? 'Maputo');
Setting::set('school_district', $validated['district'] ?? '');
```
**Boa notícia:** a intenção de capturar província/distrito já existe no produto — não é
preciso inventar esse conceito do zero, só torná-lo relacional (`province_id`,
`district_id`, `zip_id` em vez de texto livre) e ligar à nova tabela `schools`.
**Risco:** este fluxo cria hoje uma "nova instalação" mental, não uma "nova linha na
tabela schools" — terá de ser reescrito na Fase 11 (migração de módulos) para criar de
facto uma `School` dentro da base partilhada, mantendo a mesma UX de 15 dias de teste.
Há um teste (`tests/Feature/TrialRegisterTest.php`) que cobre isto — deve continuar a
passar após a mudança (ver Fase 12 do mandato: regressão).

### 3.3 Motor de cálculo académico (`app/Models/Grade.php`)
Já validado e premiado — segue o Diploma Ministerial nº 59/2015 do MINEDH
(MACS → MT → MFD → MF), com pesos configuráveis via `Setting`. **Não tem `school_id`
nem precisa de mudar a fórmula.** Só precisa de operar corretamente depois de `Grade`
receber `school_id` — o cálculo em si já é determinístico por aluno/disciplina/turma,
pelo que o risco aqui é baixo desde que o `school_id` seja preenchido corretamente por
via da nova trait de tenancy (ver `MULTI_TENANCY.md`).

### 3.4 Rota morta / view em falta (já corrigida nesta sessão)
`GradeController@gradeSheet` apontava para `grades.grade-sheet`, que não existia.
Corrigido e adicionado como `resources/views/grades/grade-sheet.blade.php` nesta mesma
branch — não é um achado da migração multi-tenant, mas fica registado aqui para que
o próximo agente não o reintroduza nem o confunda com uma regressão nova.

### 3.5 Views e queries que assumem uma única escola
Verificado em `resources/views/grades/index.blade.php`: os cálculos de MACS/MT são
recalculados inline em `@php` em vez de reutilizar `Grade::calculateMACS()` /
`calculateMT()` do model. Isto **não é um problema de tenancy diretamente**, mas é um
ponto de atenção paralelo: qualquer alteração ao `Grade` model (ex: adicionar
`school_id` ao scope de cálculo) tem de ser replicada manualmente nesta view enquanto
a duplicação não for eliminada. Recomenda-se resolver este débito técnico **antes** ou
**durante** a Fase 11 (migração do módulo académico), não depois.

## 4. Entidades e se pertencem operacionalmente a uma escola

Tabela de decisão exigida pela Fase 2 do mandato (`school_id` sim/não):

| Entidade | Pertence a uma escola? | Nota |
|---|---|---|
| `students`, `teachers`, `classes`, `class_rooms`, `class_schedules`, `class_subjects`, `subjects` | **Sim** | núcleo académico |
| `enrollments`, `enrollment_applications`, `enrollment_documents` | **Sim** | |
| `grades`, `assessments`, `attendances` | **Sim** | motor de cálculo, ver 3.3 |
| `payments`, `expenses`, `fee_types`, `financial_categories` | **Sim** | financeiro |
| `communications`, `conversations`, `messages`, `sms_logs`, `sms_inbox` | **Sim** | por escola; SMS usa SIM da própria escola |
| `gate_logs` (portaria) | **Sim** | |
| `staff_leave_requests`, `secretaries`, `parent_model`, `student_guardians`, `observations`, `transfers`, `material_lists`, `sponsorships`, `events` | **Sim** | |
| `settings` | **Parcial** | hoje é 100% global; precisa de `school_id` nullable para permitir configuração por escola, mantendo defaults globais (ver `DATA_SCOPING.md`) |
| `licenses` | **Sim, mas com cuidado** | ver 3.1 — hoje assume-se singular |
| `users` | **Sim, na maioria dos casos** | um utilizador escolar pertence a uma escola; utilizadores territoriais (ZIP/Distrito/Província/Super Admin) **não** têm `school_id` — têm `zip_id`/`district_id`/`province_id` conforme o seu nível (ver `DATA_SCOPING.md`) |
| `permissions`, roles do Spatie | **Não** | globais à plataforma |
| Futuras: `provinces`, `districts`, `zips`, `schools` | **Não são tenants** | são a própria hierarquia territorial |

## 5. Plano recomendado (aponta para os documentos seguintes)

Esta auditoria não decide a implementação — só mapeia o terreno. As decisões de
arquitetura e o plano faseado estão em:
- `docs/architecture/MULTI_TENANCY.md` — decisão do modelo de tenant
- `docs/architecture/TERRITORIAL_HIERARCHY.md` — modelo Província/Distrito/ZIP/Escola
- `docs/architecture/DATA_SCOPING.md` — isolamento vs. visibilidade agregada
- `docs/architecture/SECURITY_MODEL.md` — camadas de segurança e testes
- `docs/architecture/DATA_AGGREGATION.md` — motor de indicadores e relatórios
- `docs/architecture/MIGRATION_PLAN.md` — sprints, branches, critérios de saída
- `docs/architecture/DECISIONS.md` — registo vivo de decisões (ADR)

## 6. Riscos de fuga de dados (data leakage) já identificados para tratar na Fase 3

1. `License::first()` (secção 3.1).
2. Qualquer rota que aceite `{id}` de aluno/turma/pagamento diretamente na URL sem
   verificar que esse registo pertence à escola do utilizador autenticado — a auditoria
   de rotas específicas fica para a Fase 3 (Data Isolation), quando os controllers forem
   revistos um a um; esta auditoria de Fase 0 apenas sinaliza o padrão de risco.
3. Exports (`maatwebsite/excel` está instalado, `app/Exports/` a confirmar na Fase 11) —
   qualquer export tem de respeitar exatamente o mesmo scope que o dashboard, nunca uma
   regra à parte (ver `SECURITY_MODEL.md`).
