@extends('layouts.app')

@section('title', 'Pautas - Exame')
@section('page-title', 'Pautas - Exame')
@section('page-title-icon', 'fas fa-table')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Pautas</li>
@endsection

@section('title', 'Pauta de Exames com Júris - ' . $class->name)

@section('content')
<div class="space-y-5">
    {{-- 1. Header com Taxonomia MINEDH Completa --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-layer-group me-1"></i> Nível: {{ $class->education_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="fas fa-route me-1"></i> {{ $class->cycle_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $class->grade_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-calendar-alt me-1"></i> Ano Lectivo {{ $year }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                    <i class="fas fa-file-signature text-rose-600 me-1"></i> Exame Nacional &amp; Júri (Lei 18/2018)
                </span>
            </div>

            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-file-signature text-rose-600"></i> Pauta de Exames com Júris: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Apuramento oficial de NE, Recorrência (ACF), NE Eficaz e Nota Final da Disciplina (NFD) com Júri de Exame MINEDH.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/80 text-xs font-bold transition cursor-pointer" data-bs-toggle="modal" data-bs-target="#juryConfigModal">
                <i class="fas fa-user-tie text-rose-600"></i> Atribuir / Editar Júri
            </button>

            <div class="inline-flex rounded-lg bg-slate-100 p-1 border border-slate-200">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-slate-700 hover:text-emerald-700">
                    <i class="fas fa-list-alt me-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-slate-700 hover:text-emerald-700">
                    <i class="fas fa-calendar-alt me-1"></i> Anual
                </a>
                <a href="{{ route('pautas.final', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-slate-700 hover:text-emerald-700">
                    <i class="fas fa-graduation-cap me-1"></i> Final
                </a>
                <a href="{{ route('pautas.exame', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-extrabold bg-rose-700 text-white shadow-2xs">
                    <i class="fas fa-file-signature me-1"></i> Exames &amp; Júri
                </a>
            </div>

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'final', 'year' => $year]) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-700 hover:bg-rose-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- 2. Bloco de Informação do Júri --}}
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-2xl border border-slate-200 p-4 shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-slate-200">
            <div class="pt-2 md:pt-0 pr-2">
                <span class="text-3xs uppercase font-extrabold tracking-wider text-slate-400 block mb-1">Júri de Exame</span>
                <h4 class="font-black text-sm text-rose-700 mb-0">{{ $juryCode ?: "Júri Nº {$class->name}" }}</h4>
            </div>
            <div class="pt-2 md:pt-0 md:pl-4 pr-2">
                <span class="text-3xs uppercase font-extrabold tracking-wider text-slate-400 block mb-1">Presidente do Júri</span>
                <strong class="text-xs font-black text-slate-800 block">{{ $juryPresident ?: 'Não atribuído' }}</strong>
            </div>
            <div class="pt-2 md:pt-0 md:pl-4 pr-2">
                <span class="text-3xs uppercase font-extrabold tracking-wider text-slate-400 block mb-1">1º Vogal / Examinador</span>
                <strong class="text-xs font-black text-slate-800 block">{{ $juryExaminer1 ?: 'Não atribuído' }}</strong>
            </div>
            <div class="pt-2 md:pt-0 md:pl-4">
                <span class="text-3xs uppercase font-extrabold tracking-wider text-slate-400 block mb-1">2º Vogal / Co-Examinador</span>
                <strong class="text-xs font-black text-slate-800 block">{{ $juryExaminer2 ?: 'Não atribuído' }}</strong>
            </div>
        </div>
    </div>

    {{-- 3. Filtros de Pesquisa --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <form method="GET" action="{{ route('pautas.exame', $class->id) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-users text-rose-600 me-1"></i> Turma
                </label>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" onchange="this.form.submit()">
                    @foreach($availableClasses as $c)
                        <option value="{{ $c->id }}" @selected($c->id === $class->id)>
                            {{ $c->name }} ({{ $c->grade_level_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-book text-rose-600 me-1"></i> Disciplina
                </label>
                <select name="subject_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" onchange="this.form.submit()">
                    <option value="">{{ $isTeacher ? 'Todas (que lecciono)' : 'Todas as Disciplinas' }}</option>
                    @foreach($availableSubjects as $s)
                        <option value="{{ $s->id }}" @selected($selectedSubjectId === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-calendar text-rose-600 me-1"></i> Ano Lectivo
                </label>
                <select name="year" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" onchange="this.form.submit()">
                    <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026 (Ano Letivo Atual)</option>
                    <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025 (Histórico)</option>
                </select>
            </div>
        </form>
    </div>

    {{-- 4. Tabela Matriz de Exame com Júri --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-center border-collapse">
                <thead class="bg-slate-800 text-white font-black uppercase tracking-wider">
                    <tr>
                        <th rowspan="2" class="py-3 px-3 text-center border-r border-slate-700 w-12 bg-slate-900">Nº</th>
                        <th rowspan="2" class="py-3 px-4 text-left border-r border-slate-700 bg-slate-900 min-w-[200px]">Nome Completo do Aluno</th>
                        <th rowspan="2" class="py-3 px-2 text-center border-r border-slate-700 w-12 bg-slate-900">Sexo</th>
                        @foreach($subjects as $subject)
                            <th colspan="5" class="py-2 px-2 text-center border-r border-slate-700 bg-slate-900 border-b border-slate-700 text-rose-300 font-black min-w-[250px]">
                                {{ $subject->name }}
                            </th>
                        @endforeach
                        <th rowspan="2" class="py-3 px-3 text-center bg-rose-900 text-white border-r border-rose-950 min-w-[140px]">Resultado do Júri</th>
                    </tr>
                    <tr class="bg-slate-700 text-slate-200 text-3xs uppercase tracking-wider">
                        @foreach($subjects as $subject)
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600" title="Média Anual de Frequência">MAF</th>
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600" title="Nota de Exame Época Normal">NE</th>
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600" title="Nota Recorrência / Média Júri">ACF</th>
                            <th class="py-1.5 px-2 bg-rose-950 text-rose-200 font-bold border-r border-slate-600" title="NE Eficaz = max(NE, ACF)">NE_E</th>
                            <th class="py-1.5 px-2 bg-emerald-900 text-emerald-200 font-black border-r border-slate-600" title="Nota Final da Disciplina = round((MAF + NE_Eficaz) / 2)">NFD</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($matrix as $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-center font-mono text-slate-500 border-r border-slate-100">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 text-left font-black text-slate-900 border-r border-slate-100">
                                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
                                <div class="text-slate-400 text-2xs font-mono font-normal">N.º {{ $row['student']->student_number ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3 px-2 text-center uppercase font-bold text-slate-600 border-r border-slate-100">{{ substr($row['student']->gender ?? 'M', 0, 1) }}</td>
                            @foreach($subjects as $subject)
                                @php 
                                    $subData = $row['subjects'][$subject->id] ?? []; 
                                    $nfd = $subData['mfd'] ?? '-';
                                    $nfdVal = is_numeric($nfd) ? (float) $nfd : null;
                                @endphp
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['mf'] ?? '-' }}</td>
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['exam'] ?? '-' }}</td>
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['acf'] ?? '-' }}</td>
                                <td class="py-3 px-2 font-bold text-rose-700 bg-rose-50/50 border-r border-slate-100">{{ $subData['ne_effective'] ?? '-' }}</td>
                                <td class="py-3 px-2 font-black border-r border-slate-100 {{ $nfdVal !== null ? ($nfdVal >= 9.5 ? 'text-emerald-700 bg-emerald-50/80' : 'text-rose-700 bg-rose-50/80') : '' }}">
                                    {{ $nfd }}
                                </td>
                            @endforeach
                            <td class="py-3 px-3 text-center">
                                @if($row['final_status'] === 'Aprovado')
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">Aprovado</span>
                                @elseif($row['final_status'] === 'Reprovado')
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-rose-100 text-rose-800 border border-rose-200">Reprovado</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-amber-100 text-amber-800 border border-amber-200">{{ $row['final_status'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + (count($subjects) * 5) }}" class="py-10 text-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                <span>Nenhuma avaliação ou nota de exame registada para esta turma.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. Termo de Homologação e Assinaturas do Júri + DAP e DAE --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs space-y-4">
        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3 flex items-center gap-2">
            <i class="fas fa-signature text-rose-600"></i> Termo de Homologação e Assinaturas do Júri de Exame
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center pt-2">
            <div class="space-y-2">
                <div class="border-b-2 border-slate-900 pb-1 w-4/5 mx-auto">
                    <p class="font-black text-slate-900 text-xs mb-0">{{ $juryPresident ?: '________________________' }}</p>
                </div>
                <span class="text-2xs font-extrabold uppercase text-slate-500 block">Presidente do Júri</span>
            </div>
            <div class="space-y-2">
                <div class="border-b-2 border-slate-900 pb-1 w-4/5 mx-auto">
                    <p class="font-black text-slate-900 text-xs mb-0">{{ $juryExaminer1 ?: '________________________' }}</p>
                </div>
                <span class="text-2xs font-extrabold uppercase text-slate-500 block">1º Vogal (Examinador)</span>
            </div>
            <div class="space-y-2">
                <div class="border-b-2 border-slate-900 pb-1 w-4/5 mx-auto">
                    <p class="font-black text-slate-900 text-xs mb-0">{{ $juryExaminer2 ?: '________________________' }}</p>
                </div>
                <span class="text-2xs font-extrabold uppercase text-slate-500 block">2º Vogal (Co-Examinador)</span>
            </div>
        </div>
    </div>

    {{-- Assinaturas Oficiais MINEDH (DAP & DAE) --}}
    <x-official-signatures :year="$year" />
</div>

<!-- Modal de Atribuição do Júri -->
<div class="modal fade" id="juryConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <div class="px-5 py-4 bg-rose-700 text-white flex items-center justify-between">
                <h3 class="text-sm font-black text-white tracking-tight flex items-center gap-2 mb-0">
                    <i class="fas fa-user-tie"></i> Atribuir / Editar Júri de Exame
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('pautas.exame.jury', $class->id) }}">
                @csrf
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Código / Identificação do Júri</label>
                        <input type="text" name="jury_code" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-rose-500 focus:ring-rose-500" value="{{ $juryCode }}" placeholder="Ex: Júri Nº 12 - 12ª Classe A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Presidente do Júri</label>
                        <input type="text" name="jury_president" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-rose-500 focus:ring-rose-500" value="{{ $juryPresident }}" placeholder="Nome do Presidente do Júri">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">1º Vogal (Examinador)</label>
                        <input type="text" name="jury_examiner1" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-rose-500 focus:ring-rose-500" value="{{ $juryExaminer1 }}" placeholder="Nome do 1º Vogal">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">2º Vogal (Co-Examinador)</label>
                        <input type="text" name="jury_examiner2" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-rose-500 focus:ring-rose-500" value="{{ $juryExaminer2 }}" placeholder="Nome do 2º Vogal">
                    </div>
                </div>
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-700 hover:bg-rose-800 text-white text-xs font-bold shadow-2xs">
                        <i class="fas fa-save me-1"></i> Guardar Atribuição
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
