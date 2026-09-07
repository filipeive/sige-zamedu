# ZAMEDU — Visão Estratégica e Abordagem de Arquitetura

## 1. Propósito deste documento

Este documento é a **fonte de contexto estratégico** para os AI Agents que trabalham no ZAMEDU.

O objetivo é impedir que um agente trate a evolução atual como um simples conjunto de funcionalidades isoladas. O ZAMEDU deve continuar sendo um **Sistema Integrado de Gestão Escolar**, mas evoluir para uma plataforma capaz de operar em múltiplos níveis administrativos.

> **Princípio central:** não estamos reconstruindo o ZAMEDU. Estamos evoluindo uma plataforma já avançada sem perder as funcionalidades existentes.

---

## 2. Visão do produto

### Antes

O sistema é utilizado principalmente por uma escola para:

- gerir alunos;
- gerir professores e funcionários;
- gerir turmas e matrículas;
- gerir notas e avaliações;
- gerir pagamentos;
- gerir comunicação;
- emitir documentos;
- gerir portaria;
- disponibilizar portais;
- utilizar recursos de IA;
- produzir relatórios.

### Nova visão

O ZAMEDU passa a funcionar como uma plataforma educacional **multi-tenant e territorial**, permitindo:

```text
PROVÍNCIA
   │
   ├── DISTRITO A
   │      │
   │      ├── ZIP 01
   │      │     ├── Escola A
   │      │     └── Escola B
   │      │
   │      └── ZIP 02
   │            ├── Escola C
   │            └── Escola D
   │
   └── DISTRITO B
          └── ...
```

A escola continua sendo a unidade operacional principal.

O ZIP, Distrito e Província passam a funcionar como **camadas de gestão, supervisão, monitoria e análise**.

---

## 3. Modelo conceptual

### 3.1 School = Tenant

Cada escola é um tenant lógico.

```text
Tenant
└── School
    ├── Users
    ├── Students
    ├── Teachers
    ├── Classes
    ├── Enrollments
    ├── Grades
    ├── Attendance
    ├── Payments
    ├── Communications
    └── Reports
```

A primeira implementação recomendada é:

**shared database + shared tables + tenant_id**

Não iniciar com database-per-tenant.

---

## 4. Hierarquia territorial

Criar uma relação explícita:

```text
Province
    hasMany Districts

District
    belongsTo Province
    hasMany ZIPs

ZIP
    belongsTo District
    hasMany Schools

School/Tenant
    belongsTo ZIP
    belongsTo District
    belongsTo Province
```

Embora Distrito e Província possam ser derivados através dos relacionamentos, manter referências cuidadosamente controladas pode ser útil para:

- consultas analíticas;
- filtros;
- índices;
- relatórios;
- performance.

Entretanto, não duplicar informação sem uma estratégia de consistência.

---

## 5. Regra de ouro: isolamento dos dados

O `tenant_id` não deve ser tratado apenas como filtro visual.

A segurança deve existir em toda a cadeia:

```text
Authentication
      ↓
Tenant Context
      ↓
Authorization / Policy
      ↓
Query Scope
      ↓
Service
      ↓
Controller
      ↓
View / API / Export
```

Um usuário de Escola A nunca deve conseguir acessar dados da Escola B apenas alterando:

```text
?id=123
```

ou manipulando:

```text
/schools/2/students
```

ou uma API.

---

## 6. Tenant Context

Criar um conceito central de contexto atual.

Exemplo conceptual:

```text
CurrentContext
├── user
├── administrative_level
├── tenant
├── school
├── zip
├── district
└── province
```

O restante da aplicação deve consultar este contexto em vez de cada módulo criar sua própria lógica.

Isso reduz inconsistências.

---

## 7. Níveis de acesso

A autorização deve combinar:

1. **Role**
2. **Administrative scope**
3. **Tenant scope**

Exemplo:

| Perfil | Escopo |
|---|---|
| Super Admin | Global |
| Provincial Admin | Província |
| District Admin | Distrito |
| ZIP Admin | ZIP |
| School Admin | Escola |
| Director | Escola |
| Teacher | Escola / turmas atribuídas |
| Staff | Escola / permissões específicas |
| Parent | Dados autorizados do educando |
| Student | Dados próprios |

Os nomes exatos dos roles devem respeitar o sistema existente.

Não criar roles duplicados se já existir uma estrutura equivalente.

---

## 8. Relatórios como produto central

A evolução mais importante não é apenas "ter várias escolas".

É transformar os dados operacionais em **informação administrativa**.

```text
Dados da Escola
      ↓
Indicadores da Escola
      ↓
Agregação ZIP
      ↓
Indicadores do ZIP
      ↓
Agregação Distrito
      ↓
Indicadores Distritais
      ↓
Agregação Provincial
      ↓
Inteligência Educacional
```

---

## 9. Mesmo dado, diferentes perspectivas

Um aluno pode existir apenas na Escola A.

A Escola A pode consultar:

> João, 7ª classe, turma B, frequência 91%.

O ZIP não precisa ver todos os detalhes pessoais para saber:

> ZIP 01: 92% de frequência média.

O Distrito pode visualizar:

> Distrito X: 89% de frequência média.

A Província pode visualizar:

> Província Y: 90% de frequência média.

Portanto:

