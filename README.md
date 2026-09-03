# ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar)

[![Versão](https://img.shields.io/badge/vers%C3%A3o-2026.1.0-blue.svg)](https://github.com/filipeive/sige-zamedu)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4.svg?logo=php)](https://php.net)
[![Laravel Framework](https://img.shields.io/badge/Laravel-10%20%7C%2012-FF2D20.svg?logo=laravel)](https://laravel.com)
[![Desenvolvedor](https://img.shields.io/badge/Desenvolvido%20por-Filipe%20dos%20Santos-007acc.svg)](http://146.235.224.99/)
[![Licença](https://img.shields.io/badge/Licen%C3%A7a-SaaS%20%2F%20Propriet%C3%A1rio-emerald.svg)]()

**ZAMEDU - SIGE** é uma plataforma comercial moderna, robusta e escalável de **Gestão Escolar Integrada (ERP & SaaS)** desenvolvida por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)). 

A plataforma foi concebida para atender às normas oficiais do **Ministério da Educação e Desenvolvimento Humano (MINEDH) de Moçambique**, suportando tanto **Instituições de Ensino Privadas** como **Escolas Públicas**, oferecendo automação ponta a ponta desde a portaria até aos relatórios executivos.

---

## 🌟 Destaques & Principais Módulos

### 🚨 1. Portaria Digital & Controlo de Acesso (`/gatekeeper`)
- **Leitura em Tempo Real:** Suporte a leitores de código de barras USB e scanners de QR Code via câmara.
- **Painel Ao Vivo:** Monitorização instantânea de entradas, saídas e contagem de alunos presentes no recinto escolar.
- **Cartão de Estudante com Emblema:** Emissão de cartões de estudante personalizáveis com fotos, QR Code/Barcode e o **Emblema Oficial da República de Moçambique**.
- **Trilha de Acessos:** Registo detalhado com data, hora e fotos dos movimentos de alunos e visitantes.

### 🇲🇿 2. Emblema da República & Conformidade MINEDH
- **Inclusão em Documentos Oficiais:** O Emblema da República de Moçambique está integrado nativamente em todos os impressos oficiais (Certidões de Matrícula, Certificados de Habilitações, Boletins de Notas, Pautas de Avaliação e Cartões).
- **Assinaturas Institucionais Inteligentes:** Tratamento automático de duplicidade de cargos nas assinaturas (*O Director da Escola*, *O Chefe da Secretaria*, *O Director de Turma*).

### 🏛️ 3. Suporte Híbrido: Escolas Públicas vs. Escolas Privadas
- **Escolas Públicas:** Regras parametrizadas de isenção de propinas mensais e taxas específicas de inscrição.
- **Escolas Privadas:** Cobrança automática de mensalidades, geração de juros/multas por atraso, emissão de referências de pagamento e recibos em PDF.
- **Badges de Identificação:** Indicadores visuais claros em todo o sistema indicando a modalidade de ensino em vigor.

### 📱 4. Central de Comunicações & Envio de SMS (`/communications` & `/sms`)
- **Disparo de SMS:** Envio massivo ou individual de mensagens SMS para encarregados de educação e professores.
- **Modelos & Tags Dinâmicas:** Suporte a tags (`{student_name}`, `{fee_amount}`, `{due_date}`) para personalização automática.
- **Histórico & Registos (`SmsLog`):** Acompanhamento do estado de entrega e histórico completo de envios.

### 🤖 5. Assistente Virtual com Inteligência Artificial (`ZAMEDU AI Assistant`)
- **Chatbot Flutuante Integrado:** Assistente contextual acessível em toda a plataforma para auxiliar gestores e professores com pesquisas rápidas, ideias pedagógicas e relatórios.

### 📊 6. Dashboard Executivo & Analytics
- **Painel de Indicadores (KPIs):** Receita Mensal, Despesas, Resultado Líquido, Assiduidade Global, Média Académica e Quadro de Honra.
- **Gráficos Interativos:** Gráfico de evolução de receitas dos últimos 12 meses (Chart.js) e distribuição de estudantes por turma.

### 📝 7. Pré-Inscrições Sem Filas & Portal Público
- **Candidatura Online:** Formulário público no portal principal para pré-inscrição de alunos sem necessidade de deslocação física inicial à escola.

### 📚 8. Lançamento de Notas, Pautas & Boletins (`/grades`)
- **Avaliação Contínua:** Lançamento por lote de notas ACS (Avaliação Contínua e Sistemática), ACP (Avaliação Parcial) e ACF (Avaliação Final).
- **Pautas Oficiais:** Emissão de pautas trimestrais e anuais com estatísticas de aproveitamento por género e por disciplina.

### 💳 9. Gestão Financeira & Cobranças (`/payments` & `/expenses`)
- **Propinas & Recibos:** Emissão de recibos profissionais em PDF, acompanhamento de mensalidades pendentes e cobrança por lote.
- **Gestão de Despesas:** Categorização financeira e relatórios com cálculo de **Resultado Líquido** (Receita - Despesas).

### 👥 10. Portais Dedicados (Encarregados & Professores)
- **Portal do Encarregado (`/parent/dashboard`):** Consulta de notas, faltas, boletim e propinas em atraso.
- **Portal do Professor (`/teacher/dashboard`):** Registo de chamadas, lançamento de notas e pedido de licenças.

---

## 🛠️ Tecnologias Utilizadas

- **Backend:** Laravel 10 / 12 (PHP 8.2 / 8.3)
- **Frontend:** Blade, TailwindCSS, Alpine.js, Bootstrap 5, Chart.js 4.4.1 UMD, SweetAlert2
- **PDF Engine:** DomPDF com otimização para carregamento local de imagens (`public_path`)
- **Base de Dados:** MySQL 8.0 / MariaDB (SQLite para ambiente de testes PHPUnit)
- **Controlo de Acessos:** Spatie Laravel-Permission (RBAC) com 5 papéis nativos
- **Licenciamento SaaS:** Middleware `LicenseCheckMiddleware` (ativo, carência e suspenso)

---

## 📚 Documentação Técnica

Toda a documentação do projeto está localizada no diretório `docs/`:

1. 📘 [ONBOARDING.md](ONBOARDING.md) — Guia de instalação e configuração de novas instâncias.
2. 🎨 [docs/DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md) — Paleta de cores, tokens CSS e componentes visuais.
3. 🏗️ [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) — Arquitetura de software, middlewares e permissões RBAC.
4. 📋 [docs/MODULES_AND_FEATURES.md](docs/MODULES_AND_FEATURES.md) — Especificação exaustiva dos módulos funcionais.
5. 📊 [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) — Esquema relacional da base de dados e dicionário de tabelas.
6. 🖥️ [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) — Manual de implantação em produção (Nginx, SSL, PHP-FPM).
7. 💳 [docs/API_AND_INTEGRATIONS.md](docs/API_AND_INTEGRATIONS.md) — Endpoints REST, Gateways SMS e integrações.
8. 📱 [docs/PWA_AND_ACCESSIBILITY.md](docs/PWA_AND_ACCESSIBILITY.md) — PWA Standalone e conformidade WCAG 2.1 AA.
9. 🧪 [docs/QA_CHECKLIST.md](docs/QA_CHECKLIST.md) — Checklist de qualidade e testes automatizados.
10. 🔄 [docs/UPGRADE_LARAVEL_12.md](docs/UPGRADE_LARAVEL_12.md) — Guia de atualização do Laravel.

---

## 🚀 Instalação Rápida

```bash
# 1. Clonar o repositório oficial
git clone git@github.com:filipeive/sige-zamedu.git
cd sige-zamedu

# 2. Instalar dependências do PHP e Node.js
composer install
npm install && npm run build

# 3. Configurar o ambiente
cp .env.example .env
php artisan key:generate

# 4. Executar as migrações e seeders obrigatórios
php artisan migrate --seed

# 5. Iniciar o servidor local
php artisan serve
```

---

## 👨‍💻 Desenvolvedor & Autor

- **Autor:** Filipe dos Santos
- **Website / Portfolio:** [http://146.235.224.99/](http://146.235.224.99/)
- **Repositório GitHub:** [git@github.com:filipeive/sige-zamedu.git](https://github.com/filipeive/sige-zamedu)

---

## 📄 Licença & Propriedade

Direitos reservados © 2026 **ZAMEDU - SIGE** por **Filipe dos Santos**. Proibida a reprodução ou distribuição não autorizada sem licença comercial prévia.
