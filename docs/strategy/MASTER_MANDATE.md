# ZAMEDU — MASTER ARCHITECTURE & DEVELOPMENT MANDATE

> Documento fonte, fornecido verbatim pelo Filipe (colado diretamente, não enviado como
> ficheiro). Preservado aqui para rastreabilidade. As suas 16 fases foram distribuídas e
> operacionalizadas nos documentos em `docs/architecture/` (ver mapeamento no fim deste
> ficheiro) — ler estes primeiro para a versão executável; este ficheiro é a fonte
> original completa, para consulta.

## CONTEXTO

Estamos desenvolvendo o ZAMEDU — SIGE (Sistema Integrado de Gestão Escolar), actualmente disponível no repositório:

https://github.com/filipeive/sige-zamedu

O projecto já possui uma base funcional avançada e não deve ser tratado como um projecto novo.

O ZAMEDU já contém múltiplos módulos de gestão escolar, incluindo gestão académica, estudantes, professores, turmas, avaliações, relatórios, finanças, comunicações, portais, controlo de acesso, PWA, RBAC e outras funcionalidades.

O projecto utiliza Laravel/PHP, Blade, Tailwind/Bootstrap, MySQL/MariaDB e Spatie Laravel Permission, entre outras dependências.

O objectivo desta fase é EVOLUIR a arquitectura existente para suportar uma plataforma educacional multi-tenant e hierárquica, sem destruir ou regressar funcionalidades existentes.

---

# VISÃO DO PRODUTO

O ZAMEDU deverá evoluir de:

"Sistema de Gestão de uma Escola"

para:

"Plataforma Integrada de Gestão, Monitoria e Inteligência Educacional Multi-Tenant."

A plataforma deverá permitir que múltiplas escolas utilizem a mesma infraestrutura, mantendo isolamento dos seus dados, enquanto níveis administrativos superiores poderão visualizar dados agregados dentro do seu respectivo território.

Modelo conceptual:

PROVÍNCIA
↓
DISTRITO
↓
ZIP
↓
ESCOLA / TENANT
↓
UTILIZADORES / ALUNOS / PROFESSORES / TURMAS / DADOS

A escola continua sendo responsável pela gestão operacional dos seus próprios dados.

Os níveis ZIP, Distrito e Província terão acesso somente ao escopo territorial que lhes corresponde.

---

# OBJECTIVO ESTRATÉGICO

Não queremos simplesmente implementar "multi-tenancy".

Queremos construir uma arquitectura capaz de responder a três necessidades:

1. Gestão operacional da escola.
2. Supervisão territorial.
3. Produção de indicadores educacionais agregados para apoio à decisão.

O sistema deverá permitir navegar de indicadores agregados para níveis territoriais inferiores através de drill-down controlado.

Exemplo:

Província
→ Distrito
→ ZIP
→ Escola
→ Turma
→ indicador/dado permitido

---

# REGRA FUNDAMENTAL

NÃO REESCREVER O SISTEMA.

NÃO SUBSTITUIR FUNCIONALIDADES FUNCIONAIS SEM NECESSIDADE.

NÃO FAZER UMA MIGRAÇÃO BIG-BANG.

NÃO ALTERAR O comportamento funcional existente apenas para "melhorar a arquitectura".

A evolução deverá ser incremental, testável e reversível.

Antes de alterar qualquer módulo:

1. analisar a arquitectura existente;
2. identificar dependências;
3. identificar tabelas relacionadas;
4. identificar controllers;
5. identificar models;
6. identificar policies;
7. identificar middleware;
8. identificar queries;
9. identificar views;
10. identificar testes existentes;
11. documentar o impacto;
12. só depois implementar.

---

# PAPEL DO AGENTE

Actua como:

* Chief Software Architect
* Laravel Architect
* Database Architect
* Multi-Tenant Architect
* Security Architect
* Product Architect
* QA Engineer
* DevOps Engineer

Não actues como um simples gerador de código.

Cada alteração deverá ser precedida de análise arquitectural.

---

# FASE 0 — AUDITORIA OBRIGATÓRIA

Antes de escrever código, analisar completamente o repositório.

Mapear:

app/
bootstrap/
config/
database/
resources/
routes/
tests/
docs/
.agents/

Identificar:

* Models
* Controllers
* Form Requests
* Policies
* Middleware
* Services
* Helpers
* Jobs
* Events
* Listeners
* Notifications
* Commands
* Migrations
* Seeders
* Factories
* Blade views
* APIs
* testes

Produzir uma análise:

docs/architecture/MULTI_TENANCY_AUDIT.md

O documento deverá identificar:

* arquitectura actual;
* modelo de autenticação;
* modelo RBAC;
* entidades principais;
* relações existentes;
* módulos que já possuem school_id/tenant_id;
* módulos que não possuem;
* queries que assumem uma única escola;
* riscos de data leakage;
* pontos críticos da migração;
* dependências;
* plano recomendado de migração.

NÃO IMPLEMENTAR MULTI-TENANCY NESTA FASE.

---

# FASE 1 — MODELO ORGANIZACIONAL

Criar uma arquitectura territorial explícita.

Entidades conceptuais:

Province
District
ZIP
School/Tenant

Relação:

Province
hasMany District

District
belongsTo Province
hasMany ZIP

ZIP
belongsTo District
hasMany Schools

School/Tenant
belongsTo ZIP
belongsTo District
belongsTo Province

A estrutura deve permitir futuras alterações administrativas sem depender de valores hardcoded.

---

# FASE 2 — TENANT MODEL

Introduzir um conceito central de Tenant.

Cada escola deverá possuir um tenant_id único.

O tenant representa o contexto de isolamento dos dados operacionais da escola.

Todas as entidades operacionais pertencentes a uma escola deverão estar associadas ao tenant.

Exemplo conceptual:

students
teachers
classes
subjects
grades
attendance
payments
expenses
communications
etc.

deverão possuir tenant_id quando forem entidades pertencentes ao contexto da escola.

NÃO adicionar tenant_id indiscriminadamente.

Entidades globais como:

* provinces
* districts
* ZIPs
* system configurations globais
* permissões globais

não devem ser transformadas em tenants.

---

# FASE 3 — DATA ISOLATION

Implementar isolamento de dados.

Regra:

Um utilizador de uma escola nunca poderá consultar, modificar, exportar ou inferir dados de outro tenant através das interfaces normais da aplicação.

O isolamento deverá funcionar em múltiplas camadas:

1. Authentication
2. Authorization
3. Tenant resolution
4. Query scoping
5. Policies
6. Form Requests
7. Services
8. API authorization
9. Exports
10. Reports

Não confiar apenas na UI.

Esconder um botão NÃO constitui isolamento.

---

# FASE 4 — TENANT CONTEXT

Criar um Tenant Context centralizado.

O sistema deverá conseguir determinar:

* tenant actual;
* escola actual;
* ZIP actual;
* distrito actual;
* província actual;
* nível administrativo do utilizador.

Evitar múltiplas implementações independentes da lógica de tenant.

Deve existir uma única fonte de verdade para resolução do contexto.

---

# FASE 5 — ROLES E SCOPES

Implementar RBAC + Data Scope.

Níveis conceptuais:

SUPER_ADMIN
PROVINCIAL_ADMIN
DISTRICT_ADMIN
ZIP_ADMIN
SCHOOL_ADMIN
DIRECTOR
TEACHER
STAFF
STUDENT
PARENT

Os nomes finais deverão respeitar a arquitectura de roles já existente no projecto.

Regras:

SUPER_ADMIN
→ acesso global

PROVINCIAL_ADMIN
→ somente a sua província

DISTRICT_ADMIN
→ somente o seu distrito

ZIP_ADMIN
→ somente a sua ZIP

SCHOOL_ADMIN / DIRECTOR
→ somente o seu tenant/escola

TEACHER
→ somente dados pedagógicos autorizados da sua escola

STUDENT
→ somente os seus dados

PARENT
→ somente os dados dos educandos associados.

---

# FASE 6 — ADMINISTRAÇÃO TERRITORIAL

Criar dashboards administrativos diferentes.

## SCHOOL DASHBOARD

Foco:

* estudantes;
* professores;
* turmas;
* notas;
* assiduidade;
* pagamentos;
* comunicação;
* desempenho.

## ZIP DASHBOARD

Foco:

* escolas;
* alunos;
* professores;
* assiduidade agregada;
* aproveitamento;
* indicadores;
* comparação entre escolas;
* alertas.

## DISTRICT DASHBOARD

Foco:

* ZIPs;
* escolas;
* cobertura;
* matrícula;
* professores;
* assiduidade;
* desempenho;
* indicadores territoriais.

## PROVINCIAL DASHBOARD

Foco:

* distritos;
* ZIPs;
* escolas;
* população escolar;
* professores;
* indicadores académicos;
* assiduidade;
* abandono;
* distribuição territorial;
* alertas;
* tendências.

---

# FASE 7 — DATA AGGREGATION

Criar uma camada própria para indicadores agregados.

Não duplicar dados operacionais apenas para gerar dashboards.

Preferir:

