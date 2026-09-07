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
