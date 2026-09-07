<!-- Modal de Regras de Cálculo de Notas (MINEDH) -->
<div class="modal fade" id="gradeCalculationModal" tabindex="-1" aria-labelledby="gradeCalculationModalLabel" aria-hidden="true" style="z-index: 10050;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Modal Header --}}
            <div class="modal-header bg-gradient bg-emerald-700 text-white p-3 px-4" style="background: linear-gradient(135deg, #047857 0%, #065f46 100%);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="w-10 h-10 rounded-3 bg-white/20 d-flex align-items-center justify-center text-white font-bold shadow-sm">
                        <i class="fas fa-calculator text-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-black text-white mb-0" id="gradeCalculationModalLabel">Fórmula &amp; Regras de Cálculo de Notas</h5>
                        <p class="text-white-50 text-xs mb-0">Configuração das ponderações de ACS, ACP e limiar de aprovação oficial (MINEDH)</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="{{ route('pautas.update-rules') }}" method="POST" id="gradeRulesForm">
                @csrf
                <div class="modal-body p-4 space-y-4">

                    {{-- 1. Card Explicativo com Fórmula Dinâmica --}}
                    <div class="card border-emerald-200 bg-emerald-50/50 rounded-3 p-3 text-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-black text-uppercase tracking-wider text-emerald-800">
                                <i class="fas fa-square-root-variable me-1 text-emerald-600"></i> Fórmula de Cálculo Ativa
                            </span>
                            <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300">Moçambique (MINEDH)</span>
                        </div>

                        <div class="row g-2 text-center my-2">
                            <div class="col-md-6">
                                <div class="bg-white p-2.5 rounded-3 border border-emerald-200 shadow-2xs">
                                    <span class="text-muted text-3xs font-uppercase fw-bold block mb-1">Média das ACS (MACS)</span>
                                    <code class="text-emerald-700 fw-extrabold text-sm block" id="previewMacsFormula">
                                        MACS = ∑(ACS) / n
                                    </code>
                                    <span class="text-muted text-[10px] block mt-1" id="previewMacsDesc">
                                        Soma das avaliações contínuas dividida apenas pelas notas lançadas.
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-white p-2.5 rounded-3 border border-emerald-200 shadow-2xs">
                                    <span class="text-muted text-3xs font-uppercase fw-bold block mb-1">Média Trimestral (MT)</span>
                                    <code class="text-emerald-700 fw-extrabold text-sm block" id="previewMtFormula">
                                        MT = ({{ $calculationRules['macs_weight'] ?? 2 }}×MACS + {{ $calculationRules['acp_weight_in_mt'] ?? 1 }}×ACP) / {{ ($calculationRules['macs_weight'] ?? 2) + ($calculationRules['acp_weight_in_mt'] ?? 1) }}
                                    </code>
                                    <span class="text-muted text-[10px] block mt-1">
                                        Média ponderada entre a MACS e a Prova Parcial (ACP).
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Especial Exames Nacionais SNE (Lei 18/2018) -->
                        <div class="mt-3 pt-2 border-top border-emerald-200">
                            <span class="fw-bold text-xs text-danger uppercase block mb-1">
                                <i class="fas fa-balance-scale me-1"></i> Regras de Exame Nacional SNE (Lei 18/2018 - 9ª, 10ª e 12ª Classes)
                            </span>
                            <div class="row g-2 text-center text-xs">
                                <div class="col-md-4">
                                    <div class="bg-white p-2 rounded-3 border border-danger-subtle">
                                        <span class="text-muted text-[10px] block">Média Anual Frequência</span>
                                        <code class="text-danger fw-bold">MAF = (MT1 + MT2 + MT3) / 3</code>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-white p-2 rounded-3 border border-danger-subtle">
                                        <span class="text-muted text-[10px] block">Nota Eficaz do Exame</span>
                                        <code class="text-danger fw-bold">NE_Eficaz = max(NE, ACF)</code>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-white p-2 rounded-3 border border-danger-subtle">
                                        <span class="text-muted text-[10px] block">Nota Final da Disciplina</span>
                                        <code class="text-danger fw-bold">NFD = round((MAF + NE_E) / 2)</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Campos de Configuração do Cálculo --}}
                    <div class="row g-3">
                        {{-- Método de Cálculo da MACS --}}
                        <div class="col-md-7">
                            <label class="form-label text-xs fw-extrabold text-dark mb-1">
                                <i class="fas fa-divide text-emerald-600 me-1"></i> Método de Cálculo da MACS (ACS)
                            </label>
                            <select name="macs_calculation_method" id="rule_macs_method" class="form-select form-select-sm border-slate-300 font-semibold" onchange="updateFormulaPreview()">
                                <option value="launched_only" @selected(($calculationRules['macs_calculation_method'] ?? 'launched_only') === 'launched_only')>
                                    Divisão apenas pelas ACS Lançadas (Padrão MINEDH)
                                </option>
                                <option value="fixed_count" @selected(($calculationRules['macs_calculation_method'] ?? 'launched_only') === 'fixed_count')>
                                    Divisão por Número Fixo de ACS do Trimestre
                                </option>
                            </select>
                            <span class="form-text text-muted text-[11px]">
                                Exemplo: Se o aluno tiver 2 ACS lançadas (14 e 16), divide por 2 (MACS = 15.0).
                            </span>
                        </div>

                        {{-- Total Fixo de ACS --}}
                        <div class="col-md-5">
                            <label class="form-label text-xs fw-extrabold text-dark mb-1">
                                <i class="fas fa-list-ol text-emerald-600 me-1"></i> Nº Padrão de ACS por Trimestre
                            </label>
                            <input type="number" min="1" max="6" name="default_acs_count" id="rule_default_acs_count" 
                                   value="{{ $calculationRules['default_acs_count'] ?? 3 }}" 
                                   class="form-select form-select-sm border-slate-300 font-semibold" onchange="updateFormulaPreview()" />
                            <span class="form-text text-muted text-[11px]">
                                Quantidade de ACS esperadas por trimestre (geralmente 3).
                            </span>
                        </div>

                        {{-- Peso da MACS --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-extrabold text-dark mb-1">
                                <i class="fas fa-weight-hanging text-emerald-600 me-1"></i> Peso da MACS na MT
                            </label>
                            <input type="number" step="0.1" min="0.1" max="10" name="macs_weight" id="rule_macs_weight" 
                                   value="{{ $calculationRules['macs_weight'] ?? 2 }}" 
                                   class="form-control form-control-sm border-slate-300 font-semibold" oninput="updateFormulaPreview()" />
                            <span class="form-text text-muted text-[11px]">
                                Peso atribuído à média contínua (ex: 2).
                            </span>
                        </div>

                        {{-- Peso da ACP --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-extrabold text-dark mb-1">
                                <i class="fas fa-file-signature text-emerald-600 me-1"></i> Peso da ACP (Prova)
                            </label>
                            <input type="number" step="0.1" min="0.1" max="10" name="acp_weight_in_mt" id="rule_acp_weight" 
                                   value="{{ $calculationRules['acp_weight_in_mt'] ?? 1 }}" 
                                   class="form-control form-control-sm border-slate-300 font-semibold" oninput="updateFormulaPreview()" />
                            <span class="form-text text-muted text-[11px]">
                                Peso atribuído à prova parcial (ex: 1).
                            </span>
                        </div>

                        {{-- Limiar Mínimo de Aprovação --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-extrabold text-dark mb-1">
                                <i class="fas fa-check-double text-emerald-600 me-1"></i> Nota Mínima de Aprovação
                            </label>
                            <input type="number" step="0.1" min="0" max="20" name="passing_grade" id="rule_passing_grade" 
                                   value="{{ $calculationRules['passing_grade'] ?? 9.5 }}" 
                                   class="form-control form-control-sm border-slate-300 font-semibold" />
                            <span class="form-text text-muted text-[11px]">
                                Limiar para resultado positivo (ex: 9.5 ou 10.0 valores).
                            </span>
                        </div>
                    </div>

                    {{-- Exemplo Prático Calculado --}}
                    <div class="p-3 bg-slate-50 rounded-3 border border-slate-200 text-xs">
                        <span class="fw-bold text-slate-700 block mb-1">
                            <i class="fas fa-lightbulb text-amber-500 me-1"></i> Simulação de Exemplo com as Regras Acima:
                        </span>
                        <p class="mb-0 text-slate-600" id="practicalSimulationText">
                            Se um aluno obtiver <strong>ACS1 = 12</strong>, <strong>ACS2 = 14</strong> e <strong>ACP = 15</strong>:<br>
                            &bull; <strong>MACS:</strong> (12 + 14) / 2 = <strong>13.0</strong><br>
                            &bull; <strong>MT:</strong> (2 &times; 13.0 + 1 &times; 15) / 3 = <strong>13.7 valores</strong> (Aprovado).
                        </p>
                    </div>

                </div>

                <div class="modal-footer bg-light p-3 px-4 border-top">
                    <button type="button" class="btn btn-secondary text-xs font-weight-bold px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success text-xs font-weight-bold px-4 shadow-sm" style="background-color: #047857; border-color: #047857;">
                        <i class="fas fa-save me-1"></i> Salvar e Aplicar Fórmula
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateFormulaPreview() {
    const macsMethod = document.getElementById('rule_macs_method')?.value || 'launched_only';
    const defaultAcsCount = parseInt(document.getElementById('rule_default_acs_count')?.value || 3);
    const macsWeight = parseFloat(document.getElementById('rule_macs_weight')?.value || 2);
    const acpWeight = parseFloat(document.getElementById('rule_acp_weight')?.value || 1);
    const totalWeight = macsWeight + acpWeight;

    // Atualizar preview MACS
    const previewMacsDesc = document.getElementById('previewMacsDesc');
    const previewMacsFormula = document.getElementById('previewMacsFormula');
    if (macsMethod === 'fixed_count') {
        if (previewMacsFormula) previewMacsFormula.innerText = `MACS = ∑(ACS) / ${defaultAcsCount}`;
        if (previewMacsDesc) previewMacsDesc.innerText = `Soma das ACS dividida sempre por ${defaultAcsCount} (total fixo do trimestre).`;
    } else {
        if (previewMacsFormula) previewMacsFormula.innerText = `MACS = ∑(ACS) / n`;
        if (previewMacsDesc) previewMacsDesc.innerText = `Soma das avaliações contínuas dividida apenas pelas notas lançadas.`;
    }

    // Atualizar preview MT
    const previewMtFormula = document.getElementById('previewMtFormula');
    if (previewMtFormula) {
        previewMtFormula.innerText = `MT = (${macsWeight}×MACS + ${acpWeight}×ACP) / ${totalWeight}`;
    }

    // Atualizar simulação
    const practicalSimulationText = document.getElementById('practicalSimulationText');
    if (practicalSimulationText) {
        let macsVal = 13.0;
        if (macsMethod === 'fixed_count') {
            macsVal = (12 + 14) / defaultAcsCount;
        }
        const mtVal = (macsWeight * macsVal + acpWeight * 15) / totalWeight;
        practicalSimulationText.innerHTML = `
            Se um aluno obtiver <strong>ACS1 = 12</strong>, <strong>ACS2 = 14</strong> e <strong>ACP = 15</strong>:<br>
            &bull; <strong>MACS:</strong> ${macsVal.toFixed(1)} valores (${macsMethod === 'fixed_count' ? 'soma 26 / ' + defaultAcsCount : 'média de 2 lançadas'})<br>
            &bull; <strong>MT:</strong> (${macsWeight} &times; ${macsVal.toFixed(1)} + ${acpWeight} &times; 15) / ${totalWeight} = <strong>${mtVal.toFixed(1)} valores</strong>.
        `;
    }
}
</script>
