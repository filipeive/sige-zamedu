# ZAMEDU — Modelo de Relatórios Orientados ao Usuário e ao Escopo

## 1. Ideia central

O relatório não deve ser simplesmente:

> "uma página com dados."

Deve ser:

> **uma visão da informação adequada ao papel, responsabilidade e nível administrativo do utilizador.**

O mesmo sistema de dados pode produzir diferentes relatórios para diferentes utilizadores.

---

# 2. Regra fundamental

```text
DADOS OPERACIONAIS
       ↓
ESCOPO DO UTILIZADOR
       ↓
INDICADORES RELEVANTES
       ↓
RELATÓRIO
```

Portanto:

**Role ≠ Scope**

Um Diretor pode ter uma role administrativa, mas seu escopo é uma escola.

Um administrador provincial pode ter permissões de gestão, mas seu escopo é uma província.

---

# 3. Matriz de relatórios

| Usuário | Escopo | Principal objetivo |
|---|---|---|
| Professor | Turmas atribuídas | Acompanhar ensino |
| Diretor | Escola | Gerir escola |
| Admin ZIP | ZIP | Monitorar escolas |
| Admin Distrito | Distrito | Monitorar ZIPs |
| Admin Provincial | Província | Monitorar Distritos |
| Super Admin | Global | Administração da plataforma |

---

# 4. Relatórios do Professor

## Dashboard

```text
Minhas Turmas
     ↓
Alunos
     ↓
Frequência
     ↓
Notas
     ↓
Alertas
```

### Indicadores

- alunos por turma;
- frequência média;
- alunos abaixo do desempenho esperado;
- avaliações pendentes;
- notas lançadas;
- alunos em risco.

### Relatórios

- pauta;
- aproveitamento;
- frequência;
- alunos por turma;
- desempenho por disciplina.

O professor não deve receber indicadores financeiros ou administrativos que estejam fora do seu escopo.

---

# 5. Relatórios do Diretor

## Visão escolar

```text
ESCOLA
├── Matrículas
├── Alunos
├── Professores
├── Turmas
├── Frequência
├── Desempenho
├── Finanças
└── Ocorrências
```

### Dashboard

Cards:

- total de alunos;
- alunos ativos;
- professores;
- turmas;
- frequência média;
- desempenho médio;
- pagamentos pendentes;
- alertas.

### Relatórios

- matrícula;
- frequência;
- aproveitamento;
- professores;
- turmas;
- situação financeira;
- documentos emitidos;
- ocorrências;
- desempenho por disciplina.

---

# 6. Relatórios do Administrador ZIP

Aqui ocorre a primeira grande mudança.

O relatório deixa de ser centrado em pessoas e passa a ser centrado em **escolas e indicadores**.

## Dashboard

```text
ZIP 01

Escolas: 8
Alunos: 4.250
Professores: 183

Frequência média: 91%
Aproveitamento: 78%

Escolas em atenção: 2
```

### Comparação entre escolas

| Escola | Alunos | Frequência | Aproveitamento | Estado |
|---|---:|---:|---:|---|
| Escola A | 650 | 94% | 82% | Normal |
| Escola B | 510 | 88% | 71% | Atenção |
| Escola C | 730 | 93% | 80% | Normal |

O objetivo não é expor indiscriminadamente todos os alunos.

É permitir:

**monitoria → identificação de problema → drill-down autorizado.**

---

# 7. Relatórios do Administrador Distrital

## Dashboard

```text
DISTRITO

ZIPs: 12
Escolas: 86
Alunos: 41.800
Professores: 1.920

Frequência: 89%
Aproveitamento: 76%

ZIPs em atenção: 3
Escolas em atenção: 14
```

### Comparação por ZIP

| ZIP | Escolas | Alunos | Frequência | Aproveitamento |
|---|---:|---:|---:|---:|
| ZIP 01 | 8 | 4.250 | 91% | 78% |
| ZIP 02 | 6 | 3.120 | 87% | 72% |
| ZIP 03 | 10 | 5.600 | 93% | 81% |

---

# 8. Relatórios do Administrador Provincial

A Província deve receber uma visão executiva.

## Dashboard

```text
PROVÍNCIA

Distritos: 14
ZIPs: 120
Escolas: 840

Alunos: 420.000
Professores: 18.000

Frequência média: 90%
Aproveitamento médio: 77%

Distritos em atenção: 3
```

---

# 9. Relatório provincial por Distrito

| Distrito | Escolas | Alunos | Frequência | Aproveitamento | Atenção |
|---|---:|---:|---:|---:|---|
| Distrito A | 80 | 39.000 | 92% | 81% | Não |
| Distrito B | 64 | 31.500 | 86% | 70% | Sim |
| Distrito C | 91 | 45.200 | 90% | 76% | Não |

O relatório deve permitir clicar em:

**Distrito B**

e abrir:

```text
Distrito B
   ↓
ZIPs
   ↓
Escolas
   ↓
Indicadores
```

---

# 10. Drill-down

O conceito mais importante do módulo de relatórios.

Exemplo:

```text
Província
  │
  └── Distrito B
        │
        └── ZIP 04
              │
              └── Escola X
                    │
                    └── 7ª Classe
                          │
                          └── Turma B
```

Cada nível mostra mais detalhe.

