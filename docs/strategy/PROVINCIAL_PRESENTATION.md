# ZAMEDU — Guia de Preparação para Apresentação Provincial

## 1. Objetivo da apresentação

O objetivo não é demonstrar todas as funcionalidades do sistema.

O objetivo é fazer o júri compreender rapidamente:

> **Qual problema o ZAMEDU resolve, por que é diferente e por que pode crescer para além de uma única escola.**

---

# 2. Mensagem principal

### Frase de abertura

> **"O ZAMEDU não foi pensado apenas para gerir uma escola. Foi pensado para transformar dados de várias escolas em informação útil para quem ensina, quem dirige e quem acompanha a educação a nível territorial."**

---

# 3. Problema

Muitas soluções de gestão escolar concentram-se na administração interna de uma única escola.

Isso resolve:

- matrícula;
- notas;
- pagamentos;
- alunos;
- professores.

Mas surge uma pergunta:

> **E quando precisamos compreender o que está acontecendo em várias escolas ao mesmo tempo?**

O problema passa a ser:

- Como acompanhar várias escolas?
- Como comparar desempenho?
- Como identificar escolas que precisam de atenção?
- Como consolidar dados por ZIP?
- Como consolidar dados por Distrito?
- Como produzir uma visão provincial?

É aqui que entra a proposta do ZAMEDU.

---

# 4. Solução

O ZAMEDU utiliza uma estrutura territorial:

```text
PROVÍNCIA
   ↓
DISTRITO
   ↓
ZIP
   ↓
ESCOLA
```

Cada escola opera autonomamente.

Os níveis superiores recebem uma visão consolidada.

---

# 5. O grande diferencial

Não dizer apenas:

> "O nosso sistema suporta várias escolas."

Dizer:

> **"Cada escola possui o seu ambiente operacional, mas os níveis administrativos podem acompanhar indicadores agregados dentro do seu âmbito de responsabilidade."**

Essa diferença é muito mais forte.

---

# 6. Demonstração ideal

A apresentação deve contar uma história.

## Cena 1 — Escola

Entrar como Diretor da Escola.

Mostrar:

- alunos;
- professores;
- turmas;
- frequência;
- desempenho;
- pagamentos;
- relatórios.

Mensagem:

> "Aqui a escola trabalha normalmente."

---

## Cena 2 — ZIP

Entrar como administrador do ZIP.

Mostrar:

- número de escolas;
- total de alunos;
- frequência média;
- desempenho médio;
- escolas com alertas;
- comparação entre escolas.

Mensagem:

> "Agora deixamos de olhar apenas para uma escola."

---

## Cena 3 — Distrito

Entrar como administrador distrital.

Mostrar:

- ZIPs;
- escolas;
- alunos;
- indicadores;
- comparação entre ZIPs;
- tendências.

Mensagem:

> "O Distrito consegue acompanhar a realidade agregada das suas escolas."

---

## Cena 4 — Província

Entrar como administrador provincial.

Mostrar:

- distritos;
- total de escolas;
- total de alunos;
- frequência;
- desempenho;
- indicadores financeiros, quando apropriado;
- alertas territoriais.

Mensagem:

> "A Província obtém uma visão consolidada sem retirar à escola o controlo dos seus próprios dados."

---

# 7. O momento "wow"

O melhor momento da demonstração deve ser:

```text
Província
    ↓
Distrito
    ↓
ZIP
    ↓
Escola
```

Selecionar um indicador provincial.

Depois fazer drill-down.

Exemplo:

> "Temos um indicador provincial de frequência de 90%."

Clicar:

> Distrito A — 92%

Depois:

> ZIP 03 — 89%

Depois:

> Escola X — 84%

Depois:

> Turma 7B — 78%

Isso demonstra que o sistema não é apenas um dashboard bonito.

Existe uma cadeia entre:

**dado operacional → indicador → decisão.**

---

# 8. Exemplo de pergunta do júri

### "Qual é a inovação?"

Resposta sugerida:

> "A inovação não está apenas em digitalizar a gestão escolar. O ZAMEDU cria uma arquitetura em que a escola continua sendo a unidade operacional, enquanto ZIP, Distrito e Província podem acompanhar indicadores agregados dentro do seu âmbito de responsabilidade. Assim, os dados deixam de servir apenas para registrar o que aconteceu e passam também a apoiar monitoria e decisão."

---

# 9. "Mas outros candidatos também têm sistemas escolares."

Resposta:

