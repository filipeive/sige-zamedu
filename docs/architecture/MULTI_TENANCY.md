# MULTI_TENANCY.md
## Decisão de Arquitetura: Modelo de Tenant

Pré-requisito de leitura: `MULTI_TENANCY_AUDIT.md` (estado atual e riscos já identificados).

---

## 1. Decisão

**Single database, shared tables, isolamento lógico por `school_id`.**

Não database-per-tenant. Não schema-per-tenant. Esta decisão segue explicitamente a
recomendação do documento estratégico (`docs/strategy/VISION_AND_ARCHITECTURE.md`, secção 3)
e o padrão já validado no repositório irmão `ZBIZ_PLUS`
(`docs/05_MULTI_TENANCY.md` desse projeto).

### Porquê
- Custo mínimo de infraestrutura — viável para escolas públicas/rurais com recursos
  limitados, dezenas a centenas de escolas num único servidor acessível.
- Migrações e manutenção centralizadas — um agente ou humano só corre `php artisan migrate`
  uma vez, não uma vez por escola.
- Backups simples — uma base de dados, uma rotina de backup.
- Não antecipa complexidade que ainda não é necessária (ver secção 13 do documento de
  visão estratégica: evitar microservices, database-per-tenant, CQRS complexo nesta fase).

## 2. `School` é o tenant

O termo "tenant" nos documentos genéricos (mandato, ZBIZ+) corresponde no SIGE à entidade
`School`. Usar sempre `School` / `school_id` no código do SIGE — nunca "Tenant" genérico —
para que o código se leia como o domínio que é (ver `agents/AGENT_GUIDELINES.md`, regra 9).

```
School
├── belongsTo Zip (e, por herança, District e Province — ver TERRITORIAL_HIERARCHY.md)
├── hasMany Users (utilizadores escolares: secretaria, professores, direção)
├── hasMany Students, Teachers, Classes, Enrollments
├── hasMany Grades, Attendances, Assessments
├── hasMany Payments, Expenses
├── hasMany Communications, SmsLogs
└── hasOne/hasMany License (ver secção 4)
```

## 3. O que NÃO se torna tenant-scoped

Conforme a Fase 2 do mandato — nunca adicionar `school_id` indiscriminadamente:

- `provinces`, `districts`, `zips` — são a hierarquia acima do tenant, não pertencem a
  um tenant.
- `permissions` e `roles` (tabelas do Spatie) — globais à plataforma. Um role chamado
  `teacher` é a mesma definição de role em todas as escolas; o que varia por escola é
  **quem** tem esse role atribuído (`model_has_roles`, que já referencia `user_id`, e
  o `user` esse sim tem `school_id`).
- Configurações globais de sistema (ex: versão da aplicação, chaves de API partilhadas).

## 4. Correção necessária: `License`

A auditoria (secção 3.1) identificou `License::first()` como assunção de licença única
global. Decisão: `licenses` recebe `school_id` **nullable** na Fase 1 (aditiva), e o
`LicenseCheckMiddleware` passa a resolver a licença via `SchoolContext::current()->school_id`
em vez de `first()`. Isto tem de acontecer na mesma fase em que `School` é criado — não
pode ficar para depois, ou o sistema fica com uma licença "fantasma" a bloquear/desbloquear
escolas erradas.

## 5. Compatibilidade com modo single-school (obrigatório — Fase 16 do mandato)

O sistema tem de continuar a funcionar normalmente para a escola piloto (a que já venceu
o prémio) com uma única `School` registada. Não é permitido que a aplicação só funcione
depois de existirem várias escolas. Na prática: a primeira `School` criada pelo comando
`schools:backfill` (ver `MIGRATION_PLAN.md`, Sprint 2) representa a escola atual, e todo
o comportamento visível para essa escola permanece inalterado.