Mas cada nível deve continuar sujeito à autorização.

---

# 11. Tipos de indicadores

## Matrícula

- alunos ativos;
- novas matrículas;
- transferências;
- desistências;
- distribuição por classe;
- distribuição por sexo.

## Frequência

- frequência média;
- faltas;
- absentismo;
- tendência mensal;
- turmas críticas.

## Aproveitamento

- média;
- aprovação;
- reprovação;
- desempenho por disciplina;
- distribuição por classe.

## Recursos humanos

- professores ativos;
- distribuição por escola;
- qualificações;
- relação professor/aluno.

## Financeiro

Somente onde fizer sentido e conforme as permissões:

- receitas;
- pagamentos;
- valores pendentes;
- inadimplência;
- despesas.

---

# 12. Sistema de alertas

O relatório pode ir além de números.

Exemplo:

```text
⚠ ALERTA

Escola X
Frequência: 74%

↓ 12 pontos comparado ao período anterior.
```

Outro:

```text
⚠ DESEMPENHO

ZIP 04

3 escolas apresentam aproveitamento
abaixo do limite configurado.
```

Isso transforma o relatório em ferramenta de gestão.

---

# 13. Tendências

Não mostrar apenas:

> Frequência = 90%

Mostrar:

```text
Jan   87%
Fev   88%
Mar   89%
Abr   90%
Mai   91%
```

Pergunta que o relatório deve responder:

> "Está melhorando ou piorando?"

---

# 14. Comparação

Os níveis administrativos devem poder comparar unidades dentro do próprio escopo.

Exemplo:

```text
Distrito
   ├── ZIP 01 → 92%
   ├── ZIP 02 → 84%
   ├── ZIP 03 → 90%
   └── ZIP 04 → 76%
```

O sistema pode destacar:

- melhor desempenho;
- menor desempenho;
- maior crescimento;
- maior queda.

Sem transformar ranking em punição automática.

---

# 15. Filtros

Todos os relatórios administrativos devem permitir filtros adequados ao escopo.

Exemplo provincial:

```text
Ano letivo
↓
Distrito
↓
ZIP
↓
Escola
↓
Classe
↓
Período
```

Mas o utilizador só deve conseguir selecionar unidades dentro do seu escopo.

---

# 16. Exportação

Relatórios podem ser exportados conforme permissões:

- PDF;
- Excel;
- CSV;
- impressão.

A exportação deve aplicar exatamente as mesmas regras de autorização do dashboard.

**Não criar uma regra de segurança diferente para exportação.**

---

# 17. Relatórios e privacidade

Quanto maior o nível administrativo, maior deve ser a preferência por:

**dados agregados.**

Exemplo:

### Escola

Pode consultar:

> João — 78% de frequência.

### ZIP

Preferencialmente:

> Escola A — 91% de frequência.

### Distrito

Preferencialmente:

> ZIP 04 — 88% de frequência.

### Província

Preferencialmente:

> Distrito X — 90% de frequência.

Detalhes individuais só devem aparecer quando:

1. forem necessários;
2. estiverem dentro do escopo;
3. houver autorização.

---

# 18. Arquitetura do motor de relatórios

Evitar criar uma lógica completamente diferente para cada dashboard.

Preferir:

```text
Report Definition
       ↓
Scope Resolver
       ↓
Filters
       ↓
Query / Aggregation
       ↓
Metrics
       ↓
Presentation
       ├── Dashboard
       ├── Table
       ├── PDF
       └── Excel
```

Assim, o mesmo relatório pode ter múltiplas apresentações.

---

# 19. Exemplo de definição conceptual

```text
Report:
    student_attendance

Allowed scopes:
    teacher
    school
    zip
    district
    province

Dimensions:
    province
    district
    zip
    school
    class
    student

Metrics:
    attendance_rate
    absence_count

Filters:
    academic_year
    period
    school
    class
```

O motor decide automaticamente quais dimensões o usuário pode visualizar.

---

# 20. O relatório ideal

Um bom relatório deve responder:

### O quê?

Qual é o indicador?

### Onde?

Em qual escola, ZIP, Distrito ou Província?

### Quando?

Em qual período?

### Está melhorando?

Qual é a tendência?

### Onde está o problema?

Quais unidades estão abaixo do esperado?

### Posso investigar?

Existe drill-down autorizado?

---

# 21. Resultado esperado

A evolução do ZAMEDU transforma:

```text
CRUD
```

em:

```text
DADOS
  ↓
INFORMAÇÃO
  ↓
INDICADORES
  ↓
MONITORIA
  ↓
ALERTAS
  ↓
DECISÃO
```

Esse é o verdadeiro valor da camada de relatórios.

---

# 22. Regra para os AI Agents

Sempre que um agente criar um relatório, perguntar:

1. Quem é o usuário?
2. Qual é o seu escopo?
3. Que dados ele pode ver?
4. Qual decisão esse relatório ajuda a tomar?
5. O indicador é agregado ou individual?
6. Existe drill-down?
7. O drill-down é autorizado?
8. Os filtros respeitam o escopo?
9. PDF/Excel respeitam o mesmo escopo?
10. O relatório funciona com dados de múltiplas escolas?

Se não houver resposta clara para essas perguntas, o relatório ainda não está arquiteturalmente completo.