> "Sim. A gestão escolar tradicional é importante e o ZAMEDU também faz isso. A nossa diferenciação está na camada territorial e analítica. Não pensamos apenas na pergunta 'como gerir esta escola?', mas também 'como acompanhar várias escolas sem perder a autonomia e a segurança dos dados de cada uma?'"

---

# 10. "O sistema já funciona em várias escolas?"

Se estiver em ambiente de demonstração:

> "A arquitetura está sendo preparada para operação multi-tenant. Para a demonstração utilizamos dados operacionais e/ou demonstrativos claramente identificados. O objetivo é demonstrar a capacidade arquitetural de escalar da escola para ZIP, Distrito e Província."

Não afirmar integração oficial com instituições públicas se ela não existir.

---

# 11. "Vocês têm dados oficiais?"

Resposta segura:

> "O sistema pode trabalhar com dados inseridos pelas escolas e gerar indicadores a partir desses dados. Quando houver integração formal com fontes oficiais, esses dados poderão alimentar a plataforma. Nesta demonstração, distinguimos claramente dados operacionais e demonstrativos de estatísticas oficiais."

---

# 12. "Como garantem segurança?"

Resposta:

> "A segurança não depende apenas da interface. O sistema aplica autenticação, permissões e escopo territorial. Um utilizador escolar não pode simplesmente alterar o ID de uma escola numa URL e obter dados de outra escola. As consultas, políticas, serviços, APIs, relatórios e exportações precisam respeitar o mesmo contexto."

---

# 13. "E se uma escola deixar de utilizar o sistema?"

Resposta:

> "A arquitetura SaaS permite tratar cada escola como um tenant independente. O ciclo de vida da escola pode ser administrado sem comprometer os dados das restantes instituições."

---

# 14. "Por que SaaS?"

Resposta:

> "Porque permite que a mesma plataforma seja utilizada por diferentes escolas sem transformar cada instalação numa aplicação isolada. Isso reduz duplicação, facilita manutenção, permite atualizações centralizadas e cria uma base para expansão territorial."

---

# 15. O que NÃO fazer na apresentação

Não gastar muito tempo mostrando:

- login;
- CRUD simples;
- menus;
- configurações;
- detalhes técnicos de código;
- centenas de telas;
- funcionalidades que não diferenciam o projeto.

Não começar dizendo:

> "Usamos Laravel, MySQL, Tailwind..."

Tecnologia é importante, mas não é a proposta de valor.

---

# 16. Ordem recomendada

```text
1. Problema
2. Solução
3. Diferencial
4. Escola
5. ZIP
6. Distrito
7. Província
8. Drill-down
9. Segurança
10. Escalabilidade
11. Impacto
12. Futuro
```

---

# 17. Visão de futuro

Depois da fase provincial, o ZAMEDU pode evoluir para:

- indicadores educacionais mais avançados;
- relatórios parametrizáveis;
- alertas automáticos;
- acompanhamento de frequência;
- análise de desempenho;
- comparação territorial;
- integração com APIs;
- PWA;
- notificações;
- IA para análise de indicadores;
- previsão de risco acadêmico;
- recomendações para gestores.

Mas não prometer tudo como se já estivesse implementado.

Diferenciar:

**Já implementado**
→ **Em desenvolvimento**
→ **Roadmap**

---

# 18. Frase de encerramento

> **"O nosso objetivo não é simplesmente colocar a escola no computador. É criar uma plataforma onde os dados produzidos na escola possam, com segurança e dentro das permissões corretas, transformar-se em informação para o ZIP, para o Distrito e para a Província."**

---

# 19. Checklist antes da apresentação

## Produto

- [ ] Todas as telas principais funcionando
- [ ] Dados demonstrativos coerentes
- [ ] Dashboards sem números contraditórios
- [ ] Drill-down funcionando
- [ ] Filtros funcionando
- [ ] Relatórios funcionando

## Segurança

- [ ] Escola A não vê Escola B
- [ ] ZIP não vê outro ZIP
- [ ] Distrito não vê outro Distrito
- [ ] URL manipulation testada
- [ ] API testada
- [ ] Exportação testada

## Demonstração

- [ ] Conta Escola
- [ ] Conta ZIP
- [ ] Conta Distrito
- [ ] Conta Província
- [ ] Dados preparados
- [ ] Roteiro ensaiado
- [ ] Plano B offline

## Comunicação

- [ ] Problema explicado em menos de 1 minuto
- [ ] Diferencial explicado em menos de 1 minuto
- [ ] Demo principal ensaiada
- [ ] Respostas para perguntas difíceis preparadas
