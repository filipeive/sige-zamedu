# Guia de Onboarding e Configuração de Clientes — ZAMEDU - SIGE

Este documento descreve o procedimento padronizado para a implementação e personalização do **ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar)** para novos clientes, desenvolvido por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)).

---

## 📋 Pré-requisitos do Servidor

- **PHP**: 8.2 ou 8.3 (extensões requeridas: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `curl`, `xml`, `zip`)
- **Base de Dados**: MySQL 8.0+ ou MariaDB 10.5+
- **Gestor de Dependências**: Composer 2.x e Node.js 18+ / NPM
- **Servidor Web**: Nginx ou Apache2 com `mod_rewrite` ativado
- **Outros**: Git, Certificado SSL (Let's Encrypt / Certbot)

---

## 🛠️ Passo a Passo de Onboarding

### 1. Clonar o Repositório Oficial
```bash
git clone git@github.com:filipeive/sige-zamedu.git
cd sige-zamedu
```

### 2. Criar a Base de Dados MySQL
```sql
CREATE DATABASE sige_escola_cliente CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configurar o Ficheiro `.env`
Copie o `.env.example` e ajuste as definições principais:

```ini
APP_NAME="ZAMEDU - SIGE"
APP_ENV=production
APP_URL=https://gestao.escolasample.co.mz

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sige_escola_cliente
DB_USERNAME=usuario_db
DB_PASSWORD=senha_segura_aqui

# Definir fuso horário oficial de Moçambique
APP_TIMEZONE=Africa/Maputo
```

### 4. Instalar Dependências e Executar Script de Setup
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
./new-school-setup.sh
```

O script `new-school-setup.sh` irá:
- Gerar a `APP_KEY`
- Criar a estrutura de tabelas via `php artisan migrate`
- Executar os seeders obrigatórios (`PermissionSeeder`, `SuperAdminSeeder`, `SettingSeeder`)
- Otimizar as caches de rotas, configurações e views (`php artisan optimize`)

---

## 🎨 Personalização Institucional (Painel Admin `/admin/settings`)

Após efetuar o primeiro acesso com as credenciais administrativas por omissão, aceda a `/admin/settings` para definir:

| Configuração | Descrição | Exemplo |
| :--- | :--- | :--- |
| `school_name` | Nome oficial completo da escola | Escola Secundária Eduardo Mondlane |
| `school_short_name` | Sigla ou nome abreviado | ESEM |
| `school_type` | Tipo de Instituição | `escola_publica` ou `escola_privada` |
| `primary_color` | Cor primária da marca | `#1e40af` (Azul Oceano) |
| `secondary_color` | Cor secundária / destaque | `#059669` (Verde Esmeralda) |
| `official_signature_director` | Nome do Director da Escola | Dr. Filipe dos Santos |
| `official_signature_secretary` | Nome do Chefe da Secretaria | Maria de Fátima |

---

## 🔑 Ativação da Licença SaaS (`/admin/license`)

Navegue até **Licença do Sistema** (`/admin/license`) para introduzir a chave de licença ativa emitida por **Filipe dos Santos**.

---

## ⚙️ Comandos Especiais de Manutenção (Artisan)

### 1. Diagnóstico de Saúde do Sistema
```bash
php artisan health:check --detailed
```

### 2. Geração em Lote de Propinas Mensais (Escolas Privadas)
```bash
php artisan payments:generate-monthly-fees --month=9 --calendar-year=2026
```

---

## 👨‍💻 Suporte Técnico & Contacto

- **Desenvolvedor:** Filipe dos Santos
- **Website:** [http://146.235.224.99/](http://146.235.224.99/)
- **Repositório:** [git@github.com:filipeive/sige-zamedu.git](https://github.com/filipeive/sige-zamedu)
