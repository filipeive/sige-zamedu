# TERRITORIAL_HIERARCHY.md
## Modelo Territorial: Província → Distrito → ZIP → Escola

Pré-requisito de leitura: `MULTI_TENANCY.md`.

---

## 1. A hierarquia é real, não inventada

Confirmado por pesquisa (fonte: literatura sobre o sistema educativo moçambicano, MINEDH):
a **Zona de Influência Pedagógica (ZIP)** é uma estrutura oficial do MINEDH que agrupa um
conjunto de escolas do Ensino Básico/Secundário para efeitos de supervisão pedagógica e
formação contínua de professores — usada, por exemplo, na supervisão de avaliações
trimestrais. Moçambique tem 11 províncias e 128 distritos.

Isto é um argumento de apresentação forte (ver
`docs/strategy/PROVINCIAL_PRESENTATION.md`): o SIGE segue a hierarquia real do sistema
educativo, não uma estrutura genérica de "matriz/filial" copiada de um ERP comercial.

```
Province (11 em Moçambique)
  └── District (128 no total)
        └── Zip (Zona de Influência Pedagógica — agrupa várias escolas)
              └── School (o tenant — ver MULTI_TENANCY.md)
```

## 2. Modelo de dados

```
provinces
  id, name, code

districts
  id, province_id (FK), name, code

zips
  id, district_id (FK), name, code

schools
  id, zip_id (FK), name, nuit, type [publica|privada], status, settings (JSON)
```

`district_id` e `province_id` **não são duplicados diretamente** em `schools` — são
obtidos por relação (`School::zip->district->province`). Isto evita inconsistências
(secção 4 do documento de visão estratégica alerta para isto). Se, por razões de
performance de queries analíticas, for necessário desnormalizar `district_id`/`province_id`
diretamente em `schools` (ou em tabelas de métricas agregadas), isso é decidido e
registado explicitamente em `DECISIONS.md` — nunca feito ad-hoc por um agente sem
registar a razão.

## 3. Models e relações Eloquent

```php
// Province
public function districts(): HasMany { return $this->hasMany(District::class); }

// District
public function province(): BelongsTo { return $this->belongsTo(Province::class); }
public function zips(): HasMany { return $this->hasMany(Zip::class); }

// Zip
public function district(): BelongsTo { return $this->belongsTo(District::class); }
public function schools(): HasMany { return $this->hasMany(School::class); }

// School
public function zip(): BelongsTo { return $this->belongsTo(Zip::class); }
// Acesso conveniente (não FK duplicada):
public function district(): HasOneThrough { /* via zip */ }
public function province(): ??? // resolver via zip->district->province, ou um accessor
```

## 4. Dados demonstrativos vs. reais (Fase 10 do mandato)

Ao popular `provinces`/`districts`/`zips` para demonstração, seguir a regra do documento
de visão estratégica (secção 14): dados demonstrativos nunca são apresentados como
estatísticas oficiais. Para a fase provincial, o seed mínimo recomendado é:

- Província da Zambézia (real, com código oficial).
- Todos os distritos reais da Zambézia (dados públicos, não inventados).
- Pelo menos uma ZIP real de Namacurra, com a escola piloto associada a ela.
- Escolas adicionais para a demonstração podem ser fictícias, mas **claramente marcadas**
  como dados de demonstração na interface (ver `docs/strategy/PROVINCIAL_PRESENTATION.md`,
  secção 10/11 — nunca afirmar integração oficial que não existe).

## 5. Validações de integridade (Fase 9 do mandato)

Antes de aceitar qualquer escrita nestas tabelas, validar:
- Uma `School` só pode ser criada/movida para um `Zip` existente.
- Um `Zip` só pode pertencer a um `District` existente.
- Um `District` só pode pertencer a uma `Province` existente.
- Um aluno/professor/turma só pode pertencer à `School` do utilizador que os está a criar
  (isto já é tratado em `DATA_SCOPING.md`/`SECURITY_MODEL.md`, mas a validação de
  pertença território-a-território fica aqui, ao nível do model territorial).
