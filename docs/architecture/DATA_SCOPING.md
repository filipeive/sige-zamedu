# DATA_SCOPING.md
## Isolamento de Escrita vs. Visibilidade Agregada — o ponto mais delicado de todo o projeto

Pré-requisito de leitura: `MULTI_TENANCY.md`, `TERRITORIAL_HIERARCHY.md`.

**Qualquer agente de IA deve ler este documento por completo antes de tocar em
autorização, scopes, policies ou dashboards territoriais.** É o tipo de distinção que,
mal implementada, resulta numa escola conseguir editar dados de outra — e é fácil de
simplificar mal se não for lido com atenção.

---

## 1. `Role ≠ Scope`

Citação direta do documento de relatórios (`docs/strategy/REPORTS_BY_ROLE.md`, secção 2):
um Diretor tem uma role administrativa, mas o seu **scope** é uma escola. Um administrador
provincial tem permissões de gestão, mas o seu **scope** é uma província. Role decide
**o que** o utilizador pode fazer (ex: "ver relatórios financeiros"); scope decide
**sobre quem/o quê** ele pode fazê-lo.

## 2. As duas operações são fundamentalmente diferentes

### 2.1 Escrita operacional (lançar nota, matricular aluno, registar pagamento)
SEMPRE estritamente isolada por `school_id` do utilizador. Nunca há exceção, nunca há
"visão alargada" para escrita. Isto é implementado por:

- `App\Traits\BelongsToSchool` — aplica `addGlobalScope(new SchoolScope())` a todo model
  de negócio (ver lista completa na tabela da secção 4 de `MULTI_TENANCY_AUDIT.md`), e
  preenche `school_id` automaticamente no `creating`.
- `App\Scopes\SchoolScope` — filtra sempre por `school_id = current_school_id()` para
  utilizadores de nível escola.

Um utilizador `teacher`, `secretary`, `pedagogy`, `admin` (ao nível da escola) usa
**exclusivamente** este caminho. Nunca vê nem escreve para fora do seu `school_id`.

### 2.2 Leitura agregada territorial (painel de ZIP, Distrito, Província)
É uma **exceção controlada e explícita**, nunca uma remoção geral do isolamento:

- Só existe em queries **explicitamente marcadas** como agregação territorial (ex:
  `SchoolMetric::withinZip($zipId)`, nunca uma query "normal" de `Student::all()`).
- É **só leitura**. Nenhum painel territorial escreve em dados de uma escola específica.
- Preferencialmente opera sobre **indicadores agregados**, não sobre registos individuais
  (ver `docs/strategy/REPORTS_BY_ROLE.md`, secção 17: "quanto maior o nível
  administrativo, maior a preferência por dados agregados" — a Escola vê "João, 78% de
  frequência"; o ZIP vê "Escola A, 91% de frequência agregada", não o nome do João).
- Drill-down até ao registo individual só acontece quando (a) necessário, (b) dentro do
  scope territorial do utilizador, e (c) explicitamente autorizado — nunca por omissão.

## 3. `HierarchyScopeResolver` — a peça que falta no padrão do ZBIZ+

O `ZBIZ_PLUS` (repositório irmão) resolve tenancy só entre `Tenant` e `Branch` — um nível,
sem hierarquia territorial acima. O SIGE precisa de um serviço adicional que o ZBIZ+ não
tem:

```php
class HierarchyScopeResolver
{
    // Para um utilizador escolar normal: devolve [school_id] (lista de 1).
    // Para um zip_admin: devolve todos os school_id cuja escola.zip_id === o zip do utilizador.
    // Para um district_admin: devolve todos os school_id de todas as escolas de todas
    //   as ZIPs cujo district_id === o distrito do utilizador.
    // Para um province_admin: idem, ao nível da província.
    // Para um super_admin: null / sem filtro (acesso global).
    public function resolveVisibleSchoolIds(User $user): array|null;
}
```

Este resolver **só é chamado pelos controllers/queries dos painéis territoriais**
(Fase 6 do mandato). Nunca é injetado no `SchoolScope` global usado pelo CRUD normal da
escola — misturar os dois é exatamente o erro que este documento existe para prevenir.

## 4. `SchoolContext` — fonte única de verdade

Idêntico em espírito ao `TenantContext` do ZBIZ+, mas com mais níveis:

```php
class SchoolContext
{
    public function currentSchoolId(): ?int;      // para CRUD operacional
    public function currentAdministrativeLevel(): string; // school|zip|district|province|super
    public function currentZipId(): ?int;
    public function currentDistrictId(): ?int;
    public function currentProvinceId(): ?int;
}
```

Nenhum módulo deve implementar a sua própria lógica de "qual é a escola/zip/distrito
atual" — todos consultam este serviço (Fase 4 do mandato: "evitar múltiplas
implementações independentes da lógica de tenant").

## 5. Matriz de scope por role (referência rápida)

Adaptado de `docs/strategy/VISION_AND_ARCHITECTURE.md` secção 7, cruzado com os roles
já existentes no SIGE (ver `MULTI_TENANCY_AUDIT.md` secção 2):

| Role existente / a criar | Scope | Observação |
|---|---|---|
| `super_admin` (existe) | Global | sem filtro |
| `province_admin` (**a criar**) | Província | via `HierarchyScopeResolver` |
| `district_admin` (**a criar**) | Distrito | via `HierarchyScopeResolver` |
| `zip_admin` (**a criar**) | ZIP | via `HierarchyScopeResolver` |
| `admin` (existe, redefinir como School Admin) | Escola | `SchoolScope` |
| `pedagogy` (existe) | Escola / dados pedagógicos | `SchoolScope` |
| `secretary` (existe) | Escola / dados administrativos | `SchoolScope` |
| `teacher` (existe) | Escola / turmas atribuídas | `SchoolScope` + filtro de turma |
| `security` (existe) | Escola / portaria | `SchoolScope` |
| `parent` (existe) | Dados dos seus educandos | filtro por `student_guardians` |
| `student` (**não existe ainda**) | Dados próprios | a avaliar se é necessário no MVP provincial |

Não criar roles duplicados (`school_admin` vs `admin` já existente) sem antes confirmar
com o Filipe se `admin` deve ser redefinido ou se `school_admin` é um novo role separado.

## 6. Regra de verificação antes de qualquer PR desta área

Antes de dar como concluída qualquer funcionalidade de scoping, responder por escrito no
PR (ver `agents/BRANCH_WORKFLOW.md`):
1. Esta funcionalidade escreve dados? Se sim, usa exclusivamente `SchoolScope`/`school_id`?
2. Esta funcionalidade lê dados agregados de várias escolas? Se sim, usa
   `HierarchyScopeResolver` e é explicitamente só-leitura?
3. Existe algum caminho (rota, API, export) que misture os dois padrões? Se sim, corrigir
   antes de merge.
