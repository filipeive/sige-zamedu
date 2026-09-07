# MASTER DIRECTIVES & ENGINEERING GUIDELINES FOR AI AGENTS (SIGE / ZAMEDU)

Este documento define as regras inegociáveis para qualquer agente de IA (Antigravity, Claude, ChatGPT, Cursor, Gemini, Copilot) ou humano que trabalhar no repositório **sige-zamedu**.

---

## 📌 Ordem de Leitura Obrigatória ao Iniciar Qualquer Sessão
1. `.agents/AGENTS.md` (Este ficheiro).
2. `agents/AGENT_GUIDELINES.md` — Regras inegociáveis de arquitetura e qualidade.
3. `agents/PROJECT_CONTEXT.md` — Contexto do projeto ZAMEDU / SIGE, estado atual.
4. `agents/HANDOFF.md` — Diário de bordo das sessões anteriores. **Regra obrigatória:** Ler a última entrada antes de começar e registar uma nova entrada no topo antes de terminar.
5. Documentos em `docs/architecture/` e `docs/strategy/` aplicáveis à tarefa.

---

## 🛡️ Regras Invioláveis de Desenvolvimento & Arquitetura

1. **PROIBIDO QUEBRAR O MOTOR DE CÁLCULO MINEDH**:
   - `Grade::calculateMACS()`, `calculateMT()`, `calculateMFD()` e `calculatePautaStats()` são a fonte única de verdade.
   - Nunca recalcular notas com fórmulas inline em views se a lógica central existir no Model.

2. **REGRAS DO SISTEMA NACIONAL DE EDUCAÇÃO (MINEDH - LEI 18/2018)**:
   - Escala oficial de avaliação: $0.0$ a $20.0$ valores (limiar de aprovação: $9.5$ / $10.0$).
   - Classes com Exame Nacional: 9ª, 10ª e 12ª classes.
   - Fórmula de Exame: $NE\_Eficaz = \max(NE, ACF)$ e $NFD = \text{round}\left(\frac{MAF + NE\_Eficaz}{2}\right)$.
   - Todos os relatórios pedagógicos exigem desagregação estatística por género (**HM**: Total Homens e Mulheres / **M**: Mulheres/Feminino).

3. **MODELO DE TENANCY HIERÁRQUICO (PROVÍNCIA -> DISTRITO -> ZIP -> ESCOLA)**:
   - A hierarquia territorial de Moçambique é soberana: Província $\rightarrow$ Distrito $\rightarrow$ ZIP $\rightarrow$ Escola.
   - O escopo de escrita (CRUD) de uma escola é sempre isolado (`SchoolScope`).
   - O escopo de leitura agregada para ZIP/Distrito/Província usa `HierarchyScopeResolver` e é read-only.
   - Nomenclatura no código: Usar `school_id`, `School`, `Zip`, `District`, `Province` (nunca "tenant" genérico).

4. **ESTRUTURA DE SPRINTS E COMMIT**:
   - Princípio: **Um Sprint = Uma Branch = Um PR**.
   - Migrações são sempre aditivas antes de destrutivas (`school_id` começa `nullable()`).
   - Nunca declarar uma tarefa ou sprint concluído sem executar os testes automatizados (`vendor/bin/phpunit`).

5. **AESTHETICS & UX EXCELLENCE**:
   - Manter os design tokens do ZAMEDU: tons esmeralda/verde institucional, tipografia Inter/Poppins, cards suaves, modais com $z\text{-index}$ devidamente isolado (ex: $z\text{-index: 1055}$) e alertas limpos sem sobreposição.

---

## 📋 Sequenciamento de Sprints de Tenancy

- **Sprint 0**: Fundação Documental (`docs/multi-tenant-foundations`) — ✅ Concluído.
- **Sprint 1**: Modelo Territorial (`feature/territorial-model`) — `Province`, `District`, `Zip`, `School` + Seeds.
- **Sprint 2**: Colunas Aditivas `school_id` (`feature/school-tenant-columns`) + Backfill.
- **Sprint 3**: Trait `BelongsToSchool` e Scope (`feature/school-scoping`).
- **Sprint 4**: Tornar `school_id` NOT NULL (`feature/school-id-not-null`) — Ponto de não-retorno.
- **Sprint 5**: Roles Territoriais Spatie (`feature/territorial-roles`).
- **Sprint 6**: Dashboards Territoriais (`feature/territorial-dashboards`).
- **Sprint 7**: Agregação e Drill-Down (`feature/aggregation-drilldown`).
- **Sprint 8**: Testes de Segurança Multi-Tenant (`feature/tenant-security-tests`).
- **Sprint 9**: Otimização de Performance & Índices (`feature/tenant-performance`).
- **Sprint 10**: Onboarding Multi-Escola (`feature/school-onboarding`).
