# DATA_AGGREGATION.md
## Motor de Indicadores e Relatórios por Escopo

Pré-requisito de leitura: `DATA_SCOPING.md` (a distinção isolamento vs. agregação é a
base deste documento).

---

## 1. Princípio (de `docs/strategy/VISION_AND_ARCHITECTURE.md`, secção 8)

```
Dados da Escola → Indicadores da Escola → Agregação ZIP → Indicadores do ZIP
→ Agregação Distrito → Indicadores Distritais → Agregação Provincial
→ Inteligência Educacional
```

Não duplicar dados operacionais só para alimentar dashboards. Preferir queries de
agregação sobre os dados reais; só introduzir tabelas de métricas materializadas
(`reporting tables`) se o volume justificar (Fase 7 do mandato) — e essa decisão fica
registada em `DECISIONS.md`, não é tomada implicitamente por um agente.

## 2. Arquitetura do motor de relatórios (de `docs/strategy/REPORTS_BY_ROLE.md`, secção 18)

```
Report Definition
       ↓
Scope Resolver (HierarchyScopeResolver — ver DATA_SCOPING.md)
       ↓
Filters (ano letivo, período, dentro do scope do utilizador)
       ↓
Query / Aggregation
       ↓
Metrics
       ↓
Presentation: Dashboard | Tabela | PDF | Excel
```

Uma definição de relatório serve todas as apresentações — não criar uma lógica de
dashboard e outra, diferente, para o PDF do mesmo relatório (isto violaria também a
regra de segurança de `SECURITY_MODEL.md`: exports têm de respeitar exatamente o mesmo
scope que o ecrã).

### 2.1 Exemplo de definição (adaptado do documento de relatórios, secção 19)

```php
'student_attendance' => [
    'allowed_scopes' => ['teacher', 'school', 'zip', 'district', 'province'],
    'dimensions' => ['province', 'district', 'zip', 'school', 'class', 'student'],
    'metrics' => ['attendance_rate', 'absence_count'],
    'filters' => ['academic_year', 'period', 'school', 'class'],
]
```

O motor decide automaticamente **quais dimensões o utilizador pode ver** com base no seu
`administrative_level` (via `SchoolContext`) — nunca é o frontend a decidir isto.

## 3. Indicadores por nível (referência de `docs/strategy/REPORTS_BY_ROLE.md`)

- **Escola**: dados individuais quando necessário (aluno, turma).
- **ZIP**: indicadores por escola (comparação entre escolas do ZIP), não por aluno.
- **Distrito**: indicadores por ZIP (comparação entre ZIPs).
- **Província**: indicadores por Distrito (comparação entre Distritos).

Regra de privacidade (secção 17 do documento de relatórios): quanto mais alto o nível,
mais o relatório deve preferir dados agregados a dados individuais. Detalhe individual
só aparece via drill-down explícito e autorizado.

## 4. Categorias de indicadores a suportar (secção 11 do documento de relatórios)

- Matrícula: alunos ativos, novas matrículas, transferências, desistências, distribuição
  por classe/sexo.
- Frequência: taxa média, faltas, absentismo, tendência mensal, turmas críticas.
- Aproveitamento: média, aprovação/reprovação, desempenho por disciplina.
- Recursos humanos: professores ativos, distribuição, relação professor/aluno.
- Financeiro: só onde fizer sentido e conforme permissões (receitas, pendências).

## 5. Alertas e tendências

Um indicador isolado ("frequência = 90%") vale menos que uma tendência
("Jan 87% → Fev 88% → Mar 89%... está a melhorar") ou um alerta
("Escola X: frequência caiu 12 pontos face ao período anterior"). Isto transforma o
relatório de "registo do que aconteceu" em ferramenta de monitoria e decisão — é o
argumento central da apresentação provincial (ver `docs/strategy/PROVINCIAL_PRESENTATION.md`).

## 6. Performance (Fase 13 do mandato)

- Índices obrigatórios em `school_id`, `zip_id`, `district_id`, `province_id` e
  combinações relevantes assim que as tabelas territoriais existirem.
- Evitar N+1 — usar eager loading (`with()`) sempre que um dashboard percorrer
  escolas/turmas.
- Paginação em qualquer listagem territorial (uma província pode ter centenas de
  escolas).
- Caching só quando houver necessidade real medida, não preventivamente.
