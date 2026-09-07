# ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar & Inteligência Educacional)

[![Versão](https://img.shields.io/badge/vers%C3%A3o-2026.2.0--Multi--Tenant-emerald.svg)](https://github.com/filipeive/sige-zamedu)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4.svg?logo=php)](https://php.net)
[![Laravel Framework](https://img.shields.io/badge/Laravel-10%20%7C%2012-FF2D20.svg?logo=laravel)](https://laravel.com)
[![Arquitetura](https://img.shields.io/badge/Multi--Tenant-Territorial%20(4--N%C3%ADveis)-blue.svg)]()
[![PHPUnit Tests](https://img.shields.io/badge/PHPUnit-94%2F94%20PASS%20(100%25)-brightgreen.svg)]()
[![Desenvolvedor](https://img.shields.io/badge/Desenvolvido%20por-Filipe%20dos%20Santos-007acc.svg)](http://146.235.224.99/)
[![Licença](https://img.shields.io/badge/Licen%C3%A7a-SaaS%20%2F%20Propriet%C3%A1rio-emerald.svg)]()

**ZAMEDU - SIGE** é uma plataforma comercial moderna, robusta e altamente escalável de **Gestão Escolar Integrada, Inteligência Educacional e Multi-Tenancy Territorial (SaaS & ERP)** desenvolvida por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)). 

A plataforma atende rigorosamente às normas oficiais do **Sistema Nacional de Educação (SNE - Lei 18/2018 de Moçambique)** e aos diplomas do **Ministério da Educação e Desenvolvimento Humano (MINEDH)**, oferecendo automação ponta a ponta para **Escolas Públicas**, **Privadas** e **Comunitárias**, integrando a gestão escolar local com o acompanhamento territorial em tempo real por **Zonas de Influência Pedagógica (ZIP)**, **Serviços Distritais (SDEJT)** e **Direcções Provinciais (DPEDH)**.

---

## 🏛️ Arquitetura Multi-Tenant Territorial (4 Níveis Hierárquicos)

O SIGE implementa um modelo de **Multi-Tenancy Hierárquico Single-Database**:

$$\text{Província} \longrightarrow \text{Distrito} \longrightarrow \text{ZIP (Zona de Influência Pedagógica)} \longrightarrow \text{Escola}$$

- **Escopo de Escrita (CRUD Isolado)**: Cada escola tem os seus dados isolados automaticamente via `SchoolScope` e trait `BelongsToSchool`.
- **Escopo de Leitura Agregado (Read-Only)**: Inspectores Provinciais, Técnicos Distritais e Coordenadores de ZIP visualizam dados agregados da sua jurisdição geográfica via `HierarchyScopeResolver`.
- **Onboarding Autónomo (`/onboarding/school`)**: Assistente interativo (Wizard) para registo, aprovação e licenciamento autónomo de novas unidades escolares.
- **Segurança Penetration-Tested**: Suíte exaustiva de testes de penetração cross-school (`MultiTenantSecurityTest`) garantindo isolamento total de dados.
- **Performance Otimizada**: Migração de índices compostos (`2026_09_07_000013`) para consultas ultra-rápidas em grande escala.

---

## 🌟 Módulos & Funcionalidades Principais

### 🚨 1. Portaria Digital & Controlo de Acesso (`/gatekeeper`)
- **Leitura em Tempo Real:** Suporte a leitores de código de barras USB e scanners de QR Code via câmara.
- **Painel Ao Vivo:** Monitorização instantânea de entradas, saídas e contagem de alunos presentes no recinto escolar.
- **Cartão de Estudante com Emblema:** Emissão de cartões de estudante personalizáveis com fotos, QR Code/Barcode e o **Emblema Oficial da República de Moçambique**.

### 🇲🇿 2. Motor Académico SNE & Conformidade MINEDH (Lei 18/2018)
- **Fórmulas Oficiais:** Fonte única de verdade para $MACS$, $MT$, $MAF$, $NE$ e $NFD$.
- **Classes de Exame Nacional:** Tratamento diferenciado para 9ª, 10ª e 12ª classes com fórmula de exame:
  $$NE_{\text{eficaz}} = \max(NE, ACF) \quad \text{e} \quad NFD = \text{round}\left(\frac{MAF + NE_{\text{eficaz}}}{2}\right)$$
- **Pautas & Relatórios Oficiais:** Pautas trimestrais e anuais com estatísticas de aproveitamento desagregadas por género (**HM** e **M**).
- **Emblema da República & Assinaturas Inteligentes:** Integração nativa em Certidões, Certificados, Boletins e Pautas.

### 📊 3. Dashboards Territoriais & Inspeção Pedagógica (`/territorial/dashboard` & `/territorial/map`)
- **Visão em Cascata:** Drill-down interativo desde o nível Provincial até à visualização detalhada da Escola.
- **Mapa Pedagógico MINEDH:** Indicadores de desempenho e aproveitamento por ciclo (1º Ciclo ESG: 8ª-9ª, 2º Ciclo ESG: 10ª-12ª classes).
- **Exportação Institucional:** Download de relatórios agregados territoriais em PDF oficial e CSV/Excel.

### 🚀 4. Onboarding Multi-Escola & Licenciamento (`/onboarding/school`)
- **Wizard Interativo em 4 Etapas:** Seleção territorial encadeada, identificação da escola, criação do utilizador Diretor e seleção de plano.
- **Transação Atómica:** Emissão instantânea de licença com credenciais de arranque rápido.

### 📱 5. Central de SMS & Comunicações (`/communications` & `/sms`)
- **Envio Massivo & Individual:** Comunicação direta com encarregados de educação e corpo docente.
- **Tags Dinâmicas:** Personalização automática com `{student_name}`, `{fee_amount}`, `{due_date}`.

### 🤖 6. ZAMEDU AI Assistant
- **Assistente Flutuante:** Suporte contextual inteligente para professores e gestores acessível em toda a aplicação.

### 💳 7. Gestão Financeira & Recibos (`/payments` & `/expenses`)
- **Suporte Híbrido:** Parâmetros para Escolas Públicas (isenção de propinas) e Escolas Privadas (mensalidades, multas, recibos em PDF).
- **Resultado Líquido Executivo:** Cálculo automático de Receita vs. Despesas.

### 👥 8. Portais Dedicados por Perfil
- **Inspector Provincial / Técnico Distrital / Coordenador de ZIP**: Painéis de monitoria territorial e drill-down.
- **Diretor de Escola**: Painel executivo da unidade escolar, gestão de turmas e relatórios.
- **Professor (`/teacher/dashboard`)**: Chamadas diárias, lançamento de notas e pautas de turma.
- **Secretaria / Administrativo**: Inscrições, matrículas, emissão de documentos e propinas.
- **Encarregado de Educação (`/parent/dashboard`)**: Boletim de notas, assiduidade e situação financeira do educando.
- **Aluno**: Consulta de horário, notas e boletim escolar.

---

## 🛠️ Stack Tecnológico

- **Backend Framework:** Laravel 10 / 12 (PHP 8.2 / 8.3)
- **Base de Dados:** MySQL 8.0 / MariaDB (SQLite em memória para testes PHPUnit)
- **RBAC & Permissões:** Spatie `laravel-permission` com 9 perfis institucionais territoriais
- **Frontend & UI:** Blade, TailwindCSS (tokens esmeralda ZAMEDU), Alpine.js, Chart.js 4.4, SweetAlert2
- **PDF Engine:** DomPDF com otimização de imagens locais (`public_path`)
- **Testes Automatizados:** PHPUnit 11 com 94 testes automatizados (100% PASS)

---

## 📑 Perfis de Utilizador & Acesso (RBAC)

1. `super_admin`: Acesso total à infraestrutura global SaaS.
2. `provincial_inspector`: Inspeção e leitura agregada da Província.
3. `district_officer`: Monitoria dos Serviços Distritais (SDEJT).
4. `zip_coordinator`: Acompanhamento da Zona de Influência Pedagógica.
5. `school_director` / `admin`: Gestão integral da Escola.
6. `pedagogy`: Gestão pedagógica, pautas e mapas escolares.
7. `secretary`: Inscrições, matrículas, emissão de documentos e propinas.
8. `teacher`: Sumários, chamadas, avaliações ACS/ACP/ACF e lançamentos.
9. `security`: Operação do Controlo de Acesso na Portaria Digital.
10. `parent` / `student`: Acesso ao Portal do Encarregado e Aluno.

---

## 📚 Estrutura da Documentação (`docs/` & `agents/`)

- 📘 [ONBOARDING.md](ONBOARDING.md) — Guia de instalação e arranque de novas instâncias.
- 🎨 [docs/DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md) — Paleta de cores esmeralda, tokens e componentes.
- 🏗️ [docs/architecture/TERRITORIAL_HIERARCHY.md](docs/architecture/TERRITORIAL_HIERARCHY.md) — Especificação do modelo territorial.
- 🔒 [docs/architecture/DATA_SCOPING.md](docs/architecture/DATA_SCOPING.md) — Regras de isolamento de escrita vs. leitura agregada.
- 🛡️ [docs/architecture/SECURITY_MODEL.md](docs/architecture/SECURITY_MODEL.md) — Modelo de segurança e testes de isolamento.
- 📊 [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) — Esquema relacional da base de dados multi-tenant.
- 📋 [docs/MODULES_AND_FEATURES.md](docs/MODULES_AND_FEATURES.md) — Especificação detalhada dos módulos funcionais.
- 📱 [docs/PWA_AND_ACCESSIBILITY.md](docs/PWA_AND_ACCESSIBILITY.md) — PWA Standalone e conformidade WCAG.
- 📝 [agents/HANDOFF.md](agents/HANDOFF.md) — Diário de bordo detalhado de todos os 10 Sprints.

---

## ⚡ Instalação & Execução Local

```bash
# 1. Clonar o repositório oficial
git clone git@github.com:filipeive/sige-zamedu.git
cd sige-zamedu

# 2. Instalar dependências PHP e Node.js
composer install
npm install && npm run build

# 3. Configurar ambiente e chave de aplicação
cp .env.example .env
php artisan key:generate

# 4. Executar migrações e seeders com hierarquia territorial
php artisan migrate --seed

# 5. Executar os testes automatizados
vendor/bin/phpunit

# 6. Iniciar o servidor local
php artisan serve
```

---

## 👨‍💻 Autor & Propriedade

- **Autor:** Filipe dos Santos
- **Website / Portfolio:** [http://146.235.224.99/](http://146.235.224.99/)
- **Repositório GitHub:** [git@github.com:filipeive/sige-zamedu.git](https://github.com/filipeive/sige-zamedu)

Direitos reservados © 2026 **ZAMEDU - SIGE** por **Filipe dos Santos**.