* queries optimizadas;
* aggregate queries;
* services;
* cached metrics quando necessário;
* materialized/reporting tables apenas quando justificadas.

O sistema deverá suportar:

School metrics
↓
ZIP metrics
↓
District metrics
↓
Province metrics

Os indicadores deverão ser calculados a partir dos dados operacionais reais.

---

# FASE 8 — DRILL-DOWN

Implementar navegação hierárquica.

Exemplo:

Província
→ Distrito
→ ZIP
→ Escola
→ módulo

Cada transição deverá respeitar as permissões do utilizador.

Nunca permitir que um utilizador contorne o scope alterando IDs manualmente na URL.

Exemplo proibido:

/schools/999

se a escola 999 estiver fora do scope do utilizador.

---

# FASE 9 — DATA QUALITY

Introduzir mecanismos para garantir qualidade dos dados.

Validar:

* escola pertence à ZIP;
* ZIP pertence ao distrito;
* distrito pertence à província;
* utilizador pertence ao contexto permitido;
* aluno pertence à escola;
* professor pertence à escola;
* turma pertence à escola.

Evitar inconsistências territoriais.

---

# FASE 10 — SEEDING E DEMONSTRAÇÃO

Criar uma estrutura de dados demo realista.

Não utilizar apenas:

School 1
School 2
District 1

Criar dados semanticamente realistas.

Exemplo:

Province
District
ZIP
School
Students
Teachers
Classes
Grades
Attendance

O ambiente demo deverá permitir demonstrar:

Província
→ Distrito
→ ZIP
→ Escola
→ dados.

Os dados de demonstração NÃO devem ser apresentados como dados oficiais reais.

Devem ser claramente marcados como:

DEMO / SIMULAÇÃO.

---

# FASE 11 — MIGRAÇÃO DOS MÓDULOS EXISTENTES

Migrar os módulos existentes progressivamente.

Prioridade:

1. Users / Authentication
2. Schools
3. Students
4. Teachers
5. Classes
6. Academic records
7. Attendance
8. Finance
9. Communications
10. Reports
11. Portais
12. APIs
13. Exports

Depois de cada módulo:

* executar testes;
* verificar tenant isolation;
* verificar policies;
* verificar URLs;
* verificar filtros;
* verificar dashboards;
* verificar relatórios;
* verificar exports.

Não avançar para o módulo seguinte se houver regressões críticas.

---

# FASE 12 — TESTES DE SEGURANÇA MULTI-TENANT

Criar testes explícitos.

Exemplo:

Tenant A possui:

Student A

Tenant B possui:

Student B

User A autenticado no Tenant A NÃO pode:

* visualizar Student B;
* editar Student B;
* apagar Student B;
* exportar Student B;
* obter Student B via API;
* obter Student B através de filtros;
* obter Student B alterando IDs;
* obter Student B através de relações indirectas.

Repetir os testes para:

* professores;
* turmas;
* notas;
* pagamentos;
* relatórios;
* documentos.

Também testar isolamento administrativo.

Um ZIP admin não pode consultar outro ZIP.

Um District admin não pode consultar outro distrito.

Um Provincial admin não pode consultar outra província.

---

# FASE 13 — PERFORMANCE

A arquitectura deverá considerar crescimento.

Evitar:

N+1 queries
queries sem índices
queries que carregam todos os tenants
agregações repetidas sem necessidade
loops PHP para grandes conjuntos de dados

Criar índices apropriados para:

tenant_id
province_id
district_id
zip_id

e combinações relevantes.

Usar eager loading quando apropriado.

Usar pagination.

Usar caching somente quando existir uma necessidade real.

---

# FASE 14 — API / INTEGRAÇÕES

A arquitectura deve preparar o sistema para APIs futuras.

As APIs deverão respeitar exactamente o mesmo modelo de tenant e scope da interface web.

Não criar uma regra de segurança para Blade e outra para API.

O domínio de autorização deve ser comum.

---

# FASE 15 — DOCUMENTAÇÃO

Actualizar:

docs/ARCHITECTURE.md

Criar:

docs/architecture/MULTI_TENANCY.md
docs/architecture/TERRITORIAL_HIERARCHY.md
docs/architecture/DATA_SCOPING.md
docs/architecture/DATA_AGGREGATION.md
docs/architecture/MIGRATION_PLAN.md
docs/architecture/SECURITY_MODEL.md
docs/architecture/DECISIONS.md

Documentar também as decisões arquitecturais importantes.

---

# FASE 16 — COMPATIBILIDADE

O sistema deve continuar funcional para uma única escola.

Isto é obrigatório.

