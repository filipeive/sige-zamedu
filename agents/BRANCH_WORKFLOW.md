# BRANCH_WORKFLOW.md
## Workflow de Branches e Pull Requests

---

## 1. Regra fixa

**Um sprint (ver `docs/architecture/MIGRATION_PLAN.md`) = uma branch = um Pull Request.**
Nunca commitar diretamente em `main`. Nunca misturar dois sprints/fases diferentes no
mesmo PR — se durante o trabalho descobrir que precisa de tocar em algo de outro sprint,
pare, documente a dependência em `agents/HANDOFF.md`, e trate-a como um PR separado
(mesmo que pequeno).

## 2. Nomenclatura de branches

```
feature/<nome-do-sprint>     — nova funcionalidade (ex: feature/territorial-model)
fix/<descrição-curta>        — correção de bug não planeado (ex: fix/grade-sheet-view)
docs/<descrição-curta>       — só documentação (ex: docs/multi-tenant-foundations)
refactor/<descrição-curta>   — refactor sem mudança de comportamento
```

Usar os nomes exatos de branch já definidos em `MIGRATION_PLAN.md` para os sprints da
transformação multi-tenant, para que o histórico de branches leia como o próprio plano.

## 3. Checklist de PR (copiar para a descrição do PR)

```markdown
## Sprint / Fase
(referência a docs/architecture/MIGRATION_PLAN.md)

## O que muda
(resumo objetivo)

## Análise prévia (regra de ouro do agente — AGENT_GUIDELINES.md secção 5)
1. O que existe atualmente?
2. O que foi alterado?
3. Dependências afetadas:
4. Dados que podem quebrar:
5. Permissões afetadas:
6. Módulos com risco de regressão:
7. Como foi testado:
8. Como reverter, se necessário:

## Isolamento de dados (se aplicável — DATA_SCOPING.md)
- [ ] Esta mudança escreve dados? Usa exclusivamente SchoolScope/school_id?
- [ ] Esta mudança lê dados agregados de várias escolas? Usa HierarchyScopeResolver e é
      explicitamente só-leitura?
- [ ] Não há caminho (rota/API/export) que misture os dois padrões.

## Testes
- [ ] Testes automatizados passam (`php artisan test`)
- [ ] Testes de isolamento passam (se este PR tocar em models de negócio)
- [ ] O fluxo de pauta/notas (grades/*) continua idêntico para a escola piloto

## Documentação atualizada
- [ ] docs/architecture/*.md relevante atualizado, se a decisão mudou algo aí
- [ ] docs/architecture/DECISIONS.md tem entrada nova, se foi tomada uma decisão de
      arquitetura nesta PR
- [ ] agents/HANDOFF.md tem entrada nova para esta sessão de trabalho

## Próximo passo
(o que deve acontecer depois deste PR — vai para o HANDOFF.md também)
```

## 4. Pontos de não-retorno

PRs que envolvam qualquer um destes itens exigem aprovação explícita do Filipe,
registada em `docs/architecture/DECISIONS.md`, **antes do merge**:
- Tornar `school_id` (ou qualquer coluna) `NOT NULL` sobre dados reais existentes.
- Qualquer `DROP`, `TRUNCATE`, ou migração destrutiva.
- Qualquer alteração à fórmula de cálculo em `app/Models/Grade.php`.
- Qualquer alteração que remova ou contorne `SchoolScope`.

## 5. Revisão entre agentes

Se um agente de IA abrir um PR, idealmente outro agente (ou o próprio Filipe) revê antes
do merge — em particular qualquer PR que toque em `DATA_SCOPING.md`/`SECURITY_MODEL.md`.
Não é uma regra bloqueante formalizável tecnicamente neste projeto, mas é a prática
recomendada dada a sensibilidade da área (fuga de dados entre escolas).
