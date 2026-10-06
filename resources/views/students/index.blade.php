@extends('layouts.app')

@section('title', 'Gestão de Alunos')
@section('page-title', 'Gestão de Alunos')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>
@endsection

@section('content')
<div class="space-y-4" x-data="{ viewMode: localStorage.getItem('zamedu_students_view') || (window.innerWidth < 768 ? 'grid' : 'table') }">

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
            <span class="px-2 py-0.5 rounded-full bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider">Modo Territorial</span>
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

    <!-- 2. Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Alunos -->
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

        <!-- Masculino -->
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

        <!-- Feminino -->
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

        <!-- Matrículas Ativas -->
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

    <!-- 3. Status Filter Tabs (Scrollable Modern Bar) -->
    <div class="no-print border-b border-slate-200 flex items-center justify-between gap-2 overflow-x-auto pb-px">
        <div class="flex items-center gap-1">
            <a href="{{ route('students.index', request()->except('status')) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ !request('status') ? 'border-emerald-600 text-emerald-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-users-viewfinder"></i> Todos
                <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">{{ $totalStudents }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except('status'), ['status' => 'active'])) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ request('status') === 'active' ? 'border-emerald-600 text-emerald-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-user-check text-emerald-600"></i> Ativos
                <span class="px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">{{ \App\Models\Student::where('status', 'active')->count() }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except('status'), ['status' => 'pending_renewal'])) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ request('status') === 'pending_renewal' ? 'border-amber-600 text-amber-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-clock text-amber-500"></i> Renovação Pendente
                <span class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">{{ \App\Models\Student::where('status', 'pending_renewal')->count() }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except('status'), ['status' => 'transferred'])) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ request('status') === 'transferred' ? 'border-blue-600 text-blue-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-exchange-alt text-blue-500"></i> Transferidos
                <span class="px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">{{ \App\Models\Student::where('status', 'transferred')->count() }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except('status'), ['status' => 'graduated'])) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ request('status') === 'graduated' ? 'border-purple-600 text-purple-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-graduation-cap text-purple-500"></i> Formados
                <span class="px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">{{ \App\Models\Student::where('status', 'graduated')->count() }}</span>
            </a>
            <a href="{{ route('students.index', array_merge(request()->except('status'), ['status' => 'inactive'])) }}"
               class="px-3.5 py-2 text-xs font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                      {{ request('status') === 'inactive' ? 'border-slate-600 text-slate-800 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <i class="fas fa-user-slash text-slate-400"></i> Inativos
                <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold">{{ \App\Models\Student::where('status', 'inactive')->count() }}</span>
            </a>
        </div>
        <div class="shrink-0 pb-1">
            <a href="{{ route('admin.students-archive.index') }}" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold flex items-center gap-1.5 no-underline transition shadow-2xs">
                <i class="fas fa-box-archive text-slate-400"></i> Arquivo
            </a>
        </div>
    </div>

    <!-- 4. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('students.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-6 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome, número ou BI..." value="{{ request('search') }}">
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
                    <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferidos</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Formados</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
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

    <!-- 5. Main Directory Container (with Table & Grid Switcher) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        {{-- Toolbar: Contagem + Switcher Tabela / Grelha --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-address-book text-emerald-600"></i> Lista de Alunos ({{ $students->total() }})
                </h3>
                <span class="text-[11px] font-bold text-slate-400">· Página {{ $students->currentPage() }} de {{ $students->lastPage() }}</span>
            </div>

            {{-- View Mode Toggle --}}
            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-100 border border-slate-200/80">
                <button type="button" @click="viewMode = 'table'; localStorage.setItem('zamedu_students_view', 'table')"
                        :class="viewMode === 'table' ? 'bg-white text-emerald-800 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-800'"
                        class="px-2.5 py-1 rounded-md text-xs font-bold transition flex items-center gap-1.5" title="Vista em Tabela">
                    <i class="fas fa-table-list text-xs"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
                <button type="button" @click="viewMode = 'grid'; localStorage.setItem('zamedu_students_view', 'grid')"
                        :class="viewMode === 'grid' ? 'bg-white text-emerald-800 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-800'"
                        class="px-2.5 py-1 rounded-md text-xs font-bold transition flex items-center gap-1.5" title="Vista em Grelha">
                    <i class="fas fa-border-all text-xs"></i> <span class="hidden sm:inline">Grelha</span>
                </button>
            </div>
        </div>

        {{-- VISTA 1: TABELA (Ideal para Desktop) --}}
        <div x-show="viewMode === 'table'" class="overflow-x-auto">
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
                            $currentEnrollment = $student->currentEnrollment;
                            $statusBadge = match($student->status) {
                                'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'pending_renewal' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                                'transferred' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'graduated' => 'bg-purple-100 text-purple-800 border-purple-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200'
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
                                        <a href="{{ route('students.show', $student) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 transition">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span><i class="fas fa-calendar-alt text-[9px]"></i> {{ $student->birthdate ? $student->age . ' anos' : 'N/I' }}</span>
                                            <span>•</span>
                                            @if($student->gender === 'male')
                                                <span class="text-blue-600"><i class="fas fa-mars text-[9px]"></i> M</span>
                                            @elseif($student->gender === 'female')
                                                <span class="text-pink-600"><i class="fas fa-venus text-[9px]"></i> F</span>
                                            @else
                                                <span>{{ $student->gender }}</span>
                                            @endif
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
                                @if($currentEnrollment && $currentEnrollment->class)
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
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $student->parent->phone ?? '-' }}</div>
                                @else
                                    <span class="text-slate-400">N/I</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('students.support.index', $student) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Registar Ocorrência / Acompanhamento">
                                        <i class="fas fa-clipboard-check text-xs"></i>
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
                                            <button type="submit" class="no-underline h-7 w-7 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 flex items-center justify-center transition cursor-pointer" title="Apagar" onclick="return confirm('Tem certeza que deseja apagar este aluno?')">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fas fa-user-graduate text-3xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-bold text-slate-600">Nenhum aluno encontrado para os critérios pesquisados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- VISTA 2: GRELHA / CARDS (Ideal para Telas Pequenas, Tablets e Mobile) --}}
        <div x-show="viewMode === 'grid'" class="p-4 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                @forelse($students as $student)
                    @php
                        $currentEnrollment = $student->currentEnrollment;
                        $statusBadge = match($student->status) {
                            'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'pending_renewal' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'transferred' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'graduated' => 'bg-purple-100 text-purple-800 border-purple-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                        };
                        $statusLabel = match($student->status) {
                            'active' => 'Ativo',
                            'pending_renewal' => 'Renovação',
                            'inactive' => 'Inativo',
                            'transferred' => 'Transferido',
                            'graduated' => 'Formado',
                            default => ucfirst($student->status)
                        };
                        $guardianPhone = $student->parent?->phone ?? $student->emergency_phone;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                        {{-- Topo do Card: Avatar, Identificação e Status --}}
                        <div class="p-3.5 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    @if($student->passport_photo)
                                        <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->first_name }}" class="h-11 w-11 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                                    @else
                                        <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-2xs">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('students.show', $student) }}" class="no-underline font-black text-sm text-slate-800 hover:text-emerald-700 transition block truncate leading-tight">
                                            {{ $student->full_name }}
                                        </a>
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] font-bold text-slate-600">
                                            {{ $student->student_number }}
                                        </span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider border {{ $statusBadge }} shrink-0">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Informações do Aluno --}}
                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1 border-t border-slate-100">
                                <div>
                                    <span class="text-slate-400 block text-[9px] font-bold uppercase">Turma</span>
                                    @if($currentEnrollment && $currentEnrollment->class)
                                        <span class="font-bold text-emerald-800">{{ $currentEnrollment->class->name }}</span>
                                    @else
                                        <span class="text-slate-400">Sem turma</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px] font-bold uppercase">Idade / Sexo</span>
                                    <span class="font-semibold text-slate-700">
                                        {{ $student->birthdate ? $student->age . ' anos' : 'N/I' }} ·
                                        @if($student->gender === 'male')
                                            <span class="text-blue-600 font-bold">M</span>
                                        @elseif($student->gender === 'female')
                                            <span class="text-pink-600 font-bold">F</span>
                                        @else
                                            {{ $student->gender }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Encarregado & Contacto Rápido --}}
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 block">Encarregado</span>
                                    <span class="font-bold text-slate-800 block truncate text-[11px]">
                                        {{ $student->parent?->full_name ?? ($student->emergency_contact ?? 'Não Registado') }}
                                    </span>
                                </div>
                                @if($guardianPhone)
                                    <div class="flex items-center gap-1 shrink-0">
                                        <a href="tel:{{ $guardianPhone }}" class="h-7 w-7 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 flex items-center justify-center transition no-underline shadow-2xs" title="Ligar para {{ $guardianPhone }}">
                                            <i class="fas fa-phone text-[10px]"></i>
                                        </a>
                                        <a href="{{ route('sms.index', ['tab' => 'send']) }}" class="h-7 w-7 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 flex items-center justify-center transition no-underline shadow-2xs" title="Enviar SMS">
                                            <i class="fas fa-comment text-[10px]"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
                                @if($student->school)
                                    <div class="text-[10px] text-slate-500 flex items-center gap-1 truncate">
                                        <i class="fas fa-school text-emerald-600 text-[9px]"></i>
                                        <span class="font-semibold truncate">{{ $student->school->name }}</span>
                                    </div>
                                @endif
                            @endhasanyrole
                        </div>

                        {{-- Rodapé do Card: Ações Rápidas --}}
                        <div class="p-2.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('students.show', $student) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold no-underline transition shadow-2xs">
                                <i class="fas fa-eye text-[11px]"></i> Ver Perfil
                            </a>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('students.support.index', $student) }}" class="h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 flex items-center justify-center transition no-underline" title="Acompanhamento / Ocorrência">
                                    <i class="fas fa-clipboard-check text-xs"></i>
                                </a>
                                @can('edit_students')
                                    <a href="{{ route('students.edit', $student) }}" class="h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-amber-50 hover:text-amber-700 text-slate-600 flex items-center justify-center transition no-underline" title="Editar Aluno">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                @endcan
                                @can('delete_students')
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-700 text-slate-600 flex items-center justify-center transition cursor-pointer" title="Apagar" onclick="return confirm('Tem certeza que deseja apagar este aluno?')">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                        <i class="fas fa-user-graduate text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-bold text-slate-600">Nenhum aluno encontrado para os critérios pesquisados.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Paginação Unificada --}}
        @if($students->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-white">
                {{ $students->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection