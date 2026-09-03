# Especificação Funcional dos Módulos — ZAMEDU - SIGE

Este documento descreve exaustivamente todos os módulos funcionais da plataforma **ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar)**, desenvolvida por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)).

---

## 📋 Índice dos Módulos

1. [Dashboard Executivo & Analytics](#1-dashboard-executivo--analytics)
2. [Portaria Digital & Controlo de Acesso (`/gatekeeper`)](#2-portaria-digital--controlo-de-acesso-gatekeeper)
3. [Documentos Oficiais, Certidões & Emblema Nacional](#3-documentos-oficiais-certidões--emblema-nacional)
4. [Central de Comunicações & Envio de SMS (`/communications` & `/sms`)](#4-central-de-comunicações--envio-de-sms-communications--sms)
5. [Assistente Virtual com IA (`ZAMEDU AI Assistant`)](#5-assistente-virtual-com-ia-zamedu-ai-assistant)
6. [Gestão de Alunos](#6-gestão-de-alunos)
7. [Gestão de Professores & Pedidos de Licença](#7-gestão-de-professores--pedidos-de-licença)
8. [Turmas, Turnos & Horários Escolares](#8-turmas-turnos--horários-escolares)
9. [Disciplinas & Matriz Curricular](#9-disciplinas--matriz-curricular)
10. [Matrículas, Inscrições & Pré-Inscrições Sem Filas](#10-matrículas-inscrições--pré-inscrições-sem-filas)
11. [Propinas, Pagamentos & Modalidades (Público vs Privado)](#11-propinas-pagamentos--modalidades-público-vs-privado)
12. [Gestão de Despesas & Resultado Líquido](#12-gestão-de-despesas--resultado-líquido)
13. [Lançamento de Notas, Pautas MINEDH & Boletins](#13-lançamento-de-notas-pautas-minedh--boletins)
14. [Assiduidade & Chamada Digital](#14-assiduidade--chamada-digital)
15. [Portais Dedicados (Encarregados & Professores)](#15-portais-dedicados-encarregados--professores)
16. [Configurações da Instituição, Licenciamento SaaS & Audit Logs](#16-configurações-da-instituição-licenciamento-saas--audit-logs)

---

## 1. Dashboard Executivo & Analytics

- **Rota**: `/dashboard` (`DashboardController@index`)
- **Público-alvo**: Administradores, Diretores, Secretaria, Pedagogia.
- **Funcionalidades**:
  - Cartões de Indicadores Chave (KPIs): Receita Mensal, Alunos Ativos, Taxa Global de Assiduidade, Média Geral Académica, Propinas em Atraso, Total de Professores e Despesas do Mês.
  - Widget de Calendário com navegação mensal, pontos de eventos e lista de eventos futuros.
  - Gráfico Interativo de Evolução da Receita (Chart.js UMD) nos últimos 12 meses.
  - Gráfico de Distribuição de Alunos por Turma.
  - Quadro de Honra com o Top 5 Alunos com maior média académica global.
  - Ranking de Turmas com melhor desempenho pedagógico.
  - Smart Insights (alertas automáticos de assiduidade e cobranças).

---

## 2. Portaria Digital & Controlo de Acesso (`/gatekeeper`)

- **Rota**: `/gatekeeper`, `/gatekeeper/{student}/card` (`GatekeeperController`)
- **Público-alvo**: Operadores de Portaria, Segurança, Administração.
- **Funcionalidades**:
  - Scanner de Código de Barras / QR Code integrado para leitores físicos USB ou câmaras web/mobile.
  - Monitorização de entradas e saídas de estudantes e visitantes em tempo real.
  - Indicadores ao vivo: Alunos Presentes no Recinto vs. Alunos Fora do Recinto.
  - Emissão e impressão de Cartões de Estudante com foto tipo passe, Barcode/QR Code e **Emblema Oficial da República de Moçambique**.
  - Modal de Trilha de Acessos (`GateLog`) com histórico detalhado por data, hora e responsável.

---

## 3. Documentos Oficiais, Certidões & Emblema Nacional

- **Rota**: `/certificates`, `/enrollments/{id}/print`, `/pautas/pdf`
- **Público-alvo**: Secretaria Escolar, Direção Pedagógica.
- **Funcionalidades**:
  - Emissão de **Certidões de Matrícula**, **Certificados de Habilitações Literárias**, **Boletins de Notas** e **Pautas de Avaliação**.
  - Integração nativa do **Emblema Oficial da República de Moçambique** no cabeçalho de todos os documentos no formato PDF.
  - Tratamento inteligente de assinaturas oficiais para eliminar duplicações quando os cargos coincidem com os nomes parametrizados (*O Director da Escola*, *O Chefe da Secretaria*).

---

## 4. Central de Comunicações & Envio de SMS (`/communications` & `/sms`)

- **Rotas**: `/admin/communications`, `/sms`, `/sms/inbox` (`NotificationController`, `SmsController`, `SmsLogController`, `SmsInboxController`)
- **Público-alvo**: Secretaria, Administradores.
- **Funcionalidades**:
  - Criação de comunicados gerais e avisos diretos por turma, encarregado ou aluno.
  - Modal de envio de SMS rápido com suporte a tags (`{student_name}`, `{fee_amount}`, `{due_date}`).
  - Disparo de lembretes automáticos para pagamentos de propinas em atraso, reuniões de pais e eventos.
  - **Motor SMS multi-driver** (`SmsManager`):
    - `HttpSmsDriver` — integração real via [httpSMS](https://httpsms.com) (Android app que envia SMS pelo SIM real do telemóvel).
    - `LogSmsDriver` — driver de simulação (modo `SMS_SIMULATE=true`) que regista os envios sem chamar API externa.
    - `SmsChannel` — canal de notificação que permite enviar SMS via `Notifiable::route('sms', '+258...')`.
    - `SmsNotificationService` — service único que grava sempre na tabela `sms_logs` (auditoria).
  - **Tabela `sms_logs`**: estado (`sent`, `delivered`, `failed`, `disabled`, `pending`), `provider_id` (UUID httpSMS), `delivered_at`, `failed_at`, `provider_response` (JSON), `cost`, `context`, `channel` (guardian/event/payment/bulk/system).
  - **Inbox de mensagens recebidas** (`/sms/inbox`):
    - Webhook CloudEvents 1.0 (`POST /webhooks/httpsms`) recebe `message.phone.received` e regista em `sms_inbox`.
    - **Auto-match por telefone (E.164)**: identifica automaticamente o aluno/encarregado que enviou a mensagem.
    - **Resposta inline** — o staff pode responder directamente por SMS da página da mensagem.
    - **Notificações in-app** com preview e link directo para a mensagem.
    - **Badge no sidebar** com contador de mensagens não lidas.
    - Filtros por estado (`todas`, `não lidas`, `lidas`) e pesquisa por telefone/nome/conteúdo.
  - **Webhooks de entrega**: `message.phone.delivered` actualiza log para `delivered`; `message.send.failed`/`expired` actualiza para `failed` com motivo.

---

## 5. Assistente Virtual com IA (`ZAMEDU AI Assistant`)

- **Rota**: `/ai` (`AiController`)
- **Público-alvo**: Administradores, Diretores, Professores.
- **Funcionalidades**:
  - **Widget flutuante** de chat com inteligência artificial acessível em qualquer página do sistema (Alpine.js, persistência em `localStorage`).
  - **Página dedicada** `/ai` com sidebar de histórico de conversas.
  - **Motor AI multi-driver** (`AiManager`):
    - `OpenRouterDriver` — provider padrão, configurado com `google/gemini-2.5-flash` (modelo rápido e económico).
    - `GeminiDriver` — provider alternativo via API directa do Google Gemini.
    - Fácil extensão: basta implementar `AiDriverInterface` e registar no `AiManager`.
  - Auxílio na elaboração de comunicados, planos de aula, consulta de dados do sistema e estatísticas escolares.
  - Histórico de conversas gravado nas tabelas `ai_conversations` e `messages`.

---

## 6. Gestão de Alunos

- **Rota**: `/students` (`StudentController`)
- **Funcionalidades**:
  - Listagem paginada de estudantes com avatares circulares e filtros por nome, número, turma, status e género.
  - Registo completo de alunos com foto tipo passe, data de nascimento, endereço, contactos de emergência e necessidades educativas especiais.
  - Ficha individual do aluno (`/students/{student}`): histórico de matrículas, pagamentos associados, notas por trimestre e relatórios de assiduidade.
  - Operações CRUD completas com permissões granularizadas (`view_students`, `create_students`, `edit_students`, `delete_students`).

---

## 7. Gestão de Professores & Pedidos de Licença

- **Rota**: `/teachers`, `/staff-leave-requests` (`TeacherController`, `StaffLeaveRequestController`)
- **Funcionalidades**:
  - Cadastro de docentes com número de BI, qualificação académica, especialização, data de admissão e salário.
  - Cálculo automático dos anos de experiência docente.
  - Atribuição de turmas e disciplinas a cada professor.
  - Gestão de solicitações de licença/ausência do pessoal docente e administrativo com aprovação/rejeição.

---

## 8. Turmas, Turnos & Horários Escolares

- **Rota**: `/classes` (`ClassRoomController`)
- **Funcionalidades**:
  - Criação e gestão de turmas por ano lectivo e nível de ensino (Pré-Infantil a 12ª Classe).
  - **Organização por Nível de Ensino** (4 tabs server-side): Todas / Pré-Escolar / Primário / Secundário.
  - **Modos de Ensino** (campo `teaching_mode`):
    - `single_teacher` (Pré-Escolar & Primário): 1 **Professor Regente** titular + **Professor Assistente** opcional (`assistant_teacher_id`).
    - `subject_based` (Secundário): professores atribuídos por **disciplina** via tabela `class_subjects` (cada disciplina tem o seu próprio professor).
  - Painel contextual em `/classes/{class}`:
    - Primário: form inline para adicionar/actualizar professor assistente.
    - Secundário: matriz professor↔disciplina actualizável em massa.
  - Associação de Director de Turma.
  - Definição de turno escolar (`morning`, `afternoon`, `night`) com horários de atraso configuráveis por turno.
  - Definição de horários de aula por dia da semana (`ClassSchedule`).
  - Alocação e transferência de alunos entre turmas.
  - Rotas novas: `POST /classes/{class}/assign-subjects` (secundário), `POST /classes/{class}/update-assistant` (primário).

---

## 9. Disciplinas & Matriz Curricular

- **Rota**: `/subjects` (`SubjectController`)
- **Funcionalidades**:
  - Cadastro de disciplinas com carga horária semanal.
  - Vinculação de disciplinas às respetivas turmas e professores ministrantes (`ClassSubject`).

---

## 10. Matrículas, Inscrições & Pré-Inscrições Sem Filas

- **Rota**: `/enrollments`, `/public/pre-enrollment` (`EnrollmentController`, `TrialRegisterController`)
- **Funcionalidades**:
  - Portal de pré-inscrição pública online para novos alunos sem deslocação à escola.
  - Confirmação de matrículas de novos alunos e renovação anual automatizada (`/admin/enrollments/renewals`).
  - Impressão de comprovativos de matrícula em PDF com o Emblema da República.

---

## 11. Propinas, Pagamentos & Modalidades (Público vs Privado)

- **Rota**: `/payments` (`PaymentController`)
- **Funcionalidades**:
  - Distinção entre **Escolas Privadas** (cobrança de propinas, mensalidades, multas por atraso) e **Escolas Públicas** (isenção de mensalidades, cobrança pontual de matrículas/exames).
  - Geração automática de propinas mensais por lote.
  - Cálculo de multas e juros por mora configuráveis.
  - Geração de referências bancárias de pagamento (M-Pesa, eMola, Multicaixa).
  - Emissão de recibos profissionais em PDF com Emblema e dados do pagador.

---

## 12. Gestão de Despesas & Resultado Líquido

- **Rota**: `/expenses` (`ExpenseController`)
- **Funcionalidades**:
  - Registo, edição e categorização de despesas financeiras da escola (`FinancialCategory`).
  - Integração com o módulo financeiro para exibição do **Resultado Líquido** (Total de Receitas - Total de Despesas).

---

## 13. Lançamento de Notas, Pautas MINEDH & Boletins

- **Rota**: `/grades/dashboard` (entrada única), `/grades`, `/pautas` (`GradeController`)
- **Funcionalidades**:
  - **Dashboard central de Gestão de Notas** (`/grades/dashboard`): cards KPI (total de notas, média geral, taxa de aprovação, turmas activas), tabs por nível (Todas / Pré-Escolar / Primário / Secundário), grid de acções rápidas por turma (Lançar Notas / Pauta PDF / Notas da Turma / Ver) e atalhos para Pautas & Boletins, Relatório Académico e Gestão de Turmas.
  - Lançamento contínuo de notas: ACS (Avaliação Contínua e Sistemática), ACP (Avaliação Parcial) e ACF (Avaliação Final).
  - Entrada de notas por lote para toda a turma (`/grades/batch-create`).
  - Emissão de Pauta Oficial da Turma e Boletim Informativo do Aluno.
  - Cálculo automático de médias segundo as regras do MINEDH.
  - Toda a navegação do menu lateral e os breadcrumbs das sub-páginas voltam sempre ao dashboard, eliminando links "órfãos".

---

## 14. Assiduidade & Chamada Digital

- **Rota**: `/attendances` (`AttendanceController`)
- **Funcionalidades**:
  - Registo diário de presenças, faltas justificadas e faltas injustificadas por turma.
  - Relatórios consolidados de assiduidade.

---

## 15. Portais Dedicados (Encarregados & Professores)

- **Portal do Encarregado (`/parent/dashboard`)**: Acompanhamento do boletim de notas, assiduidade, propinas e avisos escolares.
- **Portal do Professor (`/teacher/dashboard`)**: Registo de chamadas, lançamento de notas e gestão de licenças.

---

## 16. Configurações da Instituição, Licenciamento SaaS & Audit Logs

- **Configurações Globais (`/admin/settings`)**: Definição do nome da instituição, logótipo, cores primárias/secundárias, tipo de escola (pública/privada) e parâmetros académicos.
- **Painel de Licenças (`/admin/license`)**: Validação da vigência SaaS (`active`, `grace_period`, `suspended`).
- **Registo de Auditoria (`/admin/audit`)**: Rastreamento completo das ações de utilizadores no sistema.
