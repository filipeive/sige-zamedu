# DECISIONS.md
## Registo de Decisões Arquiteturais (ADR) — SIGE Multi-Tenant

Documento vivo, append-only (não reescrever decisões passadas — se uma decisão for
revista, adicionar uma nova entrada que referencia e substitui a anterior). Todo agente
que tomar uma decisão arquitetural relevante regista aqui, com data e sprint.

Formato de cada entrada: **Contexto → Decisão → Alternativas consideradas → Consequências.**

---

### 2026-09-06 — Sprint 0 — Modelo de tenant: single database + `school_id`
**Contexto:** SIGE precisa de suportar múltiplas escolas antes da fase provincial do
Prémio Jovem Criativo.
**Decisão:** single database, shared tables, isolamento lógico por `school_id`, seguindo
o padrão já validado no repositório irmão `ZBIZ_PLUS`.
**Alternativas consideradas:** database-per-tenant (rejeitada — custo de infraestrutura e
complexidade de manutenção incompatíveis com o contexto de escolas rurais/públicas com
recursos limitados); schema-per-tenant (rejeitada pela mesma razão, com menos benefício
que database-per-tenant para justificar a complexidade adicional).
**Consequências:** ver `docs/architecture/MULTI_TENANCY.md`.

### 2026-09-06 — Sprint 0 — Hierarquia territorial de 4 níveis
**Contexto:** o SIGE precisa de uma camada acima do tenant que o ZBIZ+ não tem.
**Decisão:** `Province → District → Zip → School`, refletindo a estrutura real do MINEDH
(a ZIP — Zona de Influência Pedagógica — é uma unidade oficial, não inventada).
**Alternativas consideradas:** hierarquia de 2 níveis (Distrito → Escola, ignorando ZIP)
— rejeitada porque a ZIP é a unidade real de supervisão pedagógica em Moçambique e é um
argumento de apresentação forte perante o júri.
**Consequências:** ver `docs/architecture/TERRITORIAL_HIERARCHY.md`.

### 2026-09-06 — Sprint 0 — Isolamento de escrita separado de visibilidade agregada
**Contexto:** um coordenador de ZIP/Distrito/Província precisa de ver indicadores de
várias escolas sem que isso comprometa o isolamento de dados entre escolas.
**Decisão:** dois mecanismos distintos — `SchoolScope` (isolamento estrito, escrita e
leitura normal) e `HierarchyScopeResolver` (leitura agregada, explicitamente marcada,
nunca usada em CRUD normal).
**Alternativas consideradas:** um único scope "inteligente" que decide dinamicamente
quanto mostrar consoante o role — rejeitada por ser mais fácil de implementar mal e mais
difícil de auditar/testar do que dois mecanismos separados e explícitos.
**Consequências:** ver `docs/architecture/DATA_SCOPING.md`. Esta é considerada a decisão
de maior risco de segurança do projeto — qualquer alteração a este mecanismo exige
revisão cuidadosa.

<!--
Próximas entradas a adicionar por agentes futuros, quando essas decisões forem tomadas:
- Nomenclatura final dos roles territoriais (zip_admin vs. nomes já usados no sistema)
- Se license.school_id fica nullable indefinidamente ou se se torna obrigatório
- Se district_id/province_id serão desnormalizados em `schools` por performance
- Resultado da Fase 3/Sprint 4 (school_id NOT NULL) — registar aprovação explícita do
  Filipe antes do merge, conforme exigido em agents/AGENT_GUIDELINES.md regra 10
-->
