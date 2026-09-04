# Changelog — ZAMEDU - SIGE

Todas as alterações significativas à plataforma **ZAMEDU - SIGE** são registadas neste ficheiro.

O formato segue [Keep a Changelog](https://keepachangelog.com/pt-PT/1.0.0/) e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2026.1.0] — 2026-09-04

### Adicionado
- **ZAMEDU - SIGE Branding & Filipe dos Santos Credits:** Atualização da marca oficial e créditos do desenvolvedor Filipe dos Santos ([http://146.235.224.99/](http://146.235.224.99/)).
- **Emblema da República de Moçambique:** Inclusão nativa do Emblema Oficial em todos os impressos e PDFs (Certidões, Certificados, Boletins, Cartões de Aluno e Pautas).
- **Assinaturas Institucionais Sem Duplicação:** Sanitização de assinaturas oficiais (*Director da Escola*, *Chefe da Secretaria*) via `SchoolHelper`.
- **Portaria Digital Refatorada (`/gatekeeper`):** Interface moderna com scanner QR/Barcode ao vivo, contagem em tempo real de presença na escola e emissão de cartões com Emblema da República.
- **Central de Comunicações & Gateway SMS (`/communications` & `/sms`):** Envio massivo e individual de SMS, modal de disparo rápido com tags dinâmicas e tabela `sms_logs`.
- **Motor SMS Multi-Driver (`SmsManager` + `HttpSmsDriver` + `LogSmsDriver`):** Factory pattern com fallback automático, normalização E.164, canal de notificação `SmsChannel` e service `SmsNotificationService` que regista todos os envios na tabela `sms_logs` (status: `sent`, `delivered`, `failed`, `disabled`).
- **Integração httpSMS em Produção:** Driver real com header `x-api-key` (não `Authorization: Bearer`), `HTTPSMS_FROM=+258862134230` (Moçambique), endpoint de envio via Android app httpSMS.
- **Recebimento de SMS via Webhook (CloudEvents 1.0):** Endpoint `POST /webhooks/httpsms` (excluído de CSRF) processa `message.phone.received`, `message.phone.delivered`, `message.phone.sent`, `message.send.failed`, `message.send.expired`, `phone.heartbeat.online/offline`.
- **Inbox de SMS Recebidas (`/sms/inbox`):** Tabela `sms_inbox` com auto-match por telefone (E.164) com aluno/encarregado, marcação de lidas, resposta inline por SMS, contador de não-lidas no sidebar, página de detalhe com correspondência automática e filtros (pesquisa + estado).
- **Notificações In-App de Respostas:** Quando um encarregado responde por SMS, todos os utilizadores com permissão `view_students` recebem notificação in-app (`GenericNotification`) com preview do conteúdo e link directo para a mensagem.
- **Assistente IA Integrado (`ZAMEDU AI Assistant`):** Widget de inteligência artificial flutuante para auxílio a administradores e docentes com suporte a `ai_conversations` e `messages`, drivers OpenRouter (padrão, `google/gemini-2.5-flash`) e Gemini directo.
- **Suporte Híbrido Escolas Públicas vs Privadas:** Distinção visual e de regras financeiras entre ensino público (isenções) e privado (gestão de mensalidades/multas).
- **Gestão de Turmas por Nível de Ensino (refactor profundo):** Página `/classes` reorganizada com 4 tabs server-side (Todas / Pré-Escolar / Primário / Secundário), coluna `teaching_mode` (`single_teacher` para Pré/Primário, `subject_based` para Secundário), coluna `assistant_teacher_id` (professor assistente opcional no primário), novo painel contextual na página da turma com atribuição de professor assistente (primário) ou matriz professor↔disciplina (secundário). Rotas novas: `POST classes/{class}/assign-subjects`, `POST classes/{class}/update-assistant`. Seeder `ClassRoomSeeder` e `DemoDataSeeder` actualizados para respeitar o modo correcto por nível.
- **Dashboard de Gestão de Notas (`/grades/dashboard`):** Ponto de entrada único para toda a gestão de notas e pautas, com tabs por nível de ensino, cards KPI (total de notas, média geral, taxa de aprovação, turmas activas), grid de acções rápidas por turma (Lançar / Pauta / Notas / Ver) e atalhos para Pautas & Boletins, Relatório Académico e Gestão de Turmas. Os links de "Lançamento de Notas & Pautas" do menu lateral e os breadcrumbs das sub-páginas agora voltam sempre a este dashboard.
- **Gradebook do Professor corrigido (`/teacher/gradebook/{class}`):** O caderno de notas agora carrega correctamente as notas segmentadas por disciplina. Foram corrigidos 4 bugs: (1) `Teacher::classes()` só considerava turmas onde o professor era regente — agora `canAccessClass()` também reconhece atribuições por disciplina em `class_subjects`; (2) a dropdown mostrava TODAS as disciplinas da turma em vez de só as que o professor lecciona — agora usa `taughtSubjectsInClass()`; (3) os tipos de avaliação usavam `test/homework/project/participation` mas a BD usa o padrão MINEDH (`ACS1, ACS2, ACS3, ACP, ACF`) — view e validação actualizadas; (4) a Média Trimestral (MT) agora é calculada correctamente pela fórmula MINEDH (40% MACS + 20% ACP + 40% ACF) com badge de Aprovado/Recuperação/Reprovado.
- **Endpoint de lançamento de notas (`/teacher/grades/store`):** Agora aceita todos os tipos MINEDH (`ACS1, ACS2, ACS3, ACP, ACF, MAC, PPT, CPT, CAP, Exame`) + valida que o professor lecciona a (turma, disciplina) antes de gravar. `batchUpdateGrades` ganhou a mesma protecção com contadores de "actualizadas" e "ignoradas (sem permissão)".
- **Caderneta de Notas do Encarregado (`/parent/student/{student}/grades`):** Agora filtra exclusivamente notas com `grade` não-nulo (i.e. realmente lançadas), ignora placeholders. Mostra "Sem notas" em disciplinas sem avaliações, "Aguardando" para estados sem média calculada, e detalhes por trimestre.
- **Filtro de segurança nas Pautas (Trimestral/Anual/Final/PDF/Excel):** O `PautaController` agora aplica dois níveis de controlo para professores: (1) só podem visualizar pautas de turmas onde leccionam (regente OU disciplina atribuída); (2) quando há filtro `?subject_id=`, valida que o professor lecciona essa disciplina na turma. Novo método `resolveSubjectsForTeacher()` devolve apenas as disciplinas que o professor lecciona — o resto da pauta fica oculto. Admin/secretaria continuam a ver todas as disciplinas.

### Corrigido
- Modal de detalhes em "Comunicações" que apresentava falhas de abertura.
- Duplicação do título do cargo nas linhas de assinatura em relatórios e impressos em PDF.
- Ajuste de caminhos estáticos para imagens no DomPDF (`public_path('images/emblema-mocambique.png')`).

---

## [0.2.0] — 2026-08-05

### Adicionado
- Módulo de gestão de despesas com categorias financeiras (`FinancialCategory`, `Expense`).
- Suporte a turnos escolares (`morning`, `afternoon`, `night`) nas turmas.
- Configuração parametrizável de cálculo de médias (pesos MACS/ACP, mínimo de trimestres).
- Widget de calendário integrado nos dashboards.
- Role `security` para a Portaria com permissão `view_gatekeeper_logs`.
- Permissões `manage_expenses` e `view_audit_logs`.

---

## [0.1.0] — 2026-01-04

### Adicionado
- Lançamento inicial da plataforma.
- Gestão de alunos, matrículas, turmas, disciplinas.
- Sistema de avaliações com ACS1/ACS2/ACS3, ACP, ACF.
- Gestão de presenças com Portaria.
- Gestão financeira com propinas e cobranças.
- Portais para professores e encarregados de educação.
- Sistema de licenciamento SaaS.
