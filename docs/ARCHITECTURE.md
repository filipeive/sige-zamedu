# Arquitetura de Software — ZAMEDU - SIGE

Este documento detalha a arquitetura técnica, os auxiliares de sistema, o Theme Engine, os módulos de conformidade MINEDH e a gestão de permissões do **ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar)**, desenvolvido por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)).

---

## 🏗️ 1. Visão Geral da Arquitetura

O ZAMEDU - SIGE foi desenvolvido sob o padrão **MVC (Model-View-Controller)** desacoplado no ecossistema **Laravel 10 / 12**, combinando robustez no backend e reatividade moderna no frontend sem complexidade excessiva de frameworks JS no cliente.

```
                                  +-----------------------+
                                  | Nginx / Reverse Proxy |
                                  +-----------+-----------+
                                              |
                                              v
                                  +-----------------------+
                                  | LicenseCheckMiddleware|
                                  +-----------+-----------+
                                              |
                                              v
                                  +-----------------------+
                                  |  ThemeEngine & Helpers|
                                  |    (SchoolHelper.php) |
                                  +-----------+-----------+
                                              |
                                              v
                                  +-----------------------+
                                  |  Spatie Role & Perms  |
                                  +-----------+-----------+
                                              |
               +------------------------------+------------------------------+
               |                              |                              |
               v                              v                              v
     +--------------------+        +--------------------+        +--------------------+
     | Admin & Gatekeeper |        |  Teacher Portal    |        |   Parent Portal    |
     +--------------------+        +--------------------+        +--------------------+
               |                              |                              |
               +------------------------------+------------------------------+
                                              |
                                              v
                                  +-----------------------+
                                  |  Eloquent ORM Model   |
                                  +-----------+-----------+
                                              |
                                              v
                                  +-----------------------+
                                  | MySQL 8.0 / MariaDB   |
                                  +-----------------------+
```

---

## 🛠️ 2. Helpers e Motores Auxiliares Core

### `App\Helpers\SchoolHelper`
Auxiliar global que centraliza a lógica de negócio das instituições de ensino:
- **`isPublicSchool()` / `isPrivateSchool()`**: Determina se a escola atual opera sob regras de ensino público ou privado para exibição de botões, taxas e recolha de mensalidades.
- **`getSchoolBadge()`**: Retorna os badges visuais de identificação do tipo de instituição.
- **`getOfficialDirectorName()` / `getOfficialSecretaryName()`**: Retorna e sanitiza os nomes dos responsáveis institucionais prevenindo duplicação de cargos nas assinaturas de pautas e certificados.

### `App\Helpers\ThemeHelper`
Gerencia dinamicamente o sistema de White-Labeling e variáveis CSS baseadas nos parâmetros gravados em `settings`:
- `--primary`: Cor primária da interface (padrão `#1e40af`).
- `--secondary`: Cor de contraste e componentes (padrão `#059669`).
- Modos Claro e Escuro com transição de tokens sem necessidade de recompilação do Tailwind.

---

## 🇲🇿 3. Motor de PDF e Renderização com Emblema da República

Para garantir 100% de precisão nos documentos oficiais do MINEDH (Certidões, Certificados, Boletins, Cartões e Pautas), a integração com o **DomPDF** utiliza acesso a ficheiros estáticos locais em vez de URLs remotas HTTP:

```php
// Padrão de Inclusão no DomPDF (Evita falhas de rede ou bloqueio de assets)
$emblemaPath = public_path('images/emblema-mocambique.png');
```

Isto garante renderização instantânea, sem atrasos de requisição de rede e com resolução nítida para impressão oficial.

---

## 🚪 4. Portaria Digital (`/gatekeeper`)

O módulo de Portaria Digital interage em tempo real com hardware de varrimento (leitores USB de código de barras e câmaras web/mobile) através do componente JS desacoplado `html5-qrcode` e escuta nativa de eventos de teclado (padrão USB HID).

- **Registo instantâneo:** Cria automaticamente entradas na tabela `gate_logs`.
- **Validação visual:** Exibe a foto do aluno, estado da matrícula e alerta sonoro/visual.

---

## 📱 5. Gateway de SMS e Comunicações (`App\Services\Sms`)

O serviço de SMS abstrai a comunicação com a API de telefonia para disparo de mensagens curtas:
- Grava todas as saídas na tabela `sms_logs`.
- Suporta substituição de variáveis pré-definidas em tempo de envio (`{student_name}`, `{fee_amount}`, etc.).

---

## 🤖 6. Assistente de IA (`App\Services\Ai`)

O assistente interativo grava o histórico das sessões nas tabelas `ai_conversations` e `messages`, permitindo consultas continuadas com contextos escolares locais.

---

## 🔑 7. Middleware de Licenciamento SaaS (`LicenseCheckMiddleware`)

Intercepta todas as requisições autenticadas para verificar o estado da licença emitida por **Filipe dos Santos**:
1. `active`: Acesso desbloqueado.
2. `grace_period`: Alerta no cabeçalho do painel de controlo.
3. `suspended`: Bloqueio imediato com redirecionamento para `/license/suspended`.
