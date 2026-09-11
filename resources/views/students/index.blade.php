@extends('layouts.app')

@section('title', 'Gestão de Alunos')
@section('page-title', 'Gestão de Alunos')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-graduate text-emerald-600"></i> Directório de Alunos
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão integral de estudantes, encarregados de educação e matrículas.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('manage_enrollments')
                <a href="{{ route('admin.enrollments.renewals') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                    <i class="fas fa-sync-alt text-amber-500"></i> Renovações
                </a>
            @endcan
            @can('create_students')
                <a href="{{ route('students.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Novo Aluno
                </a>
            @endcan
        </div>
    </div>

    <!-- Banner Informativo para Super Admin & Usuários Territoriais -->
    @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
    <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-full bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider">Modo Super Admin</span>
            <span class="font-bold text-slate-700">Visualização Multi-Escola Ativa. Seleccione uma escola no filtro para restringir a consulta.</span>
        </div>
        @if(request()->filled('school_id'))
            @php $selectedSchool = $schools->firstWhere('id', request('school_id')); @endphp
            @if($selectedSchool)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 font-extrabold text-emerald-800 text-[11px] shadow-2xs">
                    <i class="fas fa-school text-emerald-600"></i> {{ $selectedSchool->name }}
                </span>
            @endif
        @endif
    </div>
    @endhasanyrole

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda + Ícone Circular) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Alunos (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total de Alunos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalStudents }}</span>
            </div>
        </div>

        <!-- Masculino (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-mars"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Masculino</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Student::where('gender', 'male')->count() }}</span>
            </div>
        </div>

        <!-- Feminino (Pink Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-pink-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-venus"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Feminino</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Student::where('gender', 'female')->count() }}</span>
            </div>
        </div>

        <!-- Matrículas Ativas (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Matrículas Ativas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Enrollment::where('status', 'active')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('students.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-6 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome, número de estudante ou BI..." value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
            <div>
                <select name="school_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>
                            {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endhasanyrole

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="pending_renewal" {{ request('status') == 'pending_renewal' ? 'selected' : '' }}>Pendente Renovação</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                    <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferidos</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Formados</option>
                </select>
            </div>

            <div>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Turmas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status', 'class_id', 'gender', 'school_id']))
                    <a href="{{ route('students.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Lista de Alunos ({{ $students->total() }})
            </h3>
            <span class="text-[11px] font-bold text-slate-400">Página {{ $students->currentPage() }} de {{ $students->lastPage() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Estudante</th>
                        @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
                        <th class="py-2.5 px-3">Escola</th>
                        @endhasanyrole
                        <th class="py-2.5 px-3">Número</th>
                        <th class="py-2.5 px-3">Turma</th>
                        <th class="py-2.5 px-3">Encarregado</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $currentEnrollment = $student->enrollments->where('status', 'active')->first();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    @if($student->passport_photo)
                                        <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->first_name }}" class="h-8 w-8 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="h-8 w-8 rounded-lg bg-emerald-700 flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-2xs">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 hover:no-underline transition">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-[10px] text-slate-400">
                                            <i class="fas fa-calendar-alt me-0.5"></i> {{ $student->birthdate ? $student->age . ' anos' : 'N/I' }} &nbsp; &nbsp;
                                            <?php 
                                            $genders = [
                                                'male' => '<i class="fas fa-mars text-blue-500"></i> Masculino',
                                                'female' => '<i class="fas fa-venus text-pink-500"></i> Feminino'
                                            ];
                                            ?>
                                            {!! $genders[$student->gender] ?? $student->gender !!}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
                            <td class="py-2.5 px-3">
                                @if($student->school)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold text-[10px] border border-slate-200">
                                        <i class="fas fa-school text-emerald-600 text-[9px]"></i> {{ $student->school->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">Sem escola</span>
                                @endif
                            </td>
                            @endhasanyrole
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono text-[11px]">
                                    {{ $student->student_number }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3">
                                @if($currentEnrollment)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                        {{ $currentEnrollment->class->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Sem turma</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @if($student->parent)
                                    <div class="font-semibold text-slate-800">{{ $student->parent->full_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $student->parent->phone ?? '-' }}</div>
                                @else
                                    <span class="text-slate-400">N/I</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @php
                                    $statusBadge = match($student->status) {
                                        'active' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'pending_renewal' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'inactive' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                        'transferred' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'graduated' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                    $statusLabel = match($student->status) {
                                        'active' => 'Ativo',
                                        'pending_renewal' => 'Renovação',
                                        'inactive' => 'Inativo',
                                        'transferred' => 'Transferido',
                                        'graduated' => 'Formado',
                                        default => ucfirst($student->status)
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <!--Registar Occorrência (desistente, transferido, etc) - reutiliza módulo de Acompanhamento -->
                                    <a href="{{ route('students.support.index', $student) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Registar Ocorrência / Acompanhamento">
                                        <i class="fas fa-exclamation-triangle text-xs"></i>
                                    </a>
                                    <a href="{{ route('students.show', $student) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @can('edit_students')
                                        <a href="{{ route('students.edit', $student) }}" class="no-underline h-7 w-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center transition" title="Editar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('delete_students')
                                        <form action="{{ route('students.destroy', $student) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="no-underline h-7 w-7 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 flex items-center justify-center transition" title="Apagar" onclick="return confirm('Tem certeza que deseja apagar este aluno?')">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-user-graduate text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum aluno encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection