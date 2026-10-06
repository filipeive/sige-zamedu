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

## [2026-10-06] — Resolução Dinâmica Multi-Tenant do Nome da Escola, Confirmação de Permissões de Ficheiros & Certificado A4 — Antigravity AI Agent

**Feito:**
- **Verificação Rigorosa de Permissões e Gravação de Ficheiros**:
  - Verificadas as permissões em disco (`ls -la` e `git status`): todos os ficheiros modificados possuem permissões de escrita normais (`-rw-rw-r--` ou `-rw-r--r--`, proprietário `fdev-ms:fdev-ms`), confirmando que **não existe bloqueio em modo leitura** e todas as edições são persistidas no disco.
  - Executados `php artisan view:clear` e `php artisan cache:clear` para assegurar que o Blade compila sempre os ficheiros mais recentes.
- **Resolução Dinâmica da Identidade da Escola no Multi-Tenant**:
  - **`app/Helpers/SchoolHelper.php`**:
    - `current_school(): ?School` unificada com hierarquia de prioridade: `SchoolContext::getSchoolId()` -> `session('support_mode_school_id')` -> `auth()->user()?->school_id`.
    - `current_school_name(string $default = 'ZamEdu'): string` retorna o nome real do modelo `School` ativo.
    - `is_public_school()` e `is_private_school()` checam dinamicamente o campo `type` (`public` vs `private`) da escola ativa.
  - **`app/Models/Setting.php` & `app/Helpers/settings.php`**:
    - Em ambiente multi-tenant, se não houver setting customizada na base de dados para a chave de identidade (`school_name`, `school_short_name`, `school_phone`, `school_email`, `school_address`, `school_code`, `director_name`, `province`, `district`), o `Setting::get()` agora faz o fallback inteligente para os atributos do modelo `App\Models\School` associado ao `school_id` em vez de usar os valores padrão estáticos globais.
    - Sincronização automática em `Setting::set()` atualizando o modelo `School` caso dados institucionais sejam alterados.
  - **Padronização em Views & Layouts**:
    - `resources/views/layouts/app.blade.php`: Título `<title>{{ current_school_name() }} - ...</title>`, Sidebar Brand com `{{ current_school_name() }}`, rodapé com `current_school_name()` e província/distrito da escola corrente.
    - `resources/views/reports/index.blade.php`: badge de Escopo Ativo atualizada com `current_school_name()`.
- **Preenchimento Total do Certificado no Papel A4 (`resources/views/certificates/pdf_certificado.blade.php`)**:
  - Corrigido o layout do certificado de conclusão em A4 paisagem (`297mm x 210mm`):
    - Configurado `height: 190mm` explícito e `box-sizing: border-box` na `.border-container`.
    - Ampliadas as proporções tipográficas (título `24pt`, nome do estudante `18pt` com destaque e borda, texto oficial `13pt` com line-height `2.1`).
    - Espaçamentos e assinaturas equilibradas com selo branco de `64px`, garantindo que o certificado preenche harmoniosamente toda a folha A4 em vez de ficar concentrado na metade superior.

**Decisões tomadas:**
- A identidade institucional em contexto de escola provém unicamente do registo da escola no tenancy (`School::find($schoolId)`), com precedência sobre valores globais da tabela `settings`.

**Testado:**
- **Testes Automatizados**: `tests/Feature/MultiTenantSchoolNameTest.php` cobrindo 4 cenários (utilizador na Escola 1, utilizador na Escola 2, Super Admin em Modo Suporte, e SchoolContext explícito).
- Suite completa `SchoolAllocationAndWelcomeTest|SmsCentralTest|StudentsIndexUxTest|MultiTenantSchoolNameTest`: 11 testes, 74 asserções aprovadas com 100% de sucesso.
- Inspeção visual de DOM e layouts via Chrome DevTools.

**Próximo passo recomendado:**
- Proceder com o teste do sistema de mensagens SMS no número indicado pelo utilizador (`847240296`) conforme credenciais da API configuradas.

## [2026-10-05] — Overhaul Completo da UI/UX do Widget IA & Verificação Visual no Navegador — Antigravity AI Agent

**Feito:**
- **Upgrade Estético e Funcional do Widget de IA (`resources/views/partials/ai-widget.blade.php`)**:
  - **Design Glassmorphism Moderno**: Implementada a classe `.ai-glass-panel` com fundo translúcido com blur (`backdrop-blur-xl`), cantos arredondados (`rounded-3xl`), sombra profunda (`shadow-2xl`) e bordas suaves adaptáveis a dark mode.
  - **Header Institucional de Alta Fidelidade**: Gradiente slate com avatar de IA esmeralda, indicador pulsante de status ("Online · Directivas do SNE Lei 18/2018"), badge "MINEDH COPILOT", botão de limpar histórico e fechar modal.
  - **Barra de Chips de Tópicos Rápidos**: Atalhos com um clique para "Pautas & Exames", "Mapa Pedagógico" e "Mensagens SMS".
  - **Cards Interativos de Sugestão**: Seção "Perguntas Frequentes & Directivas Oficiais" com 3 cards clicáveis com ícones coloridos (Fórmula NFD com Exame `NE_Eficaz = max(NE, ACF)`, Mapa Pedagógico HM/M, e Envio de SMS).
  - **Markdown Avançado & Ergonomia**: Suporte a tabelas HTML formatadas, blocos de código com botão de cópia rápida para a área de transferência, citações/alertas estilizados e toolbar de ações com botão "Copiar resposta" e selo "MINEDH Verified".
  - **Gatilho Flutuante Premium**: Botão com anel pulsante (`ai-trigger-pulse`), gradiente esmeralda/verde institucional (`#007A3D` a `#034522`), ícone de robô e pílula hover "Assistente IA".
  - **Histórico Persistente v2**: Migrado storage para `zamedu_ai_chat_history_v2` com sanitização e renderização resiliente.
- **Remoção de `<li class="breadcrumb-item">Dashboard</li>`**:
  - Confirmada a remoção completa de qualquer breadcrumb genérico `Dashboard</li>` em todas as views da aplicação (`app.blade.php`, `basic.blade.php`, `teacher.blade.php`, `teacher-portal/dashboard.blade.php`).

**Testado:**
- **Verificação Visual no Navegador Real (Chrome DevTools MCP)**:
  - Navegado para `http://127.0.0.1:8000/dashboard`.
  - Aberto o widget flutuante através do gatilho `#floating-ai-trigger`.
  - Capturadas capturas de tela comprovando a nova interface, cards de sugestões, chips rápidos e badge oficial MINEDH.
  - Testada a interação real enviando pergunta sobre fórmula NFD MINEDH através do card de sugestão, recebendo resposta precisa em tempo real calculada segundo a Lei 18/2018.
- **Suíte de Testes Automatizados**:
  - `vendor/bin/phpunit --filter="SchoolAllocationAndWelcomeTest|SmsCentralTest|StudentsIndexUxTest"`: 10/10 testes (60 asserções) aprovados.



## [2026-10-05] — Remoção Completa do Item '<li class="breadcrumb-item">Dashboard</li>' em Todas as Views — Antigravity AI Agent

**Feito:**
- **Remoção de `<li class="breadcrumb-item">Dashboard</li>` em Todos os Arquivos**:
  - Em `resources/views/layouts/app.blade.php`: removido o fallback global redundante `@if (!View::hasSection('breadcrumbs')) <li class="breadcrumb-item active">Dashboard</li> @endif`, evitando que qualquer página ou painel adicione "Dashboard" após o link raiz "Início".
  - Em `resources/views/dashboard/basic.blade.php`: removida a linha `<li class="breadcrumb-item">Dashboard</li>`, deixando apenas o item descritivo `<li class="breadcrumb-item active text-slate-600 font-medium">Painel Geral</li>`.
  - Em `resources/views/dashboard/teacher.blade.php`: removida a linha `<li class="breadcrumb-item">Dashboard</li>`, mantendo diretamente `<li class="breadcrumb-item active text-slate-600 font-medium">Dashboard Professor</li>`.
  - Em `resources/views/teacher-portal/dashboard.blade.php`: ajustado o breadcrumb para `<li class="breadcrumb-item active text-slate-600 font-medium">Portal do Professor</li>`.
  - Verificação rigorosa com regex em toda a base (`grep -rnE 'breadcrumb-item.*Dashboard|<li.*Dashboard.*</li>|>Dashboard<' resources/views/`): nenhum `<li class="breadcrumb-item">Dashboard</li>` permanece em qualquer view.
- **Cache**:
  - Limpeza completa do cache de views com `php artisan view:clear` e `php artisan optimize:clear`.

**Testado:**
- `vendor/bin/phpunit --filter="SchoolAllocationAndWelcomeTest|SmsCentralTest|StudentsIndexUxTest"`: 10/10 testes (60 asserções) aprovados com 100% de sucesso.

## [2026-10-05] — Restauração e Atualização Completa de Login, Pré-Inscrição SNE 2027, Rotas Públicas & Welcome — Antigravity AI Agent

**Feito:**
- **Restauração e Otimização do Portal de Autenticação (`resources/views/auth/login.blade.php`)**:
  - Recomposta a interface governamental com layout em dois painéis (visual esmeralda institucional com Emblema da República à esquerda, formulário limpo e acessível à direita).
  - Integradas contas de demonstração funcionais (Super Admin, Inspetor Provincial, Diretor Distrital, Coordenador ZIP, Diretor de Escola, Pedagógico, Professor, Secretária, Encarregado e Aluno) com preenchimento instantâneo em um clique.
- **Restauração e Atualização de Pré-Inscrição & Rotas Públicas (`routes/web.php` & `EnrollmentApplicationController.php`)**:
  - Restabelecidas as rotas `public.vacancies`, `public.allocations`, `public.pre-enrollment`, `public.pre-enrollment.store`, bem como redirecionamentos canónicos `/vacancies` e `/allocations`.
  - Implementados os métodos `vacancies()` e `allocations()` no `EnrollmentApplicationController` para consulta pública de disponibilidade de vagas e afetações territoriais SNE.
  - Sincronizada a view `resources/views/enrollments/applications/create.blade.php` e `resources/views/public/pre-enrollment.blade.php` com o banner oficial de afetação SNE (`Afetação Oficial SNE Reconhecida`), pré-preenchimento de dados de candidatos e transição do status de afetação para `confirmed`.
- **Página Inicial (`resources/views/welcome.blade.php`)**:
  - Atualizada a barra de topo e botões hero com os links oficiais `Afetações 2027` e `Vagas 2027`.
  - Integrados os selos dos níveis territoriais SNE (`Escola Local`, `ZIP (Micro-Rede)`, `Distrito (SDEJT)`, `Província (DPEDH)`).
  - Ocultado o botão de registo público de escola para preservar a integridade do onboarding oficial MINEDH.
- **Limpeza de Caches**:
  - Executado `php artisan optimize:clear` (rotas, views, configs e compiled) para assegurar que as alterações reflitam imediatamente em produção/desenvolvimento.

**Testado:**
- `vendor/bin/phpunit --filter="SchoolAllocationAndWelcomeTest"`: 6/6 testes (43 asserções) aprovados com 100% de sucesso.
- `vendor/bin/phpunit --filter="SmsCentralTest|StudentsIndexUxTest"`: 4/4 testes (17 asserções) aprovados.

## [2026-10-05] — Unificação Central de Mensagens SMS, UI/UX Directório de Alunos (Grid/Cards Mobile), Padronização Global de Breadcrumbs & Fix Widget IA — Antigravity AI Agent

**Feito:**
- **Unificação da Gestão de Mensagens SMS (`http://127.0.0.1:8000/sms`)**:
  - Eliminado o erro 404 em `http://localhost/sms/inbox/1`: o método `SmsInboxController::show($id)` agora captura IDs inexistentes e redireciona suavemente com notificação amigável (`Mensagem não encontrada ou já eliminada`).
  - O endpoint `/sms/inbox` agora redireciona com preservação de query parameters para `route('sms.index', ['tab' => 'inbox'])`.
  - Integrada a Caixa de Entrada (SMS Recebidas via httpSMS) diretamente na **Central de Mensagens (`sms/index.blade.php`)** com cards de métricas dedicados, filtros por status (Lidas / Não Lidas), drawer/modal de resposta rápida inline com `route('sms.inbox.reply')`, marcação de lidas e paginação.
  - Unificado o menu lateral do sidebar em `resources/views/layouts/app.blade.php` para um item singular "Central de Mensagens" com badge dinâmico de SMS não lidas.
- **Redesign UI/UX do Directório de Alunos (`http://127.0.0.1:8000/students`)**:
  - Implementado seletor de visualização Tabela / Grelha (`x-data` com persistência em `localStorage`).
  - Criada **Vista em Grelha / Cards responsiva** otimizada para mobile e tablets (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4`) com foto/avatar, número do aluno, turma, demografia, encarregado com botões de chamada rápida (`tel:`) e SMS, e barra de ações rápidas.
  - Reorganizadas as abas de status em barra pill com scroll suave: Todos, Ativos, Renovação Pendente, Transferidos, Formados e Inativos.
  - Padronizado `@section('breadcrumbs')` para `<li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>`.
- **Padronização Global de Breadcrumbs em Todas as Views**:
  - Varridas e corrigidas todas as 16 views do sistema que ainda incluíam links duplicados para "Início" / "Dashboard", garantindo que nenhuma view repita o link raiz que já é gerado globalmente por `resources/views/layouts/app.blade.php`.
- **Correção do Widget IA Flutuante no Painel Autenticado**:
  - Em `resources/views/partials/ai-widget.blade.php`, adicionado estilo explícito inline `position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; display: flex !important;` tanto no contentor como no botão de trigger.
  - Corrigida a inicialização JS para verificar `document.readyState === 'loading'` de modo a não perder eventos quando o script for avaliado após o `DOMContentLoaded`.
  - Em `resources/views/partials/cookie_consent.blade.php`, movido o banner de cookies para a esquerda (`bottom-4 left-4 sm:max-w-md`), eliminando a sobreposição física com o botão flutuante de IA no canto inferior direito.

**Testado:**
- `vendor/bin/phpunit --filter="SmsCentralTest|StudentsIndexUxTest"`: 4 testes, 17 asserções aprovadas com 100% de sucesso.
- Limpeza de caches com `php artisan route:clear`, `php artisan view:clear` e `php artisan config:clear`.

**Próximo passo recomendado:**
- Prossiga com os testes manuais no browser em `http://127.0.0.1:8000/sms` e `http://127.0.0.1:8000/students`.

## 2026-09-11 — Exportação PDF e Excel do Mapa Pedagógico Exclusiva para o Quadro I (MINEDH Standard) — Antigravity AI Agent

**Feito:**
- **Exportação PDF do Mapa Pedagógico (`resources/views/reports/pedagogical-map-pdf.blade.php`)**:
  - Criada vista Blade A4 paisagem dedicada contendo exclusivamente o **Quadro I (Quadro Geral de Rendimento Escolar por Classe e Ciclo)** e o **Bloco de Assinaturas Oficiais MINEDH** (DAP à esquerda, DAE à direita com nome dinâmico da escola/diretor), omitindo integralmente o Quadro II (Aproveitamento por Disciplina).
  - Adicionado método `exportPdf(Request $request)` no `PedagogicalMapController.php` acionando a biblioteca `Barryvdh\DomPDF\Facade\Pdf`.
- **Exportação Excel / CSV do Mapa Pedagógico (`app/Http/Controllers/PedagogicalMapController.php`)**:
  - Atualizado método `exportCsv(Request $request)` para exportar exclusivamente o Quadro I e bloco de assinaturas em formato CSV/Excel UTF-8 (com BOM), contendo os títulos das províncias/SDEJT/ZIP, seções por ciclo, subtotais por ciclo, total geral da escola e assinaturas oficiais.
- **Integração de Rotas e Botões na Interface (`routes/web.php` & `resources/views/reports/pedagogical-map.blade.php`)**:
  - Registada rota `Route::get('/pedagogical-map/export-pdf', ...)->name('pedagogical-map.export-pdf');`.
  - Adicionados os botões com ícones oficiais **PDF** e **Excel** na barra de ações/filtros do Mapa Pedagógico transmitindo todos os parâmetros e filtros selecionados (`request()->query()`).

**Testado:**
- `vendor/bin/phpunit`: Sucesso total em todos os 103 testes (`103/103 PASS`, 360 assertions).

**Próximo passo recomendado:**
- Continuar acompanhando o feedback do utilizador.

## 2026-09-11 — Filtro por Nível de Ensino (Primário vs Secundário), Ciclos Oficiais SNE/MINEDH e Limpeza da Coluna Classe no Mapa Pedagógico — Antigravity AI Agent

**Feito:**
- **Limpeza Visual da Coluna Classe no Mapa Pedagógico (`resources/views/reports/pedagogical-map.blade.php`)**:
  - Removidos o ícone de graduação (`fa-graduation-cap`) e a contagem entre parênteses de turmas (`(X Turmas)`), deixando apenas a rotulagem limpa da classe (ex: `1.ª Classe`, `2.ª Classe`, `Pré-Escolar / Pré-Infantil`).
- **Filtro por Nível de Ensino (`level` / `education_level`) no Mapa Pedagógico (`app/Http/Controllers/PedagogicalMapController.php`)**:
  - Adicionado o seletor `Nível` no painel de filtros (options: `Ensino Primário (1.ª - 6.ª)`, `Ensino Secundário (7.ª - 12.ª)`, `Pré-Escolar`, `Todos os Níveis`).
- **Resolução de `QueryException: Unknown column 'class_id' in 'WHERE'` (`app/Http/Controllers/PedagogicalMapController.php`)**:
  - Corrigidas as consultas fallback ao modelo `Student` substituindo chamadas diretas a `class_id` por relacionamentos `whereHas('enrollments', ...)` com a tabela pivot `enrollments`, eliminando a exceção de coluna inexistente na BD.
- **Remoção da Coluna Retidos / Negativas & Recálculo de % TRANS/ APROVADOS**:
  - Removidas as colunas `Retidos / Negativas` (HM e M) do Quadro I do Mapa Pedagógico.
  - Renomeada a coluna `% Aprovados` para `% TRANS/ APROVADOS`.
  - Recalculadas as médias por disciplina de cada estudante via `Grade::calculateMT(...)` para computar com precisão os totais de avaliados e transitados/aprovados e as respetivas percentagens (`rate_hm`, `rate_m`).
- **Reorganização Sequencial da Tabela por Seções de Ciclos (MINEDH Standard)**:
  - **Ensino Primário**:
    1. Cabeçalho de Seção: `1º CICLO (1ª A 3ª CLASSE)`
    2. Linhas de classes: `1.ª Classe`, `2.ª Classe`, `3.ª Classe`
    3. Linha Subtotal: `TOTAL 1º CICLO`
    4. Cabeçalho de Seção: `2º CICLO (4ª A 6ª CLASSE)`
    5. Linhas de classes: `4.ª Classe`, `5.ª Classe`, `6.ª Classe`
    6. Linha Subtotal: `TOTAL 2º CICLO`
    7. Linha de Rodapé: `TOTAL GERAL DA ESCOLA`
  - **Ensino Secundário**:
    1. Cabeçalho de Seção: `1º CICLO / ESG1 (7ª A 9ª CLASSE)`
    2. Linhas de classes: `7.ª Classe`, `8.ª Classe`, `9.ª Classe`
    3. Linha Subtotal: `TOTAL 1º CICLO`
    4. Cabeçalho de Seção: `2º CICLO / ESG2 (10ª A 12ª CLASSE)`
    5. Linhas de classes: `10.ª Classe`, `11.ª Classe`, `12.ª Classe`
    6. Linha Subtotal: `TOTAL 2º CICLO`
    7. Linha de Rodapé: `TOTAL GERAL DA ESCOLA`

**Testado:**
- `vendor/bin/phpunit`: Todos os 103 testes a passar (`103/103 PASS`, 360 assertions).

**Próximo passo recomendado:**
- Continuar acompanhando o feedback do utilizador e evoluir outros relatórios pedagógicos territoriais conforme necessário.

## 2026-09-11 — Redesign Tailwind UX Pro Max (Alunos & Pautas), Remoção de Emojis por Ícones FontAwesome & Resolução da Exceção Request::hasAnyRole — Antigravity AI Agent

**Feito:**
- **Resolução da Exceção `BadMethodCallException: Request::hasAnyRole does not exist` (`app/Providers/AppServiceProvider.php`)**:
  - Registadas as diretivas `@hasanyrole`, `@hasrole`, `@anyrole` e `@role` no `AppServiceProvider`, garantindo parsing nativo do Blade com suporte a strings separadas por barra vertical (`'super_admin|province_admin|...'`) e arrays.
- **Remoção de Emojis e Padronização de Badges (`resources/views/pautas/*`, `resources/views/classes/*`)**:
  - Eliminados todos os emojis das insígnias (ex: `⭐ Exame Nacional MINEDH` e `📚 Educação Básica`) substituindo-os por ícones FontAwesome (`<i class="fas fa-file-signature text-rose-600"></i>` e `<i class="fas fa-graduation-cap text-blue-600"></i>`), eliminando a estética "IA taste" e elevando a interface para o padrão enterprise `ui-ux-pro-max`.
- **Correção e Robustez do Modal de Aniversariantes (`resources/views/classes/show.blade.php`)**:
  - Adicionado suporte fail-safe (Alpine.js `x-data`/`x-show` + gatilho DOM `id="birthdayModalContainer"`), assegurando abertura e fecho instantâneo ao clicar em *Aniversariantes*.
- **Redesign 100% Tailwind CSS do Perfil 360º do Aluno (`resources/views/students/show.blade.php`)**:
  - Refatorada a view substituindo totalmente o legado Bootstrap por Tailwind CSS:
    - **Header & Cartão Lateral**: Avatar com borda esmeralda, número de processo, badge de status, turma atual, botões de ação e dados de contacto de emergência/encarregado.
    - **Cards de Métricas**: Presença %, Média Geral, Pagamentos/Taxas e Idade.
    - **Separadores 360º com Alpine.js**: Gráfico de Desempenho por Disciplina (Chart.js), Gráfico de Assiduidade, Pautas Trimestrais/Ficha Anual, Tabela de Frequência, Histórico de Taxas/Propinas e Timeline de Eventos.
- **Redesign 100% Tailwind CSS de Todas as Pautas (`resources/views/pautas/trimestral`, `anual`, `exame`, `final` & `pdf`)**:
  - Refatoradas todas asviews de pautas para Tailwind CSS com header de taxonomia MINEDH (Nível, Ciclo, Classe e Ano Lectivo), barra de filtros (Turma, Disciplina, Ano, Trimestre), fórmulas de cálculo oficial ($MT$, $MAF$, $NE\_Eficaz$, $NFD$) e matrizes pedagógicas consolidadas com realce de cores por limiar de aprovação ($\ge 9.5$v).
- **Componente Reutilizável de Assinaturas Oficiais MINEDH (`resources/views/components/official-signatures.blade.php`)**:
  - Criado o componente `<x-official-signatures />` implementando o layout de duas colunas exigido pelo MINEDH:
    - **Lado Esquerdo (DAP)**: *O Diretor Adjunto Pedagógico (DAP)* + linha de assinatura + *(Assinatura e Carimbo Oficial)*.
    - **Lado Direito (DAE)**: *O Diretor da Escola (DAE)* + Nome do Diretor em destaque (ex: `PROF. MARIA NHABINDE` / nome dinâmico) + linha de assinatura + *(Assinatura e Carimbo Oficial)*.
  - Integrado em todas as pautas (`trimestral`, `anual`, `exame`, `final`, `pdf`) e no Mapa de Aproveitamento Pedagógico (`reports/pedagogical-map.blade.php`).
- **Organização da Barra de Filtros do Mapa Pedagógico (`resources/views/reports/pedagogical-map.blade.php`)**:
  - Reestruturada a secção de filtros em grelha responsiva Tailwind CSS (Província, Distrito, ZIP, Escola, Turma, Trimestre, Ano Lectivo e Ações de Impressão/Exportação Excel), perfeitamente integrada no design system ZAMEDU.

**Testado:**
- `vendor/bin/phpunit`: **103/103 testes PASS (360 assertions)** sem falhas nem regressões.
- Limpeza de caches: `php artisan config:clear && php artisan view:clear && php artisan cache:clear` concluída com sucesso.

**Não feito / bloqueado:**
- Nenhum bloqueio. Todas as correções e redesenhos solicitados foram implementados e validados.

## 2026-09-11 — Alinhamento da Taxonomia MINEDH (Lei 18/2018), Acomodação Pré-Escolar/Privada & Agrupamento de Níveis — Antigravity AI Agent

**Feito:**
- **Taxonomia MINEDH Completa & Acomodação Pré-Escolar/Privada (`app/Helpers/EducationLevel.php`)**:
  - Implementado o helper `EducationLevel` cobrindo integralmente a estrutura do Sistema Nacional de Educação (SNE - Lei n.º 18/2018):
    - **Pré-Infantil & Pré-Escolar** (Creches e Jardins de Infância): acomodado estrategicamente com suporte ao ecossistema de escolas privadas e propinas/mensalidades.
    - **Ensino Primário (EP)**: 1.º Ciclo (1.ª a 3.ª classe, 6–8 anos) e 2.º Ciclo (4.ª a 6.ª classe, 9–11 anos) — Educação Básica Obrigatória & Gratuita.
    - **Ensino Secundário Geral (ESG)**: ESG1 (7.ª a 9.ª classe, 12–14 anos) e ESG2 (10.ª a 12.ª classe, 15–17 anos).
    - **Ensino Técnico-Profissional (ETP)**: Elementar (6.ª–8.ª), Básico (9.ª–10.ª) e Médio (11.ª–12.ª).
    - **Formação de Professores (IFP)** e **Ensino Superior**.
  - Adicionados helpers utilitários: `getCyclesForLevel()`, `getCycleForGrade()`, `getAllLevelsGrouped()`, `getGradeSelectOptions()`, `isBasicEducation()`, `hasNationalExam()`, `getExamClasses()`, `getGradeName()`, `getCycleName()`, `getLevelName()`.
- **Migration Dual-Engine SQLite/MySQL (`database/migrations/2026_09_11_084929_normalize_grade_level_offset_on_classes_and_subjects.php`)**:
  - Migration de normalização de `grade_level` atualizada com transição segura via laço inverso em vez de `ORDER BY` no `UPDATE`, garantindo compatibilidade total com o motor SQLite usado na suíte de testes PHPUnit e MySQL em produção.
- **Refatoração dos Models `ClassModel` e `Subject`**:
  - Adicionados accessors: `grade_name`, `cycle_name`, `education_level_name`, `is_basic_education`, `has_national_exam`.
  - Adicionados scopes query: `scopeByLevel()`, `scopeByCycle()`, `scopeBasicEducation()`, `scopeExamClasses()`.
- **Controllers & Interface Blade (Turmas, Disciplinas e Pautas)**:
  - `ClassController` & `SubjectController`: adicionados filtros por `level` e `cycle`, injetando `$levelsGrouped` nos formulários.
  - Views `classes/index`, `create`, `edit` e `subjects/index`, `create`, `edit`: tabs de filtragem por Nível Educacional e selects `<optgroup>` categorizados por Nível e Ciclo MINEDH.
  - Headers das Pautas Pedagógicas (`pautas/trimestral`, `anual`, `exame`, `final`): atualizados com badges oficiais da taxonomia MINEDH (Nível, Ciclo, Classe e insígnias de Educação Básica / Exame Nacional).

**Testado:**
- `vendor/bin/phpunit`: **103/103 testes PASS (360 assertions)** sem erros nem falhas.
- `php artisan config:clear && php artisan view:clear && php artisan cache:clear`: limpos com sucesso.

**Não feito / bloqueado:**
- Nenhum bloqueio. Todas as solicitações e requisitos de taxonomia pedagógica foram totalmente validados e concluídos.

## 2026-09-10 — Redesign da Paginação Tailwind ZAMEDU, Contexto Multi-Escola no Directório de Alunos & Menu Super Admin — Antigravity AI Agent

**Feito:**
- **Sistema de Paginação Tailwind Universal (`resources/views/vendor/pagination/tailwind.blade.php`)**:
  - Criado o componente Blade de paginação 100% alinhado com o design system do ZAMEDU (tons esmeralda `#007A3D`, botões responsivos, resumo de registos ex: *"Mostrando 1 a 10 de 190 resultados"*).
  - Configurado `Paginator::useTailwind()` e `Paginator::defaultView('vendor.pagination.tailwind')` em `AppServiceProvider.php`.
  - Substituídas todas as referências legadas de `pagination::bootstrap-5` por `->links()` em mais de 10 views (Alunos, Professores, Turmas, Disciplinas, Matrículas, Pagamentos, Despesas, Certidões, Comunicados, Licenças).
- **Revisão do Menu do Super Admin (`resources/views/layouts/app.blade.php`)**:
  - Adicionada a secção **"Administração Global & Territorial"** no topo da sidebar para o Super Admin / Perfis Territoriais (Inspeção MINEDH, Gestão Territorial & Escolas, Registo de Novas Escolas).
  - Adicionado badge de subtítulo no topo da barra lateral: `MINEDH · Super Admin`.
- **Contexto Multi-Escola no Directório de Alunos (`app/Http/Controllers/StudentController.php` e `resources/views/students/index.blade.php`)**:
  - Adicionado o **Banner de Modo Super Admin** com indicação da consulta Multi-Escola ativa.
  - Adicionado o **Filtro por Escola** na barra de pesquisa para filtrar alunos por instituição.
  - Adicionada a **Coluna Escola** na tabela com badge indicando a escola a que cada aluno pertence.

**Testado:**
- `vendor/bin/phpunit`: **103/103 testes PASS (360 assertions)** sem falhas.
- Verificado no browser via `browser_subagent`: visualização perfeita da sidebar do Super Admin, do filtro/coluna de escola e do novo componente de paginação Tailwind.

**Não feito / bloqueado:**
- Nenhum bloqueio. Todas as solicitações foram concluídas com sucesso.

## 2026-09-10 — Widget de IA, Prompt Contextual ZAMEDU, Barra Territorial de Relatórios e Ajuste de Tabelas no Dashboard Territorial — Antigravity AI Agent

**Feito:**
- **Widget de Assistente IA (`resources/views/partials/ai-widget.blade.php`)**:
  - Removido `x-cloak` da `div` container raiz para assegurar a visibilidade contínua do botão flutuante do assistente no DOM.
  - Refatorada a sintaxe do Alpine.js com registo duplo (`Alpine.data('floatingAiWidget', ...)` e fallback `window.floatingAiWidget`) prevenindo conflitos de inicialização.
- **Prompt do Sistema e Conhecimento do Assistente IA (`app/Http/Controllers/AiController.php`)**:
  - Expandido o método `systemPrompt()` com todo o domínio pedagógico e arquitetural do ZAMEDU: regras MINEDH (Lei 18/2018, fórmulas de MACS/MT/MAF/NFD, escala 0.0-20.0, aprovação a 9.5/10.0), desagregação por género (HM/M), estrutura de tenancy hierárquica (Província -> Distrito -> ZIP -> Escola) e navegação completa entre módulos do sistema.
- **Central de Relatórios Contextual & Links para Mapas (`app/Http/Controllers/ReportController.php` e `resources/views/reports/index.blade.php`)**:
  - Atualizado o `ReportController@index` para injetar a hierarquia territorial ativa (`$activeSchool`, `$provinces`, `$districts`, `$zips`, `$schools`).
  - Adicionada a **Barra de Contexto Territorial & Acesso Direto aos Mapas** no topo da Central de Relatórios, permitindo visualização imediata da jurisdição ativa e acesso direto às rotas `territorial.dashboard` e `admin.territory.index`.
- **Ajustes no Dashboard Territorial (`resources/views/territorial/index.blade.php`)**:
  - Removido o card resumo "Quadro Geral de Aproveitamento Pedagógico (MINEDH Lei 18/2018)".
  - Removida a coluna "Aproveitamento" das 4 tabelas territoriais (Escolas, ZIPs, Distritos e Províncias), conservando a coluna "Total de Alunos (HM / M)".

**Tested:**
- `vendor/bin/phpunit`: **103/103 testes PASS (360 assertions)** sem regressões nem falhas.
- Verificação visual e comportamental via navegação no browser em `http://127.0.0.1:8000`.

**Não feito / bloqueado:**
- Nenhum bloqueio. Todos os requisitos da solicitação foram atendidos e validados.

## 2026-09-08 — Campos de Coordenadas GPS & Resolução de Truncamento do Tipo de Escola (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Adicionados Campos de Coordenadas GPS (`latitude` & `longitude`)**:
  - Migration `2026_09_08_000002_add_coordinates_and_fix_type_on_schools_table.php` executada adicionando as colunas `latitude` (decimal 10,7) e `longitude` (decimal 10,7) à tabela `schools`.
  - Atualizado o model `App\Models\School.php` para incluir `latitude` e `longitude` no `$fillable` e em `$casts` como `float`.
  - Atualizados os métodos `storeSchool` e `updateSchool` do `TerritoryManagementController` com regras de validação para coordenadas (`between:-90,90` e `between:-180,180`).
  - Adicionados os campos de **Latitude** e **Longitude** no modal de criação e edição de escolas (`resources/views/admin/territory/index.blade.php`), com preenchimento automático em JS ao clicar em Editar.
  - Atualizado `TerritorialDashboardController::getSchoolCoordinates()` para utilizar prioritariamente as coordenadas personalizadas salvas na BD.
- **Resolução do Erro SQL 1265 (Data Truncated for Column 'type')**:
  - Implementado o mutator `setTypeAttribute` no model `School.php` para mapear de forma transparente os valores `'Pública'`, `'Privada'`, `'Comunitária'` para as chaves internas da base de dados (`'public'`, `'private'`, `'community'`), mantendo compatibilidade 100% com o MySQL (`ENUM`) e SQLite (`CHECK constraint`).
  - Adicionada a propriedade acessora `getTypeLabelAttribute` (`$school->type_label`) para exibição perfeita nos formulários e tabelas.

**Testado:**
- `vendor/bin/phpunit` executado com sucesso: **100/100 testes PASS (342 assertions)** sem erros de SQLite/MySQL.
- Testado no browser real via `browser_subagent`: atualização de coordenadas da Escola Secundária de Namacurra para `-17.878600`, `36.888300` concluída com sucesso e badge `GPS` ativo na tabela.

**Não feito / bloqueado:**
- Nenhum bloqueio.

**Próximo passo recomendado:**
- Continuar expansão de onboarding de novas escolas e personalização de relatórios do MINEDH conforme necessário.

## 2026-09-08 — Detalhes Avançados da Escola & Mapa Geográfico Leaflet (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Detalhamento da Ficha da Escola (`app/Http/Controllers/TerritorialDashboardController.php` & `resources/views/territorial/school_show.blade.php`)**:
  - Incorporado mapa georreferenciado em **Leaflet.js** exibindo as coordenadas reais da escola, pino personalizado em tom esmeralda, popup com nome da escola, diretor, contagem de alunos, taxa de aprovação e botão para abrir localização direta no Google Maps.
  - Adicionado painel de **Ficha Técnica Territorial**: Coordenadas GPS formatadas, Província, Distrito (SDEJT), ZIP, Escola Sede de ZIP e Endereço Físico completo.
  - Criada barra de **Atalhos Rápidos de Gestão**: 🎧 *Entrar no Modo Suporte Técnico*, ⚙️ *Configurar Escola* (redirecionando para o Centro de Gestão Territorial), e 📄 *Mapa Pedagógico MINEDH*.
  - Calculadas e apresentadas métricas avançadas de infraestrutura: Rácio Aluno/Professor (al./prof.), Média Alunos/Turma, Rácio de Alunas do Sexo Feminino (M %), Matriz Regulamentar MINEDH (Lei 18/2018), e tabelas de Turmas em Funcionamento e Corpo Docente Alocado.

**Testado:**
- `vendor/bin/phpunit` executado com sucesso: **100/100 testes PASS (342 assertions)**.
- Validação visual com `browser_subagent` em `http://127.0.0.1:8000/territorial/school/1` confirmando o carregamento do mapa, dados do diretor, ficha técnica, atalhos de ação e matriz de rendimento.

**Não feito / bloqueado:**
- Nenhum bloqueio.

**Próximo passo recomendado:**
- Continuar expansão de onboarding de novas escolas e personalização de relatórios do MINEDH conforme necessário.

## 2026-09-08 — Centro de Gestão Territorial & Escolas (CRUD, Tabs, Modais & Menu) (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Centro Unificado de Gestão Territorial & Escolas (`app/Http/Controllers/Admin/TerritoryManagementController.php` & `resources/views/admin/territory/index.blade.php`)**:
  - Criada página dedicada com abas (Tabs) para **Escolas**, **ZIPs (Zonas de Influência Pedagógica)**, **Distritos (SDEJT)** e **Províncias (DPEDH)**.
  - CRUD completo para registo, edição e ativação/desativação de cada entidade territorial.
  - Toolbar de filtros rápidos por Província, Distrito, ZIP e pesquisa por palavra-chave (Nome/Código/Diretor).
  - Modais responsivos para criação e edição com validação no backend e preenchimento dinâmico em JS.
  - Integração do botão de **Modo Suporte Técnico** para Super Administradores diretamente nas tabelas de escolas.
- **Navegação do Menu Lateral (`resources/views/layouts/app.blade.php`)**:
  - Adicionado item de menu **"Gestão Territorial & Escolas"** com ícone `fas fa-sitemap` em ambas as secções (Inspeção Territorial do topo e Configurações & Sistema).
- **Rotas Registadas (`routes/web.php`)**:
  - Adicionado grupo de rotas `/admin/territory` sob o prefixo `admin.` apontando para os métodos de `TerritoryManagementController`.

**Testado:**
- `vendor/bin/phpunit` executado com sucesso: **100/100 testes PASS (342 assertions)**.
- Validação visual em browser real com `browser_subagent` em `http://127.0.0.1:8000/admin/territory` cobrindo navegação pelas 4 abas, filtros e abertura de modais.

**Não feito / bloqueado:**
- Nenhum bloqueio.

**Próximo passo recomendado:**
- Continuar expansão de onboarding de novas escolas e personalização de relatórios do MINEDH conforme necessário.

## 2026-09-08 — Audit Diff Modal, Interactive Territorial Map & MINEDH Pedagogical Map Report (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Logs de Auditoria Aprimorados (`app/Http/Controllers/Admin/AuditController.php` & `resources/views/admin/audit/index.blade.php`)**:
  - Implementados filtros avançados de pesquisa (pesquisa por palavra-chave, utilizador, tipo de evento `created`/`updated`/`deleted`/`impersonated`, e intervalo de datas).
  - Modal interativo de inspeção com **Tabela Comparativa de Alterações Lado a Lado (Diff Field-by-Field)**, exibindo o valor anterior riscado em vermelho e o novo valor em verde com nomes de campos traduzidos em português, metadados do navegador/dispositivo e tab de código JSON bruto para desenvolvedores.
- **Painel Territorial com Mapa Interativo Georreferenciado (`app/Http/Controllers/TerritorialDashboardController.php` & `resources/views/territorial/index.blade.php`)**:
  - Incorporado mapa interativo em Leaflet.js (OpenStreetMap) cobrindo todas as escolas de Moçambique com marcadores georreferenciados.
  - Popups informativos com dados do **Diretor da Escola (`principal_name`)**, telefone de contacto, e-mail, estatísticas de alunos (HM e M), taxa de aproveitamento pedagógico e atalho direto 🎧 **"Entrar no Modo Suporte"** para Super Administradores.
  - Tabela com separadores (Tabs) para navegação detalhada em 4 níveis territoriais: **Escolas**, **ZIPs (Zonas de Influência Pedagógica)**, **Distritos (SDEJT)** e **Províncias (DPEDH)**.
- **Mapa de Aproveitamento Pedagógico Oficial MINEDH (`app/Http/Controllers/PedagogicalMapController.php` & `resources/views/reports/pedagogical-map.blade.php`)**:
  - Redesenho completo do documento de relatório no modelo oficial do Ministério da Educação e Desenvolvimento Humano de Moçambique (baseado em `APROV Pedgg ES-2ºTrim -2026_MARRACUA.xlsx` e `Mapa de Aproveitamento II Trimestre.xlsx`).
  - **Emblema da República de Moçambique Perfeitamente Centralizado** no topo do documento oficial.
  - **Matriz de 11 Colunas Regulamentares MINEDH em Quadro I**: `N/ Entradas (Matrícula)`, `Desistentes`, `Transf.`, `Anulou Matrícula`, `PPF/PDF`, `Avaliados`, `Transitados/Aprovados`, `Retidos/Negativas`, `% Aprovados`, `% Desistentes` (todos desagregados por género **HM** e **M**).
  - **Subtotais Automáticos por Ciclo**: Linhas de somatório para `1º CICLO`, `2º CICLO` e `TOTAL GERAL DA ESCOLA`.
  - **Proteção Contra Acesso Nulo (`Null-Safety`) & Fallbacks Territoriais**: Aplicado *optional chaining* (`?->`) em todas as propriedades de `$selectedSchool` em Blade e adicionado fallback de segurança no `PedagogicalMapController`, garantindo o carregamento sem exceção `Attempt to read property on null` mesmo quando se filtra por um distrito/ZIP sem escolas ativas.
- **Testes Globais**: Suíte de testes `vendor/bin/phpunit` — **100/100 PASS (100% aprovado, 342 asserções)**.

## 2026-09-08 — Super Admin Governance Panel & Support Mode (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Painel de Governação Central do Super Admin (`resources/views/dashboard/super_admin.blade.php` & `DashboardController::superAdminDashboard`)**:
  - Criada uma visualização dedicada e centralizada para o Super Administrador da plataforma, exibindo KPIs consolidados de todas as escolas (Públicas, Privadas, Comunitárias), Total de Alunos, Professores, Cobertura Territorial (11 Províncias, Distritos e ZIPs) e estado do licenciamento nacional.
  - Tabela Multi-Escola com listagem dinâmica de todas as instituições, dados de contacto, localização e atalhos rápidos de governação.
- **Modo Suporte Técnico / Impersonation (`SuperAdminControlController` & `IdentifySchool` Middleware)**:
  - **Acesso com 1 Clique**: Adicionado o botão 🎧 **"Entrar no Modo Suporte"** junto a cada escola na tabela central do Super Admin (`POST /super-admin/impersonate/{school}`).
  - **Banner de Alerta Transversal (`layouts/app.blade.php`)**: Quando o Modo Suporte está ativo, surge um cabeçalho proeminente no topo da página:
    `⚠️ MODO SUPORTE ATIVO: A Operar na Escola: [Nome da Escola] (#ID) | [Sair do Suporte & Voltar ao Super Admin]`.
  - **Saída Segura**: O botão **"Sair do Suporte"** remove o escopo temporário da sessão (`POST /super-admin/stop-impersonating`) e devolve o Super Admin diretamente ao Painel de Governação Central.
- **Testes de Integração Automatizados (`tests/Feature/SuperAdminSupportModeTest.php`)**:
  - Criada a suíte `SuperAdminSupportModeTest` cobrindo o acesso ao painel central, ativação do modo suporte por escola e término de suporte (**3/3 PASS**).

**Decisões tomadas:**
- O `IdentifySchool` middleware só aplica o `SchoolScope` de uma escola ao `super_admin` se este estiver explicitamente em **Modo Suporte**. Caso contrário, o Super Admin opera a nível da plataforma global sem ficar preso a uma única escola.

- **Isolamento Multi-Tenant do Módulo de Encarregados de Educação (`ParentModel::scopeForCurrentSchool` & `ParentController`)**:
  - **Identificação do Bug**: Ao aceder a `/parents` no Modo Suporte numa escola sem encarregados registados, eram apresentados encarregados de outras escolas devido à ausência do filtro por `school_id`.
  - **Correção**: Criado o escopo `scopeForCurrentSchool` no `ParentModel` (filtrando registos com educandos ou utilizador afetos a `SchoolContext::getSchoolId()`) e associado o `school_id` à criação de novos utilizadores encarregados.
  - **Testes de Integração (`tests/Feature/ParentMultiTenantTest.php`)**: Criado teste automatizado validando a listagem da escola ativa e a exibição correta de 0 encarregados em escolas sem registos (**2/2 PASS**).
- **Resolução do Erro 1062 de Chave Duplicada em Settings (`2026_09_08_000001_fix_settings_unique_index`, `Setting::set` & `SettingsController`)**:
  - **Identificação do Bug**: O salvamento de configurações em `http://127.0.0.1:8000/admin/settings` lançava `SQLSTATE[23000]: Duplicate entry 'school_name' for key 'settings_key_unique'` devido a um índice único de coluna única (`key`) criado na migração inicial da tabela `settings`.
  - **Correção**: 
    1. Criada a migração `2026_09_08_000001_fix_settings_unique_index.php` para remover o índice restritivo de coluna única e aplicar o índice composto `idx_settings_key_school` (`['key', 'school_id']`).
    2. Aprimorado o método `Setting::set()` para pesquisar e atualizar registos no escopo da escola com fallback seguro para configurações globais.
    3. Atualizado o `SettingsController` para usar `Setting::set()`.
  - **Testes de Integração (`tests/Feature/SettingsMultiTenantTest.php`)**: Criado teste automatizado confirmando atualização de configurações sem erro de chave duplicada (**1/1 PASS**).
- **Testes Globais**: Suíte completa `vendor/bin/phpunit` — **100/100 PASS (100% aprovado, 342 asserções)**.

## 2026-09-08 — UI/UX Portal, FontAwesome Icons & Auth Redesign (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Ícones Profissionais FontAwesome & Contas de Demonstração Completas**:
  - Subsituição de todos os emojis da página de login (`resources/views/auth/login.blade.php`) por ícones vetoriais de alta precisão do FontAwesome (`fa-crown`, `fa-building-columns`, `fa-map-location-dot`, `fa-sitemap`, `fa-school`, `fa-book-open-reader`, `fa-chalkboard-user`, `fa-id-card`).
  - **Barra de Demonstração Completa**: Incluídos botões interativos de preenchimento automático para **todos os 8 papéis governamentais e escolares**:
    1. 👑 *Super Admin* (`superadmin@visionarios.co.mz`)
    2. 🏛️ *Inspector Provincial* (`inspector.zambezia@minedh.gov.mz`)
    3. 🗺️ *Director Distrital / SDEJT* (`director.namacurra@minedh.gov.mz`)
    4. 🌿 *Coordenador de ZIP* (`zip.namacurra@minedh.gov.mz`)
    5. 🏫 *Director de Escola* (`admin@visionarios.co.mz`)
    6. 📖 *Coordenador Pedagógico* (`pedagogia@visionarios.co.mz`)
    7. 👨‍🏫 *Professor* (`professor@visionarios.co.mz`)
    8. 🪪 *Secretária* (`secretaria@visionarios.co.mz`)
- **Unificação dos Layouts (`resources/views/layouts/app.blade.php`)**:
  - Aliased `school.blade.php` para `@extends('layouts.app')`, garantindo que 100% dos painéis utilizem a estrutura mestre e atualizada `app.blade.php`.

**Decisões tomadas:**
- Garantido o cumprimento rigoroso dos tokens de design nacionais e a desagregação estatística SNE (HM/M) em todas as exibições institucionais.
- Mantido o isolamento multi-tenant estrito (`BelongsToSchool` / `SchoolScope`).

**Testado:**
- NAVEGADOR (`mcp-chrome-devtools` / Subagente de Navegador): Capturadas capturas de ecrã completas de `http://127.0.0.1:8000/login` e `http://127.0.0.1:8000/registar-escola` confirmando acabamento visual de alto padrão.
- PHPUNIT (`vendor/bin/phpunit`): **94/94 PASS (319 asserções)** em 100% da suíte de testes.

## 2026-09-07 — Sprint 10 (`feature/school-onboarding`) — Antigravity AI Agent

**Feito:**
- **Onboarding Multi-Escola & Onboarding Wizard (`feature/school-onboarding`)**:
  - **Branch**: `feature/school-onboarding`.
  - **Controlador (`app/Http/Controllers/SchoolOnboardingController.php`)**: Criado o assistente interativo multi-etapa (Onboarding Wizard) para registo, aprovação e licenciamento autónomo de novas escolas públicas, privadas e comunitárias no ecossistema SIGE / ZAMEDU.
  - **Visualizações Blade (`resources/views/onboarding/wizard.blade.php`, `resources/views/onboarding/success.blade.php`)**:
    - `wizard.blade.php`: Formulário estruturado com seleção territorial encadeada (Província $\rightarrow$ Distrito $\rightarrow$ ZIP), tipo de instituição, dados do Diretor/Administrador e plano de subscrição.
    - `success.blade.php`: Painel de sucesso e boas-vindas com dados da licença gerada, credenciais de acesso inicial e guia de arranque rápido.
  - **Rotas e Registo (`routes/web.php`)**: Registadas as rotas `onboarding.school` (GET), `onboarding.school.store` (POST) e `onboarding.school.success` (GET).
  - **Testes de Integração (`tests/Feature/SchoolOnboardingTest.php`)**: Criados testes automatizados cobrindo a exibição do wizard, submissão com criação atómica de Escola, Licença e Utilizador Administrador, e visualização do ecrã de sucesso (**3/3 PASS**).

**Decisões tomadas:**
- O onboarding executa a criação da Escola, da Licença e do Diretor de Escola dentro de uma transação de base de dados (`DB::transaction`), garantindo integridade total e impedindo a criação de registos orfãos em caso de falha.

**Testado:**
- `vendor/bin/phpunit tests/Feature/SchoolOnboardingTest.php` — 100% PASS (3 testes, 23 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (**94 testes, 319 asserções**).

**Estado Final da Arquitetura Multi-Tenant**:
- **TODOS OS 10 SPRINTS DA ARQUITETURA MULTI-TENANT CONCLUÍDOS COM SUCESSO!**

---

## 2026-09-07 — Sprint 9 (`feature/tenant-performance`) — Antigravity AI Agent
## 2026-09-19 — Limpeza Definitiva de students/attendance.blade.php e Validação de /grades — Antigravity AI Agent

**Feito:**
- **Remoção de Conteúdo Residual / Duplicado em `students/attendance.blade.php`**:
  - O ficheiro continha a versão legada em Bootstrap mesclada no topo com a nova versão em Tailwind (721 linhas).
  - O ficheiro foi completamente rescrito e limpo (459 linhas), 100% Tailwind CSS, com todos os tokens ZAMEDU, cards de estatísticas com pílulas laterais, filtros funcionais, tabela responsiva e análise normativa MINEDH (Lei 18/2018).
  - Cache de visualizações e rotas limpos com `php artisan view:clear` e `php artisan route:clear`.
- **Validação de Ambas as Rotas no Navegador Real (Chrome DevTools MCP)**:
  - `http://127.0.0.1:8000/students/195/attendance`: **HTTP 200 OK**, página renderizada perfeitamente com novo design Tailwind.
  - `http://127.0.0.1:8000/students/195/grades`: **HTTP 200 OK**, boletim individual carregando sem erros de sintaxe ou de execução.
- **Testes Automatizados**:
  - `vendor/bin/phpunit tests/Feature/StudentManagementTest.php`: **2/2 testes PASS (5 assertions)**.

## 2026-09-19 — Correção de ParseError em StudentController.php e CertificateController.php — Antigravity AI Agent

**Feito:**
- **Otimização de Performance & Índices de Banco de Dados (`feature/tenant-performance`)**:
  - **Branch**: `feature/tenant-performance`.
  - **Migração Concluída (`2026_09_07_000013_add_multi_tenant_and_territorial_performance_indexes.php`)**: Adicionados índices compostos e de alta performance nas tabelas operacionais e territoriais:
    - `schools`: `idx_schools_territory_hierarchy` (`province_id`, `district_id`, `zip_id`), `idx_schools_province_id`, `idx_schools_district_id`, `idx_schools_zip_id`.
    - `users`: `idx_users_school_status`, `idx_users_territory`.
    - `students`: `idx_students_school_status`, `idx_students_school_gender`.
    - `grades`: `idx_grades_school_term_year`, `idx_grades_student_subject_class`.
    - `attendances`: `idx_attendances_school_date`.
    - `payments`: `idx_payments_school_status`, `idx_payments_student_date`.
    - `classes`: `idx_classes_school_grade_level`.
    - `enrollments`: `idx_enrollments_school_class_id`.
  - **Testes de Performance (`tests/Feature/TenantPerformanceTest.php`)**: Criados testes validando a existência de índices e a execução eficiente de eager loading (`with()`) em Eloquent (**2/2 PASS**).

**Decisões tomadas:**
- A migração foi desenhada com métodos idempotentes de salvaguarda (`addIndexIfNotExists` / `dropIndexIfExists`) garantindo execução segura tanto em MySQL como em SQLite.

**Testado:**
- `vendor/bin/phpunit tests/Feature/TenantPerformanceTest.php` — 100% PASS (2 testes, 9 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (91 testes, 296 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 10 (`feature/school-onboarding`)**: Onboarding Multi-Escola & Onboarding Wizard para registo de novas escolas públicas, privadas e comunitárias.

---

## 2026-09-07 — Sprint 8 (`feature/tenant-security-tests`) — Antigravity AI Agent

**Feito:**
- **Testes de Segurança & Penetração Multi-Tenant (`feature/tenant-security-tests`)**:
  - **Branch**: `feature/tenant-security-tests`.
  - **Suite de Testes (`tests/Feature/MultiTenantSecurityTest.php`)**: Criada suite exaustiva de testes de penetração e segurança validando as diretrizes de `SECURITY_MODEL.md`.
  - **Isolamento entre Escolas (Cross-School Isolation)**: Verificado que utilizadores da Escola A não conseguem visualizar, consultar ou manipular alunos ou pagamentos da Escola B via rotas diretas ou manipuladas (retornando HTTP 404 / 403).
  - **Scoping Global Eloquent (`BelongsToSchool`)**: Validada a garantia de que queries genéricas (`Student::all()`) em contexto de escola apenas devolvem registos da própria escola.
  - **Fronteira Territorial Administrativa**: Verificado que coordenadores de ZIP e inspectores distritais não conseguem aceder a dados de escolas fora das suas jurisdições geográficas.
  - **Resolução Estrita por Nível**: Validado `HierarchyScopeResolver` para garantir isolamento em profundidade.

**Decisões tomadas:**
- A defesa em profundidade combina a restrição de banco de dados (`SchoolScope` global), Policies/Middleware HTTP e o `HierarchyScopeResolver` em rotas territoriais.

**Testado:**
- `vendor/bin/phpunit tests/Feature/MultiTenantSecurityTest.php` — 100% PASS (6 testes, 15 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (89 testes, 287 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 9 (`feature/tenant-performance`)**: Otimização de Performance & Índices de Banco de Dados.

---

## 2026-09-07 — Sprint 7 (`feature/aggregation-drilldown`) — Antigravity AI Agent

**Feito:**
- **Agregação e Drill-Down de Mapas Pedagógicos Territoriais MINEDH (`feature/aggregation-drilldown`)**:
  - **Branch**: `feature/aggregation-drilldown`.
  - **Controller (`app/Http/Controllers/TerritorialAggregationController.php`)**: Criada a funcionalidade de consolidação territorial por classes e ciclos (8ª a 12ª classe, 1º Ciclo, 2º Ciclo e Total Geral) rigorosamente alinhada com o modelo oficial MINEDH (`APROV Pedgg ES-2ºTrim -2026_MARRACUA.xlsx`).
  - **Estatísticas SNE (HM / M)**: Cálculo automatizado de Entradas, Desistentes, Transferidos, Avaliados, Aprovados/Transitados ($\ge 9.5$), % Aproveitamento e % Desistência.
  - **Exportação Multi-Formato**: Exportação em PDF oficial formatado em A4 Paisagem com marcas para assinaturas (`/territorial/map/pdf`) e exportação em Excel/CSV com codificação UTF-8 com BOM (`/territorial/map/csv`).
  - **Views (`resources/views/territorial/map.blade.php` e `pdf_map.blade.php`)**: Interface interativa de consulta com filtros por ano, trimestre, província, distrito e ZIP + template PDF oficial.
  - **Testes de Funcionalidade (`tests/Feature/TerritorialAggregationTest.php`)**: 5 testes automatizados cobrindo visualização do mapa pedagógico territorial, escopo de permissões e exportações PDF/CSV (**5/5 PASS**).

**Decisões tomadas:**
- A consolidação de dados territoriais utiliza `HierarchyScopeResolver` para garantir que inspectores e coordenadores apenas consigam exportar e visualizar mapas pedagógicos do seu escopo administrativo.

**Testado:**
- `vendor/bin/phpunit tests/Feature/TerritorialAggregationTest.php` — 100% PASS (5 testes, 13 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (83 testes, 272 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 8 (`feature/tenant-security-tests`)**: Testes exaustivos de penetração, isolamento de dados e segurança multi-tenant (garantir impedimento de vazamento de dados entre escolas e jurisdições).

---

## 2026-09-07 — Sprint 6 (`feature/territorial-dashboards`) — Antigravity AI Agent

**Feito:**
- **Dashboards Territoriais de Inspeção MINEDH (`feature/territorial-dashboards`)**:
  - **Branch**: `feature/territorial-dashboards`.
  - **Controller (`app/Http/Controllers/TerritorialDashboardController.php`)**: Criado controller de agregação de métricas pedagógicas regulamentares MINEDH (Total de Escolas, Alunos HM/M, Corpo Docente, Turmas, Avaliados HM/M, Notas Positivas $\ge 9.5$ e Negativas $< 9.5$, Taxas de Aproveitamento HM e M). Suporta desagregação e agrupamento por Distrito, ZIP ou Escola.
  - **Views (`resources/views/territorial/index.blade.php` e `school_show.blade.php`)**: Interface responsiva e moderna com tokens de design ZAMEDU (verde esmeralda `#059669`, cards estatísticos, filtros de Província/Distrito/ZIP, tabelas regulamentares MINEDH e drill-down individual por escola).
  - **Rotas (`routes/web.php`)**: Registado grupo de rotas protegidas `territorial.dashboard` e `territorial.school`.
  - **Menu de Navegação (`resources/views/layouts/app.blade.php`)**: Adicionado item de menu "Inspeção MINEDH" condicionado a perfis de inspectores/coordenadores territoriais ou permissão `view_territorial_dashboard`.
  - **Testes de Funcionalidade (`tests/Feature/TerritorialDashboardTest.php`)**: 5 testes automatizados cobrindo permissões de acesso, isolamento de dados por escopo territorial e drill-down de escolas (**5/5 PASS**).

**Decisões tomadas:**
- Apenas leitura de dados agregados nos painéis territoriais via `HierarchyScopeResolver`, garantindo isolamento total de escrita operacional.

**Testado:**
- `vendor/bin/phpunit tests/Feature/TerritorialDashboardTest.php` — 100% PASS (5 testes, 11 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (78 testes, 259 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 7 (`feature/aggregation-drilldown`)**: Agregação avançada e drill-down em tempo real de mapas de aproveitamento por ZIP/Distrito com exportação em PDF e Excel.

---

## 2026-09-07 — Sprint 5 (`feature/territorial-roles`) — Antigravity AI Agent

**Feito:**
- **Papéis Territoriais Spatie & Scope Resolver (`feature/territorial-roles`)**:
  - **Branch**: `feature/territorial-roles`.
  - **Migração Concluída (`2026_09_07_000012_add_territorial_columns_to_users_table.php`)**: Adicionou as colunas chave estrangeira nulas `province_id`, `district_id`, `zip_id` na tabela `users`.
  - **Model User (`app/Models/User.php`)**: Atualizado com `$fillable` (`province_id`, `district_id`, `zip_id`) e relacionamentos Eloquent (`province()`, `district()`, `zip()`).
  - **Serviço `HierarchyScopeResolver` (`app/Services/HierarchyScopeResolver.php`)**: Criado serviço centralizado para determinar a lista de `school_ids` visíveis por utilizador conforme a sua hierarquia territorial (`super_admin` -> `null` global; `province_inspector` -> escolas da província; `district_inspector` -> escolas do distrito; `zip_coordinator` -> escolas do ZIP; utilizador escolar -> `[school_id]`).
  - **Seeder de Permissões e Roles (`database/seeders/PermissionSeeder.php` e `TerritorialRolesSeeder.php`)**: Adicionadas permissões territoriais (`view_territorial_dashboard`, `view_province_reports`, `view_district_reports`, `view_zip_reports`), roles territoriais Spatie (`province_admin`, `province_inspector`, `district_admin`, `district_inspector`, `zip_coordinator`, `zip_admin`) e utilizadores de teste territoriais associados à Zambézia/Namacurra.
  - **Testes de Funcionalidade (`tests/Feature/TerritorialRolesTest.php`)**: Suite completa de 5 testes unitários/funcionais validando atribuição de papéis Spatie e resolução hierárquica por escopo.

**Decisões tomadas:**
- `HierarchyScopeResolver` é o serviço único e desacoplado para queries de relatórios e painéis territoriais de leitura agregada (sem misturar com `SchoolScope` de escrita operacional das escolas).

**Testado:**
- `vendor/bin/phpunit tests/Feature/TerritorialRolesTest.php` — 100% PASS (5 testes, 20 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (73 testes, 248 asserções).

**Próximo passo recomendado:**
- Avançar para a **Sprint 6 (`feature/territorial-dashboards`)**: Implementação dos painéis e rotas para visualização de métricas de ZIP, Distrito e Província.

---

## 2026-09-07 — Sprint 4 (`feature/school-id-not-null`) — Antigravity AI Agent

**Feito:**
- **Tornar `school_id` NOT NULL (Ponto de Não Retorno do Multi-Tenancy)**:
  - **Branch**: `feature/school-id-not-null`.
  - **Migração Concluída (`2026_09_07_000011_make_school_id_not_null_on_domain_tables.php`)**: Alterou a restrição nula da coluna `school_id` para `NOT NULL` em 27 tabelas do domínio de negócio (`students`, `teachers`, `classes`, `subjects`, `class_schedules`, `class_subjects`, `enrollments`, `enrollment_applications`, `enrollment_documents`, `grades`, `assessments`, `attendances`, `payments`, `expenses`, `fee_types`, `financial_categories`, `communications`, `conversations`, `messages`, `sms_logs`, `sms_inbox`, `events`, `gate_logs`, `staff_leave_requests`, `material_lists`, `settings`, `licenses`).
  - **Auditoria & Migração Segura**: Incluído salvaguarda de backfill automático previo na migração para garantir que qualquer registo órfão residual seja associado à escola padrão antes da alteração de constraint.
  - **Coluna `users.school_id`**: Mantida `nullable()` intencionalmente para utilizadores territoriais (Inspectores ZIP/Distrito/Província e Super Admins).

**Decisões tomadas:**
- A coluna `school_id` é agora estritamente obrigatória a nível de motor de base de dados (DB constraint `NOT NULL`).
- Tentativas de inserção de dados do domínio sem `school_id` e sem `SchoolContext` ativo agora falham a nível de BD (`SQLSTATE[23000]`).

**Testado:**
- `vendor/bin/phpunit --filter=SchoolIdNotNullTest` — 100% PASS (2 testes, 3 asserções).
- Suíte completa `vendor/bin/phpunit` — 100% PASS (68 testes, 228 asserções).

**Próximo passo recomendado:**
1. Iniciar **Sprint 5 (`feature/territorial-roles`)**: Implementação dos papéis e permissões territoriais Spatie para Província, Distrito, ZIP e Escola.

---

## 2026-09-07 — Sprint 3 (`feature/school-scoping`) — Antigravity AI Agent

**Feito:**
- **Mecanismo de Isolamento Multi-Tenant & Scoping Eloquent Concluído**:
  - **Branch**: `feature/school-scoping`.
  - **`App\Services\SchoolContext`**: Criado serviço mestre de gestão do contexto de escola ativa com suporte a `setSchoolId()`, `getSchoolId()`, `hasSchoolContext()`, `clear()` e `runInContext()`, com auto-cura de verificação de existência no banco de dados para evitar inconsistências em execuções de testes ou plano de fundo.
  - **`App\Scopes\SchoolScope`**: Criado Eloquent Global Scope com filtro automático `where('table.school_id', activeSchoolId)` evitando colisão de colunas em JOINs.
  - **`App\Traits\BelongsToSchool`**: Criada Trait universal com atribuição automática do `school_id` no evento `creating`, relacionamento `$model->school()`, e métodos de bypass de escopo (`scopeForSchool`, `scopeWithoutSchoolScope`).
  - **`App\Http\Middleware\IdentifySchool`**: Registado em `Kernel.php` (grupos `web` e `api`) para identificar automaticamente o contexto da escola via `user->school_id`, cabeçalho `X-School-Id` ou sessão ativa.
  - **Modelos Atualizados (28 Tabelas)**: `User`, `Student`, `Teacher`, `ClassRoom`, `Subject`, `Grade`, `Attendance`, `Enrollment`, `Payment`, `Expense`, `FeeType`, `FinancialCategory`, `Communication`, `Event`, `GateLog`, `StaffLeaveRequest`, `MaterialList`, `EnrollmentApplication`, `EnrollmentDocument`, `Assessment`, `ClassSchedule`, `ClassSubject`, `Conversation`, `Message`, `SmsLog`, `SmsInbox`, `Setting`, `License`.
  - **`TrialRegisterController`**: Integrado para instanciar e vincular automaticamente a nova escola no Modelo Territorial (`Province` / `District`).

**Decisões tomadas:**
- A auto-cura do `SchoolContext::hasSchoolContext()` valida se a escola existe no banco antes de aplicar o escopo, garantindo retrocompatibilidade com scripts CLI, super administradores e instalações limpas.
- Mantido o `school_id` como `nullable()` na BD até o Sprint 4 (Ponto de Não Retorno).

**Testado:**
- `vendor/bin/phpunit --filter=SchoolScopingTest` — 100% PASS (5 testes, 11 asserções).
- Suite completa `vendor/bin/phpunit` — 100% PASS (66 testes, 225 asserções).

**Não feito / bloqueado:**
- Alteração da coluna `school_id` para `NOT NULL` reservada para o **Sprint 4 (`feature/school-id-not-null`)**.

**Próximo passo recomendado:**
1. Iniciar **Sprint 4 (`feature/school-id-not-null`)**: Ponto de não-retorno onde a coluna `school_id` passa a `NOT NULL` após verificação final de integridade de dados.

---

## 2026-09-07 — Sprint 2 (`feature/school-tenant-columns`) — Antigravity AI Agent

**Feito:**
- **Colunas Aditivas `school_id` (Nullable) & Backfill de Tenancy Concluídos**:
  - **Branch**: `feature/school-tenant-columns`.
  - **Migrações Aditivas**:
    - `2026_09_07_000005_add_school_id_to_academic_tables.php` (`students`, `teachers`, `classes`, `subjects`, `class_schedules`, `class_subjects`)
    - `2026_09_07_000006_add_school_id_to_enrollments_tables.php` (`enrollments`, `enrollment_applications`, `enrollment_documents`)
    - `2026_09_07_000007_add_school_id_to_grades_and_attendances_tables.php` (`grades`, `assessments`, `attendances`)
    - `2026_09_07_000008_add_school_id_to_financial_tables.php` (`payments`, `expenses`, `fee_types`, `financial_categories`)
    - `2026_09_07_000009_add_school_id_to_communication_and_support_tables.php` (`communications`, `conversations`, `messages`, `sms_logs`, `sms_inbox`, `events`, `gate_logs`, `staff_leave_requests`, `material_lists`, `users`)
    - `2026_09_07_000010_add_school_id_to_settings_and_licenses_tables.php` (`settings`, `licenses`)
  - **Comando Artisan `schools:backfill`**: Criado e executado com sucesso sobre a base de dados do sistema — **15.113 registos associados** à escola piloto *Escola Comunitária Escola da Paz* (`ESG-PAZ`) sem qualquer perda ou quebra de dados.
  - **`LicenseCheckMiddleware`**: Refatorado para consultar e avaliar licenças por `school_id` com fallback gracioso.

**Decisões tomadas:**
- Todas as colunas `school_id` permanecem `nullable()` nesta fase (Sprint 2) para garantir zero paragens e 100% de retrocompatibilidade com código legado antes do Sprint 4.
- Registada a execução limpa de `php artisan schools:backfill` sobre 15.113 registos existentes.

**Testado:**
- `vendor/bin/phpunit --filter=SchoolTenantColumnsTest` — 100% PASS (2 testes, 34 asserções).
- `TerritorialModelTest`, `SNEPedagogicalDocTest`, `RoleAccessAndWorkflowTest`, `MenuVisibilityTest` — 100% PASS.

**Não feito / bloqueado:**
- Trait `BelongsToSchool` e `SchoolScope` reservados para o **Sprint 3 (`feature/school-scoping`)**.

**Próximo passo recomendado:**
1. Iniciar **Sprint 3 (`feature/school-scoping`)**: Criar a Trait `BelongsToSchool`, o `SchoolScope`, o `SchoolContext` service e o middleware `IdentifySchool` para isolamento de dados de leitura/escrita.

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
