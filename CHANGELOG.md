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
- **Assistente IA Integrado (`ZAMEDU AI Assistant`):** Widget de inteligência artificial flutuante para auxílio a administradores e docentes com suporte a `ai_conversations` e `messages`.
- **Suporte Híbrido Escolas Públicas vs Privadas:** Distinção visual e de regras financeiras entre ensino público (isenções) e privado (gestão de mensalidades/multas).

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
