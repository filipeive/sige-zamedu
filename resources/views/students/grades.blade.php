@extends('layouts.app')

@section('title', 'Boletim & Aproveitamento')
@section('page-title', 'Boletim & Aproveitamento')
@section('page-title-icon', 'fas fa-graduation-cap')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="no-underline">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}" class="no-underline">{{ $student->full_name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Notas</li>
@endsection

@section('title', 'Pautas & Notas — ' . $student->full_name)
@section('content')
<div class="max-w-full mx-auto space-y-4" x-data="{ shareMailOpen: false }">

    <!-- 1. Header Banner Card -->
    <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 rounded-2xl p-5 text-white shadow-md relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 backdrop-blur-xs text-[10px] font-extrabold text-emerald-200 border border-white/20 uppercase tracking-wider">
                <i class="fas fa-file-invoice"></i> Boletim Individual de Rendimento
            </span>
            <h1 class="text-xl font-black tracking-tight text-white flex items-center gap-2">
                {{ $student->full_name }}
            </h1>
            <p class="text-xs text-emerald-100/90 font-medium flex flex-wrap items-center gap-2">
                <span><strong>Nº Estudante:</strong> <code class="bg-emerald-950/40 px-1.5 py-0.5 rounded text-white font-mono">{{ $student->student_number ?? 'N/A' }}</code></span>
                <span>•</span>
                <span><strong>Turma:</strong> <span class="text-white font-bold">{{ $currentEnrollment->class->name ?? 'N/A' }}</span></span>
                <span>•</span>
                <span><strong>Ano Lectivo:</strong> <span class="text-white font-bold">{{ $currentEnrollment->school_year ?? date('Y') }}</span></span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('students.show', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition backdrop-blur-xs border border-white/20">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>
    </div>

    <!-- 2. Term Selection Navigation & Action Toolbar -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Term Tabs Group -->
        <div class="inline-flex rounded-lg bg-slate-100 p-1 border border-slate-200 self-start sm:self-auto overflow-x-auto">
            <button type="button" class="term-btn active px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-700 hover:text-slate-900 cursor-pointer" data-term="1" onclick="switchTerm('1')">
                1º Trimestre
            </button>
            <button type="button" class="term-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-700 hover:text-slate-900 cursor-pointer" data-term="2" onclick="switchTerm('2')">
                2º Trimestre
            </button>
            <button type="button" class="term-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-700 hover:text-slate-900 cursor-pointer" data-term="3" onclick="switchTerm('3')">
                3º Trimestre
            </button>
            <button type="button" class="term-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-700 hover:text-slate-900 cursor-pointer" data-term="annual" onclick="switchTerm('annual')">
                Ficha Anual
            </button>
        </div>

        <!-- Export & Share Actions -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('students.grades.pdf', ['student' => $student->id, 'term' => 1]) }}" id="pdfDownloadBtn" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-file-pdf"></i> Extrair Boletim PDF
            </a>

            <button type="button" @click="shareMailOpen = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition cursor-pointer">
                <i class="fas fa-envelope text-slate-500"></i> Enviar por E-mail
            </button>

            <a href="#" id="whatsappShareBtn" target="_blank" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-xs font-bold text-emerald-800 transition shadow-2xs">
                <i class="fab fa-whatsapp text-emerald-600"></i> Partilhar no WhatsApp
            </a>
        </div>
    </div>

    <!-- 3. Grades Matrix Tables Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        
        <!-- ── 1º TRIMESTRE TABLE ── -->
        <div class="term-table-wrapper" id="termTable-1">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Disciplina</th>
                            <th class="py-3 px-3 text-center">ACS 1</th>
                            <th class="py-3 px-3 text-center">ACS 2</th>
                            <th class="py-3 px-3 text-center">ACS 3</th>
                            <th class="py-3 px-3 text-center">Média ACS (MACS)</th>
                            <th class="py-3 px-3 text-center">ACP</th>
                            <th class="py-3 px-3 text-center">Média Trimestre (MT)</th>
                            <th class="py-3 px-4 text-center">Aproveitamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($matrix as $subjectId => $data)
                            @php $tData = $data['terms'][1]; @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $data['subject']->name }}</div>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $data['subject']->code }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $tData['mt'] >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                            {{ number_format($tData['mt'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="font-bold text-xs {{ $tData['mt'] >= 10 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400 font-semibold">Não há disciplinas associadas a esta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── 2º TRIMESTRE TABLE ── -->
        <div class="term-table-wrapper" id="termTable-2" style="display:none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Disciplina</th>
                            <th class="py-3 px-3 text-center">ACS 1</th>
                            <th class="py-3 px-3 text-center">ACS 2</th>
                            <th class="py-3 px-3 text-center">ACS 3</th>
                            <th class="py-3 px-3 text-center">Média ACS (MACS)</th>
                            <th class="py-3 px-3 text-center">ACP</th>
                            <th class="py-3 px-3 text-center">Média Trimestre (MT)</th>
                            <th class="py-3 px-4 text-center">Aproveitamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($matrix as $subjectId => $data)
                            @php $tData = $data['terms'][2]; @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $data['subject']->name }}</div>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $data['subject']->code }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $tData['mt'] >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                            {{ number_format($tData['mt'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="font-bold text-xs {{ $tData['mt'] >= 10 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400 font-semibold">Não há disciplinas associadas a esta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── 3º TRIMESTRE TABLE ── -->
        <div class="term-table-wrapper" id="termTable-3" style="display:none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Disciplina</th>
                            <th class="py-3 px-3 text-center">ACS 1</th>
                            <th class="py-3 px-3 text-center">ACS 2</th>
                            <th class="py-3 px-3 text-center">ACS 3</th>
                            <th class="py-3 px-3 text-center">Média ACS (MACS)</th>
                            <th class="py-3 px-3 text-center">ACP</th>
                            <th class="py-3 px-3 text-center">Média Trimestre (MT)</th>
                            <th class="py-3 px-4 text-center">Aproveitamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($matrix as $subjectId => $data)
                            @php $tData = $data['terms'][3]; @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $data['subject']->name }}</div>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $data['subject']->code }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $tData['mt'] >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                            {{ number_format($tData['mt'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($tData['mt'] !== null)
                                        <span class="font-bold text-xs {{ $tData['mt'] >= 10 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            {{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400 font-semibold">Não há disciplinas associadas a esta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── ANUAL TABLE ── -->
        <div class="term-table-wrapper" id="termTable-annual" style="display:none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-600 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Disciplina</th>
                            <th class="py-3 px-3 text-center">MT1</th>
                            <th class="py-3 px-3 text-center">MT2</th>
                            <th class="py-3 px-3 text-center">MT3</th>
                            <th class="py-3 px-3 text-center">Média Anual (MF)</th>
                            <th class="py-3 px-3 text-center">Exame</th>
                            <th class="py-3 px-3 text-center">Média Geral (MGD)</th>
                            <th class="py-3 px-4 text-center">Situação Final</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($matrix as $subjectId => $data)
                            @php
                                $annual = $data['annual'];
                                $isPositive = $annual['mfd'] !== null && $annual['mfd'] >= 10;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $data['subject']->name }}</div>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $data['subject']->code }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $annual['mt1'] !== null ? number_format($annual['mt1'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $annual['mt2'] !== null ? number_format($annual['mt2'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $annual['mt3'] !== null ? number_format($annual['mt3'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $annual['mf'] !== null ? number_format($annual['mf'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600">{{ $annual['exam'] !== null ? number_format($annual['exam'], 1) : '-' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($annual['mfd'] !== null)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold {{ $isPositive ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                            {{ number_format($annual['mfd'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($annual['status'] === 'Aprovado')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Aprovado</span>
                                    @elseif($annual['status'] === 'Reprovado')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Reprovado</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Em Curso</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400 font-semibold">Não há disciplinas associadas a esta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Teleported Share via Mail Modal -->
    <template x-teleport="body">
        <div x-show="shareMailOpen" x-transition.opacity
             style="position: fixed; inset: 0; z-index: 999999 !important; display: none;"
             class="flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100" @click.outside="shareMailOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-envelope-open-text text-emerald-600"></i> Enviar Boletim por E-mail
                    </h3>
                    <button type="button" @click="shareMailOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <form action="{{ route('students.grades.share-email', $student) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="term" id="modalTermInput" value="1">
                    
                    <p class="text-xs text-slate-500">O boletim oficial será gerado em formato PDF e anexado diretamente ao e-mail enviado ao encarregado.</p>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">E-mail do Encarregado de Educação *</label>
                        <input type="email" name="email" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" value="{{ $student->parent->email ?? '' }}" placeholder="exemplo@email.com" required>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="shareMailOpen = false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition flex items-center gap-1.5">
                            <i class="fas fa-paper-plane"></i> Enviar Boletim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    let currentActiveTerm = '1';

    function switchTerm(term) {
        currentActiveTerm = term;

        // Update active styling on term buttons
        document.querySelectorAll('.term-btn').forEach(btn => {
            const isActive = btn.dataset.term === term;
            if (isActive) {
                btn.classList.add('bg-emerald-700', 'text-white', 'shadow-2xs');
                btn.classList.remove('text-slate-700');
            } else {
                btn.classList.remove('bg-emerald-700', 'text-white', 'shadow-2xs');
                btn.classList.add('text-slate-700');
            }
        });

        // Toggle visibility of table wrappers
        document.querySelectorAll('.term-table-wrapper').forEach(wrapper => {
            wrapper.style.display = wrapper.id === `termTable-${term}` ? 'block' : 'none';
        });

        // Update PDF export link with current selected term
        const pdfBtn = document.getElementById('pdfDownloadBtn');
        if (pdfBtn) {
            let url = new URL(pdfBtn.href);
            url.searchParams.set('term', term);
            pdfBtn.href = url.toString();
        }

        // Update modal form hidden input
        const modalTermInput = document.getElementById('modalTermInput');
        if (modalTermInput) {
            modalTermInput.value = term;
        }

        updateWhatsAppShareLink();
    }

    function updateWhatsAppShareLink() {
        const shareBtn = document.getElementById('whatsappShareBtn');
        if (!shareBtn) return;

        const parentPhone = "{{ $student->parent->phone ?? '' }}".replace(/\s+/g, '');
        const studentName = "{{ $student->full_name }}";
        const termLabel = currentActiveTerm === 'annual' ? 'Ano Lectivo' : `${currentActiveTerm}º Trimestre`;
        
        let message = `Olá! Segue o aproveitamento escolar de *${studentName}* relativo ao *${termLabel}*:\n\n`;

        // Parse corresponding visible table rows to construct WhatsApp message text
        const activeTable = document.querySelector(`#termTable-${currentActiveTerm} tbody`);
        if (activeTable) {
            const rows = activeTable.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    const subjectNameEl = cells[0].querySelector('.font-bold');
                    const subjectName = subjectNameEl ? subjectNameEl.textContent.trim() : cells[0].textContent.trim();
                    let grade = '-';
                    if (cells.length >= 7) {
                        grade = cells[6].textContent.trim(); // Media Trimestre (MT) ou Media Geral (MGD)
                    }
                    message += `• *${subjectName}*: Média ${grade}\n`;
                }
            });
        }

        message += `\nPara ver detalhes completos, consulte o portal do encarregado.`;
        
        shareBtn.href = `https://api.whatsapp.com/send?phone=${parentPhone}&text=${encodeURIComponent(message)}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        switchTerm('1');
    });
</script>
@endpush
