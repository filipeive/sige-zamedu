# ZAMEDU - SIGE Design System & Theme Engine Architecture

Este documento especifica o **Design System consolidado** e a arquitetura do **Theme Engine (White-Label)** do **ZAMEDU - SIGE (Sistema Integrado de Gestão Escolar)**, desenvolvido por **Filipe dos Santos** ([http://146.235.224.99/](http://146.235.224.99/)).

---

## 🎨 1. Theme Engine Architecture (`ThemeHelper.php`)

O sistema conta com um **Theme Engine dinâmico e desacoplado**, gerenciado pela classe `App\Helpers\ThemeHelper`. As variáveis CSS são geradas em tempo de execução e injetadas no `<style>` global de [layouts/app.blade.php](file:///home/fdev-ms/Filipe/visionarios-school-system/resources/views/layouts/app.blade.php).

### Injeção Dinâmica no Layout:
```html
<style>
    {!! \App\Helpers\ThemeHelper::getCssVariables() !!}
</style>
```

### Tokens CSS Universais (Light & Dark Mode):

```css
:root {
    /* Branding Tokens Institucionais */
    --primary: #1e40af;
    --primary-dark: color-mix(in srgb, var(--primary) 82%, #000);
    --primary-light: color-mix(in srgb, var(--primary) 18%, #fff);
    --primary-soft: color-mix(in srgb, var(--primary) 10%, #fff);

    --secondary: #059669;
    --secondary-dark: color-mix(in srgb, var(--secondary) 82%, #000);
    --secondary-light: color-mix(in srgb, var(--secondary) 18%, #fff);

    --accent: #F59E0B;
    --accent-light: color-mix(in srgb, var(--accent) 20%, #fff);

    /* Cores Semânticas de Estado (Harmonizadas) */
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
    --info: #06B6D4;

    /* Superfícies & Layout (Modo Claro) */
    --content-bg: #F4F6FA;
    --card-bg: #FFFFFF;
    --surface-bg: #F8FAFC;
    --sidebar-bg: #FFFFFF;
    --border-color: #E2E8F0;
    --text-primary: #0F172A;
    --text-secondary: #475569;
    --text-muted: #94A3B8;
}

[data-bs-theme="dark"] {
    /* Superfícies & Layout (Modo Escuro) */
    --content-bg: #0B132B;
    --card-bg: #1C2541;
    --surface-bg: #151E3D;
    --sidebar-bg: #1C2541;
    --border-color: #2D3748;
    --text-primary: #F8FAFC;
    --text-secondary: #CBD5E1;
    --text-muted: #64748B;
}
```

---

## 🇲🇿 2. Padrões Visuais Oficiais & Emblema da República

Todos os documentos oficiais (Certidões, Certificados, Boletins, Cartões de Aluno e Pautas) utilizam o **Emblema da República de Moçambique** centralizado no topo com tipografia oficial serifada/sans-serif legível e linhas divisórias ornamentais.

- **Ficheiro de Emblema:** `public/images/emblema-mocambique.png`
- **Dimensões Padrão:** `width: 65px; height: auto;`
- **Assinaturas Institucionais:** Linhas duplas de validação para o Director da Escola e Chefe da Secretaria com mitigação de duplicidade de cargos.

---

## 🧩 3. Catálogo de Componentes Reutilizáveis

A plataforma padronizou seus componentes principais na pasta `resources/views/components/`:

### 3.1 Cartões Estatísticos (`<x-cards.stat>`)
```html
<x-cards.stat 
    title="Total de Alunos" 
    value="189" 
    icon="fas fa-user-graduate" 
    type="primary" 
    subtitle="Ano Lectivo 2026" />
```

### 3.2 Alertas & Feeds (`<x-alerts.success>` / `<x-alerts.error>`)
```html
<x-alerts.success message="Configurações atualizadas com sucesso!" />
<x-alerts.error message="Erro ao processar o pagamento." />
```

### 3.3 Componentes da Portaria Digital (`/gatekeeper`)
- Badge de presenças em tempo real com indicador visual pulsante verde/vermelho.
- Modal de pré-visualização de Cartão de Estudante com lados frontal e verso.

---

## 🎨 4. Módulo de Personalização Institucional (Painel Admin)

Aceda a `/admin/settings` (Tab 1: Escola & Marca) para definir visualmente:
- **Tipo de Escola**: Escola Pública ou Escola Privada.
- **Cor Principal (Primary)**: Escolha via Color Picker.
- **Cor Secundária (Secondary)**: Escolha via Color Picker.
- **Assinaturas Oficiais**: Nome do Director da Escola e Chefe da Secretaria.
