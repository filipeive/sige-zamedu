@extends('layouts.app')

@section('title', 'Gestão de Alunos da Turma')
@section('page-title', 'Gestão de Alunos da Turma')
@section('page-title-icon', 'fas fa-users')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.show', $class->id) }}">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>
@endsection

@section('title', 'Alunos - ' . $class->name)
@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-users text-emerald-600"></i> Alunos da Turma: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Nível: <strong>{{ $class->grade_level_name }}</strong> | Ano Letivo: <strong>{{ $class->school_year }}</strong> | 
                Professor: <strong>{{ $class->teacher ? $class->teacher->first_name . ' ' . $class->teacher->last_name : 'Não atribuído' }}</strong> | 
                Capacidade: <strong>{{ $students->count() }}/{{ $class->max_students }}</strong>
            </p>
        </div>
        <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Turma
        </a>
    </div>

    <!-- Add Student Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-user-plus text-emerald-600"></i> Adicionar Aluno à Turma
        </h3>
        <form action="{{ route('classes.add-student', $class->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            @csrf
            <div class="sm:col-span-6">
                <label for="student_id" class="block text-xs font-bold text-slate-700 mb-1">Selecionar Aluno *</label>
                <select name="student_id" id="student_id" required
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('student_id') border-rose-500 @enderror">
                    <option value="">Selecione um aluno...</option>
                    @foreach ($availableStudents as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                            @if ($student->parent) - Enc: {{ $student->parent->first_name }} @endif
                        </option>
                    @endforeach
                </select>
                @error('student_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-4">
                <label for="monthly_fee" class="block text-xs font-bold text-slate-700 mb-1">Mensalidade (MT) *</label>
                <input type="number" step="0.01" min="0" name="monthly_fee" id="monthly_fee" required
                       value="{{ old('monthly_fee', $class->students->avg('monthly_fee') ?? 0) }}"
                       class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('monthly_fee') border-rose-500 @enderror">
                @error('monthly_fee')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </div>
        </form>
        @if ($availableStudents->isEmpty())
            <div class="mt-3 p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-center justify-between">
                <span><i class="fas fa-info-circle mr-1"></i> Todos os alunos disponíveis já estão matriculados em turmas deste ano letivo.</span>
                <a href="{{ route('students.create') }}" class="font-bold text-blue-900 underline">Cadastrar novo aluno</a>
            </div>
        @endif
    </div>

    <!-- Students Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Alunos Matriculados ({{ $students->count() }})
            </h3>
            <span class="text-[10px] font-bold text-slate-500">
                Capacidade: {{ $students->count() }}/{{ $class->max_students }} ({{ round(($students->count() / max($class->max_students, 1)) * 100, 1) }}%)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Aluno</th>
                        <th class="py-2.5 px-3">Número</th>
                        <th class="py-2.5 px-3">Gênero</th>
                        <th class="py-2.5 px-3">Idade</th>
                        <th class="py-2.5 px-3">Data Matrícula</th>
                        <th class="py-2.5 px-3">Mensalidade</th>
                        <th class="py-2.5 px-3">Enc. Educação</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $enrollment = $student->enrollments->where('class_id', $class->id)->where('status', 'active')->first();
                            $age = $student->birthdate ? $student->birthdate->age : 'N/A';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    @if ($student->passport_photo)
                                        <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->first_name }}" class="w-7 h-7 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                                    @if ($student->has_special_needs)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold" title="Necessidades Especiais"><i class="fas fa-wheelchair"></i></span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 px-3"><code class="text-xs">{{ $student->student_number }}</code></td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $student->gender == 'male' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-pink-100 text-pink-800 border border-pink-200' }}">
                                    <i class="fas {{ $student->gender == 'male' ? 'fa-mars' : 'fa-venus' }} mr-1"></i> {{ $student->gender == 'male' ? 'Masculino' : 'Feminino' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $age }} anos
                                @if ($student->birthdate)
                                    <span class="block text-[10px] text-slate-400">{{ $student->birthdate->format('d/m/Y') }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $enrollment ? $enrollment->enrollment_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-emerald-700">
                                {{ number_format($student->monthly_fee, 2, ',', '.') }} MT
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                @if ($student->parent)
                                    <span class="font-semibold text-slate-700 block">{{ $student->parent->first_name }} {{ $student->parent->last_name }}</span>
                                    @if ($student->parent->phone)<span class="text-[10px] text-slate-400">{{ $student->parent->phone }}</span>@endif
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('students.show', $student->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Perfil">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('classes.remove-student', [$class->id, $student->id]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Remover da Turma" data-confirm="Tem certeza que deseja remover {{ $student->first_name }} da turma {{ $class->name }}?">
                                            <i class="fas fa-user-minus text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">
                                <i class="fas fa-users text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                                <p class="text-[10px] text-slate-400">Use o formulário acima para adicionar alunos à turma.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Class Stats & Summary -->
    @if ($students->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-emerald-600"></i> Distribuição por Gênero
                </h3>
                @php $total = $students->count(); @endphp
                <div class="max-w-[250px] mx-auto">
                    <canvas id="genderChart" width="200" height="200"></canvas>
                </div>
                <div class="mt-3 flex justify-center items-center gap-3">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                        <i class="fas fa-mars mr-1"></i> {{ $maleCount }} ({{ $total > 0 ? round(($maleCount / $total) * 100, 1) : 0 }}%)
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-800 border border-pink-200">
                        <i class="fas fa-venus mr-1"></i> {{ $femaleCount }} ({{ $total > 0 ? round(($femaleCount / $total) * 100, 1) : 0 }}%)
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-emerald-600"></i> Resumo da Turma
                </h3>
                <div class="divide-y divide-slate-100 text-xs">
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-slate-600">Total de Alunos</span>
                        <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-full">{{ $total }}</span>
                    </div>
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-slate-600">Vagas Disponíveis</span>
                        <span class="font-bold {{ $class->max_students - $total > 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }} px-2 py-0.5 rounded-full">
                            {{ $class->max_students - $total }}
                        </span>
                    </div>
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-slate-600">Alunos com Necessidades Especiais</span>
                        <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">{{ $students->where('has_special_needs', true)->count() }}</span>
                    </div>
                    <div class="py-2 flex justify-between items-center">
                        <span class="text-slate-600">Média de Idade</span>
                        <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                            @php
                                $ages = $students->filter(fn($s) => $s->birthdate)->map(fn($s) => $s->birthdate->age);
                                $averageAge = $ages->isNotEmpty() ? round($ages->avg(), 1) : 0;
                            @endphp
                            {{ $averageAge }} anos
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Birthdays this month -->
    @php
        $birthdaysThisMonth = $students->filter(function ($student) {
            return $student->birthdate && $student->birthdate->format('m') == now()->format('m');
        })->sortBy(function ($student) {
            return $student->birthdate->format('d');
        });
    @endphp

    @if ($birthdaysThisMonth->count() > 0)
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-birthday-cake text-amber-500"></i> Aniversariantes do Mês
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ($birthdaysThisMonth as $student)
                    <div class="p-3 rounded-xl border border-amber-200 bg-amber-50/50 text-center">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-birthday-cake text-sm"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-0.5">{{ $student->first_name }} {{ $student->last_name }}</h4>
                        <p class="text-[10px] text-slate-500 mb-2">
                            <i class="fas fa-calendar mr-1"></i> {{ $student->birthdate->format('d/m') }} ({{ $student->birthdate->age + 1 }} anos)
                        </p>
                        <a href="{{ route('students.show', $student->id) }}" class="no-underline inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-bold shadow-2xs transition">
                            <i class="fas fa-user"></i> Ver Perfil
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('genderChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Masculino', 'Feminino'],
                        datasets: [{
                            data: [{{ $maleCount }}, {{ $femaleCount }}],
                            backgroundColor: ['#3b82f6', '#ec4899'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
@endpush
@endsection
