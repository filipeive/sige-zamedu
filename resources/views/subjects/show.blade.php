@extends('layouts.app')

@section('title', $subject->name)
@section('page-title', $subject->name)
@section('page-title-icon', 'fas fa-book')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Disciplinas</a></li>
    <li class="breadcrumb-item active">{{ $subject->name }}</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-book text-emerald-600"></i> {{ $subject->name }} ({{ $subject->code }})
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Detalhes da disciplina, turmas e professores associados.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit_subjects')
                <a href="{{ route('subjects.edit', $subject->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-edit"></i> Editar Disciplina
                </a>
            @endcan
            <a href="{{ route('subjects.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sidebar Info & Stats (1/3) -->
        <div class="space-y-4">
            <!-- Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i> Informações da Disciplina
                    </h3>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Nome:</span>
                        <span class="text-slate-800 font-semibold">{{ $subject->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Código:</span>
                        <code class="text-xs font-bold">{{ $subject->code }}</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Nível:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $gradeLevels[$subject->grade_level] ?? $subject->grade_level }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Horas Semanais:</span>
                        <span class="text-slate-800 font-semibold">{{ $subject->weekly_hours }}h</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Status:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $subject->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $subject->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    @if($subject->description)
                        <div class="pt-2 border-t border-slate-100">
                            <span class="font-bold text-slate-700 block mb-1">Descrição:</span>
                            <p class="text-slate-500 text-[11px] leading-relaxed">{{ $subject->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-bar text-emerald-600"></i> Estatísticas
                    </h3>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Turmas:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">{{ $stats['total_classes'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Notas Registradas:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $stats['total_grades'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Média Geral:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stats['average_grade'] >= 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ number_format($stats['average_grade'], 1) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Professores:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $stats['active_teachers'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Quick Actions Bar -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <a href="{{ route('subjects.classes', $subject->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 transition">
                        <i class="fas fa-chalkboard text-lg mb-1"></i>
                        <span class="text-xs font-bold">Turmas</span>
                    </a>
                    <a href="{{ route('subjects.grades', $subject->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 transition">
                        <i class="fas fa-medal text-lg mb-1"></i>
                        <span class="text-xs font-bold">Notas</span>
                    </a>
                    <a href="#" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 transition">
                        <i class="fas fa-chart-line text-lg mb-1"></i>
                        <span class="text-xs font-bold">Relatórios</span>
                    </a>
                    <a href="#" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 transition">
                        <i class="fas fa-print text-lg mb-1"></i>
                        <span class="text-xs font-bold">Exportar</span>
                    </a>
                </div>
            </div>

            <!-- Classes Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chalkboard text-emerald-600"></i> Turmas ({{ $classesUsingSubject->count() }})
                    </h3>
                    <a href="{{ route('subjects.classes', $subject->id) }}" class="no-underline text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Ver Todas <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                    </a>
                </div>
                <div>
                    @if($classesUsingSubject->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                        <th class="py-2.5 px-4">Turma</th>
                                        <th class="py-2.5 px-3">Nível</th>
                                        <th class="py-2.5 px-3">Professor</th>
                                        <th class="py-2.5 px-3">Ano Letivo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($classesUsingSubject->take(5) as $class)
                                        @php
                                            $classSubject = $subject->classSubjects->where('class_id', $class->id)->first();
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-4 font-bold text-slate-800">{{ $class->name }}</td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $class->grade_level_name }}</span>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                @if($classSubject && $classSubject->teacher)
                                                    {{ $classSubject->teacher->first_name }} {{ $classSubject->teacher->last_name }}
                                                @else
                                                    <span class="text-slate-400">Não atribuído</span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">{{ $class->school_year }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400">
                            <i class="fas fa-chalkboard text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-semibold text-slate-600 mb-3">Esta disciplina não está associada a nenhuma turma.</p>
                            <a href="{{ route('subjects.classes', $subject->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-link"></i> Associar a Turmas
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Teachers List -->
            @if($teachersTeachingSubject->count() > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-users text-emerald-600"></i> Professores ({{ $teachersTeachingSubject->count() }})
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($teachersTeachingSubject->take(6) as $teacher)
                        <div class="p-3 rounded-lg border border-slate-200/80 bg-slate-50/50 text-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-700 text-white font-bold text-xs flex items-center justify-center mx-auto mb-2">
                                {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                            </div>
                            <h4 class="text-xs font-bold text-slate-800">{{ $teacher->first_name }} {{ $teacher->last_name }}</h4>
                            <p class="text-[10px] text-slate-400 truncate">{{ $teacher->email }}</p>
                            @if($teacher->phone)<p class="text-[10px] text-slate-400">{{ $teacher->phone }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Grades -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-medal text-emerald-600"></i> Notas Recentes
                    </h3>
                    <a href="{{ route('subjects.grades', $subject->id) }}" class="no-underline text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Ver Todas <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                    </a>
                </div>
                <div>
                    @if($subject->grades->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                        <th class="py-2.5 px-4">Aluno</th>
                                        <th class="py-2.5 px-3">Nota</th>
                                        <th class="py-2.5 px-3">Tipo</th>
                                        <th class="py-2.5 px-3">Data</th>
                                        <th class="py-2.5 px-3">Professor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($subject->grades->take(5) as $grade)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-4 font-bold text-slate-800">{{ $grade->student->first_name }} {{ $grade->student->last_name }}</td>
                                            <td class="py-2.5 px-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $grade->grade >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                    {{ number_format($grade->grade, 1) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                @switch($grade->assessment_type)
                                                    @case('continuous') Contínua @break
                                                    @case('test') Teste @break
                                                    @case('exam') Exame @break
                                                    @default {{ $grade->assessment_type }}
                                                @endswitch
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">{{ $grade->date_recorded->format('d/m/Y') }}</td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                {{ $grade->teacher ? $grade->teacher->first_name . ' ' . $grade->teacher->last_name : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-400">
                            <i class="fas fa-medal text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-semibold text-slate-600">Nenhuma nota registrada para esta disciplina.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
