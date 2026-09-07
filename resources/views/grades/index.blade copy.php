@extends('layouts.app')

@section('title', 'Gestão de Notas & Pauta da Turma')
@section('page-title', 'Pautas & Lançamento de Notas')
@section('page-title-icon', 'fas fa-medal')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Gestão de Notas</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-medal text-emerald-600"></i> Pautas & Lançamento de Notas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Avaliações contínuas (ACS), exames (ACP) e médias finais (MT).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('grades.dashboard') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            @if($selectedClass)
                <a href="{{ route('pautas.trimestral', $selectedClass->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                    <i class="fas fa-file-pdf text-rose-500"></i> Pauta (PDF)
                </a>
                @can('create_grades')
                    <a href="{{ route('grades.batch-create', ['class_id' => $selectedClass->id, 'term' => $term]) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                        <i class="fas fa-edit"></i> Lançar Notas
                    </a>
                @endcan
            @endif
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Notas Registadas (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-medal"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Notas Registadas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Grade::where('year', $currentYear)->count() }}</span>
            </div>
        </div>

        <!-- Média Geral (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Geral</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format(\App\Models\Grade::where('year', $currentYear)->avg('grade') ?? 0, 1) }} / 20</span>
            </div>
        </div>

        <!-- Turmas (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ count($classes) }}</span>
            </div>
        </div>

        <!-- Alunos na Turma (Purple Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-purple-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos na Turma</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ count($students) }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('grades.index') }}" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div>
                <select name="level" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="if(this.form.class_id) this.form.class_id.value = ''; this.form.submit();">
                    <option value="all" {{ $level === 'all' ? 'selected' : '' }}>Todos os Níveis</option>
                    <option value="preschool" {{ $level === 'preschool' ? 'selected' : '' }}>Pré-Escolar & Infantil</option>
                    <option value="primary" {{ $level === 'primary' ? 'selected' : '' }}>Ensino Primário (1ª - 6ª Classe)</option>
                    <option value="secondary" {{ $level === 'secondary' ? 'selected' : '' }}>Ensino Secundário (7ª - 12ª Classe)</option>
                </select>
            </div>

            <div>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @forelse($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClass?->id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->grade_level_name }})
                        </option>
                    @empty
                        <option value="">Nenhuma turma disponível</option>
                    @endforelse
                </select>
            </div>

            <div>
                <select name="term" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="1" {{ $term == 1 ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2" {{ $term == 2 ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3" {{ $term == 3 ? 'selected' : '' }}>3º Trimestre</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Carregar Pauta
                </button>
            </div>
        </form>
    </div>

    <!-- 4. Pauta Table Container -->
    @if($selectedClass)
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Pauta Trimestral: {{ $selectedClass->name }} ({{ $term }}º Trimestre)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                            <th rowspan="2" class="p-2.5 ps-4 align-middle border-r border-slate-800" style="min-width: 200px;">Estudante</th>
                            @foreach($subjects as $sub)
                                <th colspan="3" class="p-1.5 text-center border-r border-b border-slate-800">
                                    {{ $sub->code ?? substr($sub->name, 0, 8) }}
                                </th>
                            @endforeach
                            <th rowspan="2" class="p-2.5 text-center align-middle bg-emerald-900 text-amber-300 font-extrabold">Média Geral</th>
                        </tr>
                        <tr class="bg-slate-800 text-slate-300 font-semibold text-[9px]">
                            @foreach($subjects as $sub)
                                <th class="p-1 text-center border-r border-slate-700">MACS</th>
                                <th class="p-1 text-center border-r border-slate-700">ACP</th>
                                <th class="p-1 text-center border-r border-slate-700 bg-slate-900 text-amber-400 font-bold">MT</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            @php
                                $studentCalculated = $calculatedMatrix[$student->id] ?? [];
                                $allMts = array_filter(array_column($studentCalculated, 'mt'), fn($v) => $v !== null);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-2.5 ps-4 border-r border-slate-100">
                                    <a href="{{ route('students.show', $student->id) }}" class="no-underline hover:no-underline font-bold text-slate-800 hover:text-emerald-700 transition">
                                        {{ $student->full_name }}
                                    </a>
                                </td>

                                @foreach($subjects as $sub)
                                    @php
                                        $cell = $studentCalculated[$sub->id] ?? ['macs' => null, 'acp' => null, 'mt' => null];
                                        $acsAvg = $cell['macs'];
                                        $acp = $cell['acp'];
                                        $mt = $cell['mt'];
                                    @endphp
                                    <td class="p-1.5 text-center text-slate-500 border-r border-slate-100">
                                        {{ $acsAvg !== null ? number_format($acsAvg, 1) : '-' }}
                                    </td>
                                    <td class="p-1.5 text-center text-slate-500 border-r border-slate-100">
                                        {{ $acp !== null ? number_format($acp, 1) : '-' }}
                                    </td>
                                    <td class="p-1.5 text-center border-r border-slate-100 font-extrabold {{ $mt !== null ? ($mt >= 10 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700') : 'text-slate-400' }}">
                                        {{ $mt !== null ? number_format($mt, 1) : '-' }}
                                    </td>
                                @endforeach

                                <td class="p-2 text-center font-black bg-slate-50 text-slate-900">
                                    @php
                                        $totalSubjects = count($subjects);
                                        $gradedCount = count($allMts);
                                        $avgMethod = setting('average_method', 'all_subjects');
                                        if ($avgMethod === 'all_subjects') {
                                            $overall = $gradedCount > 0 ? round(array_sum($allMts) / $totalSubjects, 1) : 0;
                                        } else {
                                            $overall = $gradedCount > 0 ? round(array_sum($allMts) / $gradedCount, 1) : 0;
                                        }
                                    @endphp
                                    <span class="{{ $overall >= 10 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $overall > 0 ? number_format($overall, 1) : '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($subjects) * 3 + 2 }}" class="p-6 text-center text-slate-400">
                                    <i class="fas fa-users-slash text-2xl text-slate-300 mb-2"></i>
                                    <p class="text-xs font-semibold text-slate-600">Nenhum aluno nesta turma.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl p-8 text-center border border-slate-200 shadow-2xs">
            <i class="fas fa-chalkboard text-3xl text-slate-300 mb-2"></i>
            <p class="text-xs font-semibold text-slate-600">Selecione uma turma para visualizar a pauta.</p>
        </div>
    @endif

</div>
@endsection