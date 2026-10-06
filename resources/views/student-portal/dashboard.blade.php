@extends('layouts.app')

@section('title', 'Portal do Estudante')
@section('page-title', 'Portal do Estudante')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Portal do Estudante</li>
@endsection

@section('meta_description', 'Acompanhe o seu desempenho académico, notas, assiduidade e simule a sua nota final de disciplina (NFD) para exames nacionais. Portal oficial do estudante.')</p>
@section('page-icon', 'fas fa-user-graduate')
@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    @if($noStudentRecord)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center max-w-xl mx-auto shadow-sm">
        <i class="fas fa-user-graduate text-4xl text-amber-600 mb-3"></i>
        <h3 class="text-base font-bold text-amber-900">Perfil de Aluno em Configuração</h3>
        <p class="text-xs text-amber-700 mt-1">
            Não foi encontrado um registo de estudante associado à sua conta. Por favor, contacte a secretaria da sua escola.
        </p>
    </div>
    @else

    <!-- Header do Estudante (Banner Card) -->
    <div class="relative bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-950 rounded-3xl p-6 text-white shadow-xl mb-6 overflow-hidden border border-emerald-700/50">
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-graduation-cap text-9xl"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="relative">
                    @if($student->passport_photo)
                        <img src="{{ asset('storage/' . $student->passport_photo) }}" alt="{{ $student->first_name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-white/40 shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-emerald-600/60 border-2 border-white/30 flex items-center justify-center text-3xl font-extrabold text-white shadow-md">
                            {{ substr($student->first_name, 0, 1) }}
                        </div>
                    @endif
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-400 border-2 border-slate-900 flex items-center justify-center text-[9px] text-slate-900 font-bold" title="Matrícula Ativa">
                        <i class="fas fa-check"></i>
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            {{ $class ? $class->name : 'Ensino Secundário Geral' }}
                        </span>
                        @if($isExamClass)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/30 text-purple-200 border border-purple-400/30 flex items-center gap-1">
                            <i class="fas fa-award text-xs"></i> Classe de Exame Nacional ({{ $class->grade_level }}ª Classe)
                        </span>
                        @endif
                    </div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight mt-1">{{ $student->first_name }} {{ $student->last_name }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-emerald-200/90 mt-1">
                        <span><i class="fas fa-id-card me-1 text-emerald-400"></i> Nº: <strong>{{ $student->student_number ?? 'EST-'.$student->id }}</strong></span>
                        <span><i class="fas fa-school me-1 text-emerald-400"></i> {{ $student->school?->name ?? 'Escola Secundária' }}</span>
                        <span><i class="fas fa-calendar-alt me-1 text-emerald-400"></i> Ano Lectivo: <strong>{{ $currentYear }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Botões Rápidos -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" onclick="openStudentCardModal()" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-semibold backdrop-blur transition flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-id-badge text-amber-400"></i> Cartão de Estudante
                </button>
            </div>
        </div>
    </div>

    <!-- KPIs Rápidos do Estudante -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Média Geral -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase text-slate-400">Média Geral ({{ $currentTerm }}º Trimestre)</span>
                <div class="text-2xl font-black text-slate-800 mt-0.5">
                    {{ $overallAverage !== null ? number_format($overallAverage, 1) : '—' }}
                    <span class="text-xs font-semibold text-slate-400">/ 20</span>
                </div>
                <div class="text-[11px] mt-1 font-semibold {{ ($overallAverage ?? 0) >= 9.5 ? 'text-emerald-600' : 'text-amber-600' }}">
                    <i class="fas {{ ($overallAverage ?? 0) >= 9.5 ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                    {{ ($overallAverage ?? 0) >= 9.5 ? 'Aproveitamento Positivo' : 'Necessita de Reforço' }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <!-- Taxa de Assiduidade -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase text-slate-400">Taxa de Assiduidade</span>
                <div class="text-2xl font-black text-slate-800 mt-0.5">
                    {{ number_format($attendanceRate, 1) }}%
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $presences }} presenças · {{ $unjustifiedAbsences }} faltas injustificadas
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <!-- Disciplinas -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase text-slate-400">Disciplinas Curriculares</span>
                <div class="text-2xl font-black text-slate-800 mt-0.5">
                    {{ count($gradesData) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Ensino Secundário Geral (SNE)
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-book-open"></i>
            </div>
        </div>

        <!-- Meta Pedagógica MINEDH -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase text-slate-400">Limiar Oficial MINEDH</span>
                <div class="text-2xl font-black text-slate-800 mt-0.5">
                    9.5 <span class="text-xs font-semibold text-slate-400">Valores</span>
                </div>
                <div class="text-[11px] text-emerald-700 font-semibold mt-1">
                    Lei 18/2018 (SNE Moçambique)
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>

    <!-- Navegação por Trimestre & Tabela de Notas -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-base text-slate-800 flex items-center gap-2">
                    <i class="fas fa-award text-emerald-600"></i> Notas e Avaliações Contínuas
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Cálculo automático em conformidade com o motor oficial do MINEDH (ACS1, ACS2, ACS3, MACS e MT).
                </p>
            </div>

            <!-- Selector de Trimestre -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-200/60 rounded-xl">
                @foreach([1 => '1º Trimestre', 2 => '2º Trimestre', 3 => '3º Trimestre'] as $t => $label)
                <a href="{{ route('student.dashboard', ['term' => $t]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $currentTerm == $t ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100/75 text-slate-600 font-bold uppercase tracking-wider text-[10.5px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Disciplina</th>
                        <th class="py-3 px-3 text-center">ACS 1</th>
                        <th class="py-3 px-3 text-center">ACS 2</th>
                        <th class="py-3 px-3 text-center">ACS 3</th>
                        <th class="py-3 px-3 text-center bg-emerald-50/50 text-emerald-900">MACS</th>
                        <th class="py-3 px-3 text-center">ACP</th>
                        <th class="py-3 px-3 text-center bg-emerald-100/60 text-emerald-950 font-extrabold">MT (Média)</th>
                        <th class="py-3 px-4 text-center">Situação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($gradesData as $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            {{ $item['subject']->name }}
                            <span class="block text-[10px] font-normal text-slate-400">{{ $item['subject']->code }}</span>
                        </td>
                        <td class="py-3.5 px-3 text-center text-slate-700">{{ $item['acs1'] !== null ? number_format($item['acs1'], 1) : '—' }}</td>
                        <td class="py-3.5 px-3 text-center text-slate-700">{{ $item['acs2'] !== null ? number_format($item['acs2'], 1) : '—' }}</td>
                        <td class="py-3.5 px-3 text-center text-slate-700">{{ $item['acs3'] !== null ? number_format($item['acs3'], 1) : '—' }}</td>
                        <td class="py-3.5 px-3 text-center font-bold bg-emerald-50/30 text-emerald-800">{{ $item['macs'] !== null ? number_format($item['macs'], 1) : '—' }}</td>
                        <td class="py-3.5 px-3 text-center text-slate-700">{{ $item['acp'] !== null ? number_format($item['acp'], 1) : '—' }}</td>
                        <td class="py-3.5 px-3 text-center font-black text-sm bg-emerald-100/40 {{ ($item['mt'] ?? 0) >= 9.5 ? 'text-emerald-700' : 'text-amber-600' }}">
                            {{ $item['mt'] !== null ? number_format($item['mt'], 1) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item['mt'] === null)
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">A Decorrer</span>
                            @elseif($item['mt'] >= 9.5)
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-check-circle me-1"></i> Positiva
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Reforço
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-slate-400">
                            Nenhuma nota lançada para o {{ $currentTerm }}º Trimestre até ao momento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Simulador de Exame Nacional (Destaque para Classes 6ª, 9ª e 12ª) -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 rounded-3xl p-6 text-white shadow-lg mb-6 border border-slate-700">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
            <div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase tracking-wider">
                    <i class="fas fa-calculator me-1"></i> Simulador Oficial MINEDH
                </span>
                <h3 class="text-lg font-bold text-white mt-1">Simulador de Exame Nacional (6ª, 9ª e 12ª Classes)</h3>
                <p class="text-xs text-slate-400">
                    Fórmula oficial: $NE\_Eficaz = \max(NE, ACF)$ e $NFD = \text{round}\left(\frac{MAF + NE\_Eficaz}{2}\right)$. Calcule a nota mínima que necessita no exame!
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-white/5 p-4 rounded-2xl border border-white/10">
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Média Anual de Frequência (MAF)</label>
                <input type="number" id="sim_maf" step="0.1" min="0" max="20" value="{{ $overallAverage ?? 11.5 }}" 
                       class="w-full rounded-xl bg-white/10 border border-white/20 text-white px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Simular Nota do Exame (NE)</label>
                <input type="number" id="sim_ne" step="0.1" min="0" max="20" value="10.0" 
                       class="w-full rounded-xl bg-white/10 border border-white/20 text-white px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Avaliação Contínua Final (ACF opcional)</label>
                <input type="number" id="sim_acf" step="0.1" min="0" max="20" placeholder="Sem ACF" 
                       class="w-full rounded-xl bg-white/10 border border-white/20 text-white px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <button type="button" onclick="runExamSimulation()" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-wand-magic-sparkles"></i> Calcular NFD
                </button>
            </div>
        </div>

        <!-- Resultado da Simulação -->
        <div id="sim_result_box" class="mt-4 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <span class="text-xs text-emerald-300 font-semibold" id="sim_status_label">Resultado da Simulação:</span>
                    <h4 class="text-base font-extrabold text-white" id="sim_result_text">Com NE de 10.0, a sua Nota Final de Disciplina (NFD) será 11 valores (Aprovado).</h4>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[10px] text-slate-400 block uppercase">Nota Final Estimada</span>
                <span class="text-2xl font-black text-amber-400" id="sim_nfd_display">11 / 20</span>
            </div>
        </div>
    </div>

    <!-- Modal do Cartão de Estudante -->
    <div id="studentCardModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden p-6 text-center relative">
            <button type="button" onclick="closeStudentCardModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-sm">
                <i class="fas fa-times"></i>
            </button>

            <!-- Frente do Cartão Escolar -->
            <div class="bg-gradient-to-tr from-emerald-800 to-teal-900 text-white p-5 rounded-2xl shadow-lg border border-emerald-700 text-left relative overflow-hidden">
                <div class="flex items-center justify-between border-b border-white/20 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="ZamEdu" class="h-7 w-auto">
                        <div>
                            <h5 class="text-xs font-extrabold uppercase tracking-tight">CARTÃO DO ESTUDANTE</h5>
                            <span class="text-[9px] text-emerald-200 block">REPÚBLICA DE MOÇAMBIQUE · MINEDH</span>
                        </div>
                    </div>
                    <img src="{{ asset('images/emblem.png') }}" alt="Emblema da República" class="h-8 w-auto">
                </div>

                <div class="flex gap-3 items-center">
                    <div class="w-16 h-16 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center text-2xl font-bold text-white shrink-0">
                        {{ substr($student->first_name, 0, 1) }}
                    </div>
                    <div class="text-xs">
                        <div class="font-extrabold text-sm">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div class="text-emerald-200 text-[10px]">Turma: {{ $class ? $class->name : 'N/D' }}</div>
                        <div class="text-emerald-200 text-[10px]">Nº Aluno: {{ $student->student_number ?? 'EST-'.$student->id }}</div>
                        <div class="text-emerald-200 text-[10px]">Validade: 31/12/{{ $currentYear }}</div>
                    </div>
                </div>

                <div class="mt-3 pt-2 border-t border-white/20 flex items-center justify-between text-[9px] text-emerald-200">
                    <span>{{ $student->school?->name ?? 'Escola Oficial' }}</span>
                    <span class="font-bold text-amber-300"><i class="fas fa-qrcode me-1"></i> VERIFICADO</span>
                </div>
            </div>

            <p class="text-xs text-slate-500 mt-4">
                Apresente este comprovativo digital de estudante para acesso às instalações escolares e requisição de documentos.
            </p>
            <button type="button" onclick="closeStudentCardModal()" class="mt-3 w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                Fechar
            </button>
        </div>
    </div>

    @endif

</div>

@push('scripts')
<script>
function openStudentCardModal() {
    document.getElementById('studentCardModal')?.classList.remove('hidden');
}
function closeStudentCardModal() {
    document.getElementById('studentCardModal')?.classList.add('hidden');
}

async function runExamSimulation() {
    const maf = parseFloat(document.getElementById('sim_maf')?.value || '0');
    const ne = parseFloat(document.getElementById('sim_ne')?.value || '0');
    const acfVal = document.getElementById('sim_acf')?.value;
    const acf = acfVal ? parseFloat(acfVal) : null;

    try {
        const res = await fetch('{{ route('student.simulate-exam') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ maf, ne, acf })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('sim_nfd_display').innerText = data.nfd + ' / 20';
            document.getElementById('sim_result_text').innerText = 
                `Com NE de ${ne.toFixed(1)} (NE Eficaz: ${data.ne_eficaz.toFixed(1)}), a sua Nota Final de Disciplina (NFD) será ${data.nfd} valores (${data.status}).`;
            const box = document.getElementById('sim_result_box');
            if (data.approved) {
                box.className = 'mt-4 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 flex flex-col sm:flex-row items-center justify-between gap-4';
            } else {
                box.className = 'mt-4 p-4 rounded-2xl bg-amber-950/60 border border-amber-500/40 flex flex-col sm:flex-row items-center justify-between gap-4';
            }
        }
    } catch (e) {
        console.error('Erro na simulação:', e);
    }
}
</script>
@endpush
@endsection

