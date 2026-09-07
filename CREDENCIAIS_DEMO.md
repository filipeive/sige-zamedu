# 🎓 SIGE ZamEdu — Guia de Credenciais & Apresentação (Prémio Jovem Criativo)

Este documento contém todas as credenciais de acesso preparadas e validadas para a demonstração e auditoria do sistema em diferentes papéis de utilizador (*roles*).

---

## 🔑 Credenciais de Acesso de Teste

| Perfil / Papel | Email de Acesso | Palavra-passe | Módulos & Funcionalidades Principais |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@visionarios.co.mz` | `password` | Controlo total, gestão de licenças, auditoria e definições globais do sistema. |
| **Administrador** | `admin@visionarios.co.mz` | `password` | Gestão de turmas, disciplinas, atribuição de professores e relatórios académicos. |
| **Secretaria** | `secretaria@visionarios.co.mz` | `password` | Matrículas de alunos, propinas, pagamentos (M-Pesa/e-Mola/Numerário), declarações e certificados. |
| **Pedagogia** | `pedagogia@visionarios.co.mz` | `password` | Monitorização pedagógica, assiduidade global, pautas de exames e desempenho por disciplina. |
| **Professor** | `professor@visionarios.co.mz` | `password` | Lançamento rápido de notas (ACS1, ACS2, ACS3, ACP), sumários e assiduidade por turma. |
| **Encarregado** | `pai@visionarios.co.mz` | `password` | Portal do Encarregado: visualização de boletins de notas, presenças e histórico de pagamentos do educando. |

---

## 📋 Regras de Negócio Validadas nas Pautas (MINEDH)

1. **Cálculo da MACS & MT (Sem Duplicação)**:
   - Toda a lógica de cálculo de **MACS** (*Média das Avaliações Contínuas e Sistemáticas*) e **MT** (*Média Trimestral*) utiliza o modelo estático reutilizável `Grade::calculateMACS()` e `Grade::calculateMT()`.
   - Considera os pesos das configurações (`macs_weight = 2`, `acp_weight_in_mt = 1`, `default_acs_count = 3`).

2. **Corte de Aprovação (>= 9.5 valores)**:
   - Alunos com **Média Geral ≥ 9.5** são automaticamente classificados como **APROVADO** (destacado em verde com ícone `fa-check-circle`).
   - Média Geral < 9.5 é classificada como **REPROVADO / RETIDO**.

3. **Turmas sem Exame (Ensino Geral / Classes Transitórias)**:
   - Em classes que não são de exame nacional (ex: 1ª a 5ª, 8ª, 9ª e 11ª classes), a Média Final de Disciplina (**MFD**) é calculada exclusivamente com base nas médias dos três trimestres (`MF = (MT1 + MT2 + MT3) / 3`), ignorando notas de exame inexistentes.

4. **Exportação das Pautas**:
   - Botões de exportação directa para **PDF Oficial** (layout paisagem A4/A3) e **Excel / CSV** (`.csv` codificado em UTF-8 BOM para abertura perfeita no Microsoft Excel).
   - Seletor de Turma directamente no topo de todas as Pautas (Trimestral, Anual e Final).

---

## 🚀 Roteiro de Demonstração Sugerido para Amanhã

1. **Apresentação do Painel Principal (Admin)**:
   - Entrar com `admin@visionarios.co.mz` / `password`.
   - Mostrar o Dashboard central, gráficos de receitas/matrículas e estatísticas de assiduidade.

2. **Demonstração do Módulo de Pautas**:
   - Aceder a **Gestão de Notas & Pautas** (`/grades`).
   - Seleccionar uma turma (ex: `5ª Classe A` ou `10ª Classe B`) e navegar entre a **Pauta Trimestral**, **Anual** e **Final & Exames**.
   - Mostrar a coluna fixa (*sticky column*) com o nome dos alunos e testar o botão **Exportar Excel** e **Exportar PDF**.

3. **Lançamento de Notas como Professor**:
   - Entrar com `professor@visionarios.co.mz` / `password`.
   - Aceder a **Lançamento em Lote** (`/grades/batch-create`).
   - Demonstrar o lançamento isolado de `ACS 1`, `ACS 2`, `ACS 3` e `ACP`.

4. **Operações da Secretaria & Cobrança**:
   - Entrar com `secretaria@visionarios.co.mz` / `password`.
   - Mostrar a lista de alunos e registo de pagamento de propina.

5. **Portal do Encarregado de Educação**:
   - Entrar com `pai@visionarios.co.mz` / `password`.
   - Visualizar a caderneta escolar digital do educando.

---
*Gerado e validado autonomamente pelo assistente AI Antigravity.*