**a informação deve subir em forma de indicadores, não necessariamente em forma de exposição integral dos dados individuais.**

---

## 10. Drill-down

O sistema deve permitir navegar:

```text
Província
  ↓
Distrito
  ↓
ZIP
  ↓
Escola
  ↓
Turma
  ↓
Indicador
  ↓
Registo operacional
```

Cada passo deve verificar autorização.

---

## 11. Relatórios orientados ao usuário

O mesmo motor de relatórios deve adaptar o resultado ao perfil.

### Professor

Foco:

- suas turmas;
- alunos;
- notas;
- frequência;
- desempenho;
- pendências.

### Diretor

Foco:

- escola;
- matrícula;
- frequência;
- aproveitamento;
- professores;
- finanças;
- ocorrências.

### Administrador ZIP

Foco:

- comparação entre escolas;
- cobertura;
- frequência;
- desempenho;
- alertas;
- escolas com problemas.

### Administrador Distrital

Foco:

- visão consolidada dos ZIPs;
- comparação territorial;
- indicadores;
- evolução;
- escolas críticas.

### Administrador Provincial

Foco:

- visão provincial;
- distritos;
- tendências;
- indicadores agregados;
- distribuição de recursos;
- comparação territorial.

---

## 12. Arquitetura de dados

O modelo existente já possui entidades operacionais como:

- users;
- students;
- teachers;
- classes;
- enrollments;
- payments;
- grades;
- sms_logs;
- sms_inbox;
- ai_conversations;
- messages;
- gate_logs;
- settings.

A evolução multi-tenant deve ser feita **progressivamente**.

Não adicionar `tenant_id` indiscriminadamente a todas as tabelas.

Pergunta obrigatória para cada entidade:

> "Este dado pertence operacionalmente a uma escola?"

Se sim, avaliar `tenant_id`.

Se for global/configuracional, avaliar se deve permanecer global.

---

## 13. Estratégia de agregação

Preferência inicial:

```text
Operational Data
       ↓
Services / Query Layer
       ↓
Aggregated Metrics
       ↓
Dashboards
```

Evitar duplicar dados apenas para criar dashboards.

Quando o volume justificar:

```text
Operational Tables
       ↓
Reporting / Aggregate Tables
       ↓
Dashboard
```

Cache pode ser utilizado quando houver justificativa de performance.

Não introduzir prematuramente:

- microservices;
- Kafka;
- Kubernetes;
- CQRS complexo;
- database-per-tenant;
- data warehouse completo.

---

## 14. Dados reais vs dados demonstrativos

É obrigatório distinguir:

### Dados operacionais reais

Dados introduzidos por escolas utilizando o sistema.

### Dados demonstrativos

Dados criados para demonstração da plataforma.

Nunca apresentar dados simulados como:

> "estatísticas oficiais da Província"

se não houver integração ou fonte oficial que sustente essa afirmação.

Utilizar termos como:

- Dados demonstrativos;
- Dados operacionais;
- Indicadores do sistema;
- Simulação;
- Ambiente de demonstração.

---

## 15. Princípios de implementação

Todo agente deve seguir:

1. Analisar antes de modificar.
2. Preservar funcionalidades existentes.
3. Preferir mudanças incrementais.
4. Não duplicar regras de negócio.
5. Centralizar contexto e autorização.
6. Testar isolamento entre tenants.
7. Testar URLs manipuladas.
8. Testar APIs e exports.
9. Documentar decisões arquiteturais.
10. Evitar overengineering.

---

## 16. Regra para AI Agents

Antes de implementar qualquer funcionalidade relacionada à arquitetura multi-tenant:

```text
1. Inspecionar código existente
2. Identificar dependências
3. Identificar entidades afetadas
4. Identificar riscos de dados
5. Identificar impacto em permissões
6. Criar plano
7. Implementar pequena alteração
8. Executar testes
9. Verificar regressões
10. Atualizar documentação
```

Se uma alteração puder comprometer dados existentes, **parar e propor migração incremental**.

---

## 17. Critério final de sucesso

A arquitetura será considerada bem implementada quando:

- múltiplas escolas puderem coexistir;
- cada escola enxergar apenas seus dados;
- ZIPs puderem acompanhar escolas;
- Distritos puderem acompanhar ZIPs;
- Províncias puderem acompanhar Distritos;
- dashboards agregarem dados corretamente;
- drill-down respeitar permissões;
- relatórios respeitarem escopo;
- APIs respeitarem escopo;
- exports respeitarem escopo;
- single-school mode continuar funcional;
- os módulos existentes continuarem funcionando.

---

## 18. Contexto atual do projeto

O ZAMEDU já possui uma arquitetura Laravel/MVC, sistema de permissões, middleware de licenciamento, Theme Engine, portais, módulos administrativos e diversos serviços especializados. A documentação atual descreve Laravel, Eloquent/MySQL/MariaDB, Spatie Permission e o uso de helpers e serviços centrais.

Esta evolução deve **estender a arquitetura existente**, não substituí-la por uma arquitetura completamente nova.

---

# Decisão estratégica

> **ZAMEDU não deve competir apenas como "mais um sistema de gestão escolar".**
>
> Deve demonstrar que consegue transformar várias escolas em uma rede educacional gerenciável, monitorável e analisável através de diferentes níveis administrativos.

Essa é a característica que deve ser mais visível na fase provincial.