O primeiro tenant existente deve poder funcionar normalmente.

Não criar uma arquitectura em que o sistema somente funciona depois de cadastrar dezenas de escolas.

A multi-tenancy deve ser uma capacidade da plataforma, não uma dependência que destrua o modo single-school existente.

---

# REGRA DE OURO PARA O AGENTE

Antes de qualquer alteração importante, responder internamente:

1. O que existe actualmente?
2. O que será alterado?
3. Que dependências serão afectadas?
4. Que dados podem ser quebrados?
5. Que permissões podem ser afectadas?
6. Que módulos podem sofrer regressões?
7. Como iremos testar?
8. Como poderemos reverter?

Se a alteração for arquitecturalmente arriscada:

PARAR.

Documentar o risco.

Propor uma estratégia incremental.

NÃO executar uma refactorização destrutiva.

---

# PROCESSO DE EXECUÇÃO

Executar em SPRINTS.

Cada sprint deve possuir:

## OBJECTIVO

O que estamos a resolver.

## ANÁLISE

Arquitectura existente.

## IMPLEMENTAÇÃO

Alterações necessárias.

## TESTES

Testes automatizados e manuais.

## SEGURANÇA

Verificação de isolamento.

## REGRESSÃO

Verificação das funcionalidades existentes.

## DOCUMENTAÇÃO

Documentação actualizada.

## RESULTADO

O que ficou concluído.

## PRÓXIMO PASSO

O que deve acontecer depois.

---

# CRITÉRIO DE SUCESSO

O ZAMEDU será considerado arquitecturalmente preparado quando:

* múltiplas escolas puderem coexistir;
* cada escola tiver tenant_id;
* os dados estiverem isolados;
* escolas estiverem associadas a ZIP;
* ZIPs estiverem associados a distritos;
* distritos estiverem associados a províncias;
* utilizadores possuírem scopes correctos;
* gestores territoriais conseguirem visualizar dados agregados;
* dashboards apresentarem indicadores;
* drill-down respeitar autorização;
* relatórios respeitarem scope;
* APIs respeitarem scope;
* exports respeitarem scope;
* testes comprovarem isolamento;
* funcionalidades existentes continuarem operacionais.

---

# PRINCÍPIO FINAL

O objectivo não é construir um sistema "complicado".

O objectivo é construir uma arquitectura simples, segura e escalável que permita:

UMA ESCOLA HOJE

→ VÁRIAS ESCOLAS

→ UMA ZIP

→ UM DISTRITO

→ UMA PROVÍNCIA

→ EVENTUALMENTE UMA REDE EDUCACIONAL MAIOR.

Não antecipar complexidade que ainda não é necessária.

Construir fundações correctas hoje para permitir crescimento amanhã.

Sempre privilegiar:

CORRECTNESS
SECURITY
DATA INTEGRITY
MAINTAINABILITY
SCALABILITY
OBSERVABILITY

sobre velocidade de implementação.

---

## Mapeamento: fase deste mandato → documento executável

| Fase do mandato | Documento em `docs/architecture/` |
|---|---|
| Fase 0 (Auditoria) | `MULTI_TENANCY_AUDIT.md` |
| Fase 1 (Modelo organizacional) | `TERRITORIAL_HIERARCHY.md` |
| Fase 2 (Tenant model) | `MULTI_TENANCY.md` |
| Fase 3 (Data isolation) + Fase 4 (Tenant context) | `DATA_SCOPING.md` |
| Fase 5 (Roles e scopes) | `DATA_SCOPING.md` secção 5 |
| Fase 6 (Dashboards territoriais) | `MIGRATION_PLAN.md` Sprint 6 |
| Fase 7 (Data aggregation) | `DATA_AGGREGATION.md` |
| Fase 8 (Drill-down) | `DATA_AGGREGATION.md` + `MIGRATION_PLAN.md` Sprint 7 |
| Fase 9 (Data quality) | `TERRITORIAL_HIERARCHY.md` secção 5 |
| Fase 10 (Seeding/demo) | `TERRITORIAL_HIERARCHY.md` secção 4 |
| Fase 11 (Migração de módulos) | `MIGRATION_PLAN.md` (todos os sprints) |
| Fase 12 (Testes de segurança) | `SECURITY_MODEL.md` |
| Fase 13 (Performance) | `DATA_AGGREGATION.md` secção 6 |
| Fase 14 (API/Integrações) | `SECURITY_MODEL.md` secção 5 |
| Fase 15 (Documentação) | este mapeamento + toda a pasta `docs/architecture/` |
| Fase 16 (Compatibilidade) | `MULTI_TENANCY.md` secção 5 |
