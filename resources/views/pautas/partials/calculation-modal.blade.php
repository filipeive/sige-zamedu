<!-- Modal de Regras de Cálculo de Notas (MINEDH) -->
<div x-data="{ open: false }" 
     x-show="open" 
     x-on:open-grade-rules-modal.window="open = true" 
     x-on:keydown.escape.window="open = false" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-200">

            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-emerald-800 to-emerald-950 p-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold shadow-xs">
                        <i class="fas fa-calculator text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Fórmula &amp; Regras de Cálculo de Notas</h3>
                        <p class="text-emerald-100 text-xs mt-0.5">Configuração das ponderações de ACS, ACP e limiar de aprovação oficial (MINEDH)</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-emerald-200 hover:text-white transition text-lg p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('pautas.update-rules') }}" method="POST" id="gradeRulesForm">
                @csrf
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">

                    {{-- 1. Card Explicativo com Fórmula Dinâmica --}}
                    <div class="border border-emerald-200 bg-emerald-50/60 rounded-xl p-4 text-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                                <i class="fas fa-square-root-variable text-emerald-600"></i> Fórmula de Cálculo Ativa
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Moçambique (MINEDH)</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-center">
                            <div class="bg-white p-3 rounded-xl border border-emerald-200 shadow-2xs">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block mb-1">Média das ACS (MACS)</span>
                                <code class="text-emerald-700 font-mono font-extrabold text-xs block" id="previewMacsFormula">
                                    MACS = ∑(ACS) / n
                                </code>
                                <span class="text-slate-500 text-[10px] block mt-1" id="previewMacsDesc">
                                    Soma das avaliações contínuas dividida apenas pelas notas lançadas.
                                </span>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-emerald-200 shadow-2xs">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block mb-1">Média Trimestral (MT)</span>
                                <code class="text-emerald-700 font-mono font-extrabold text-xs block" id="previewMtFormula">
                                    MT = ({{ $calculationRules['macs_weight'] ?? 2 }}×MACS + {{ $calculationRules['acp_weight_in_mt'] ?? 1 }}×ACP) / {{ ($calculationRules['macs_weight'] ?? 2) + ($calculationRules['acp_weight_in_mt'] ?? 1) }}
                                </code>
                                <span class="text-slate-500 text-[10px] block mt-1">
                                    Média ponderada entre a MACS e a Prova Parcial (ACP).
                                </span>
                            </div>
                        </div>

                        <!-- Seção Especial Exames Nacionais SNE (Lei 18/2018) -->
                        <div class="pt-3 border-t border-emerald-200 space-y-2">
                            <span class="font-extrabold text-xs text-rose-700 uppercase block">
                                <i class="fas fa-balance-scale mr-1"></i> Regras de Exame Nacional SNE (Lei 18/2018 - 9ª, 10ª e 12ª Classes)
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-center text-xs">
                                <div class="bg-white p-2 rounded-xl border border-rose-200">
                                    <span class="text-slate-500 text-[10px] block">Média Anual Frequência</span>
                                    <code class="text-rose-700 font-mono font-bold text-[11px]">MAF = (MT1 + MT2 + MT3) / 3</code>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-rose-200">
                                    <span class="text-slate-500 text-[10px] block">Nota Eficaz do Exame</span>
                                    <code class="text-rose-700 font-mono font-bold text-[11px]">NE_Eficaz = max(NE, ACF)</code>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-rose-200">
                                    <span class="text-slate-500 text-[10px] block">Nota Final da Disciplina</span>
                                    <code class="text-rose-700 font-mono font-bold text-[11px]">NFD = round((MAF + NE_E) / 2)</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Campos de Configuração do Cálculo --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        {{-- Método de Cálculo da MACS --}}
                        <div class="sm:col-span-7">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                <i class="fas fa-divide text-emerald-600 mr-1"></i> Método de Cálculo da MACS (ACS)
                            </label>
                            <select name="macs_calculation_method" id="rule_macs_method" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="updateFormulaPreview()">
                                <option value="launched_only" @selected(($calculationRules['macs_calculation_method'] ?? 'launched_only') === 'launched_only')>
                                    Divisão apenas pelas ACS Lançadas (Padrão MINEDH)
                                </option>
                                <option value="fixed_count" @selected(($calculationRules['macs_calculation_method'] ?? 'launched_only') === 'fixed_count')>
                                    Divisão por Número Fixo de ACS do Trimestre
                                </option>
                            </select>
                            <span class="text-slate-500 text-[11px] mt-1 block">
                                Exemplo: Se o aluno tiver 2 ACS lançadas (14 e 16), divide por 2 (MACS = 15.0).
                            </span>
                        </div>

                        {{-- Total Fixo de ACS --}}
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                <i class="fas fa-list-ol text-emerald-600 mr-1"></i> Nº Padrão de ACS por Trimestre
                            </label>
                            <input type="number" min="1" max="6" name="default_acs_count" id="rule_default_acs_count" 
                                   value="{{ $calculationRules['default_acs_count'] ?? 3 }}" 
                                   class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="updateFormulaPreview()" />
                            <span class="text-slate-500 text-[11px] mt-1 block">
                                Quantidade de ACS esperadas por trimestre (geralmente 3).
                            </span>
                        </div>

                        {{-- Peso da MACS --}}
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                <i class="fas fa-weight-hanging text-emerald-600 mr-1"></i> Peso da MACS na MT
                            </label>
                            <input type="number" step="0.1" min="0.1" max="10" name="macs_weight" id="rule_macs_weight" 
                                   value="{{ $calculationRules['macs_weight'] ?? 2 }}" 
                                   class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" oninput="updateFormulaPreview()" />
                            <span class="text-slate-500 text-[11px] mt-1 block">
                                Peso atribuído à média contínua (ex: 2).
                            </span>
                        </div>

                        {{-- Peso da ACP --}}
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                <i class="fas fa-file-signature text-emerald-600 mr-1"></i> Peso da ACP (Prova)
                            </label>
                            <input type="number" step="0.1" min="0.1" max="10" name="acp_weight_in_mt" id="rule_acp_weight" 
                                   value="{{ $calculationRules['acp_weight_in_mt'] ?? 1 }}" 
                                   class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" oninput="updateFormulaPreview()" />
                            <span class="text-slate-500 text-[11px] mt-1 block">
                                Peso atribuído à prova parcial (ex: 1).
                            </span>
                        </div>

                        {{-- Limiar Mínimo de Aprovação --}}
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                <i class="fas fa-check-double text-emerald-600 mr-1"></i> Nota Mínima de Aprovação
                            </label>
                            <input type="number" step="0.1" min="0" max="20" name="passing_grade" id="rule_passing_grade" 
                                   value="{{ $calculationRules['passing_grade'] ?? 9.5 }}" 
                                   class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" />
                            <span class="text-slate-500 text-[11px] mt-1 block">
                                Limiar para resultado positivo (ex: 9.5 ou 10.0 valores).
                            </span>
                        </div>
                    </div>

                    {{-- Exemplo Prático Calculado --}}
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700 block mb-1">
                            <i class="fas fa-lightbulb text-amber-500 mr-1"></i> Simulação de Exemplo com as Regras Acima:
                        </span>
                        <p class="mb-0 text-slate-600 leading-relaxed" id="practicalSimulationText">
                            Se um aluno obtiver <strong>ACS1 = 12</strong>, <strong>ACS2 = 14</strong> e <strong>ACP = 15</strong>:<br>
                            &bull; <strong>MACS:</strong> (12 + 14) / 2 = <strong>13.0</strong><br>
                            &bull; <strong>MT:</strong> (2 &times; 13.0 + 1 &times; 15) / 3 = <strong>13.7 valores</strong> (Aprovado).
                        </p>
                    </div>

                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="open = false" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                        <i class="fas fa-save"></i> Salvar e Aplicar Fórmula
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

