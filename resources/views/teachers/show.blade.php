@extends('layouts.app')

@section('title', 'Detalhes do Professor')
@section('page-title', 'Detalhes do Professor')
@section('page-title-icon', 'fas fa-eye')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Professores</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> {{ $teacher->first_name }} {{ $teacher->last_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ $teacher->specialization }} · {{ $teacher->qualification }}</p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit_teachers')
                <a href="{{ route('teachers.edit', $teacher->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('teachers.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Content (2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- 1. Informações Pessoais -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i> Informações Pessoais
                    </h3>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Dados Pessoais</h4>
                        <div class="space-y-1.5 text-xs">
                            <p><span class="font-bold text-slate-700">Nome Completo:</span> <span class="text-slate-600">{{ $teacher->first_name }} {{ $teacher->last_name }}</span></p>
                            <p><span class="font-bold text-slate-700">BI:</span> <span class="text-slate-600">{{ $teacher->bi_number }}</span></p>
                            <p><span class="font-bold text-slate-700">Gênero:</span> <span class="text-slate-600">{{ $teacher->gender == 'male' ? 'Masculino' : 'Feminino' }}</span></p>
                            <p><span class="font-bold text-slate-700">Data Nascimento:</span> <span class="text-slate-600">{{ $teacher->birth_date ? $teacher->birth_date->format('d/m/Y') : 'Não informada' }}</span></p>
                            <p><span class="font-bold text-slate-700">Idade:</span> <span class="text-slate-600">{{ $teacher->birth_date ? $teacher->birth_date->diffInYears(now()) . ' anos' : 'Não informada' }}</span></p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Contactos</h4>
                        <div class="space-y-1.5 text-xs">
                            <p><span class="font-bold text-slate-700">Email:</span> <span class="text-slate-600">{{ $teacher->email }}</span></p>
                            <p><span class="font-bold text-slate-700">Telefone:</span> <span class="text-slate-600">{{ $teacher->phone }}</span></p>
                            <p><span class="font-bold text-slate-700">Endereço:</span> <span class="text-slate-600">{{ $teacher->address }}</span></p>
                        </div>
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mt-3">Informações Profissionais</h4>
                        <div class="space-y-1.5 text-xs">
                            <p><span class="font-bold text-slate-700">Data Contratação:</span> <span class="text-slate-600">{{ $teacher->hire_date ? $teacher->hire_date->format('d/m/Y') : 'Não informada' }}</span></p>
                            <p><span class="font-bold text-slate-700">Experiência:</span> <span class="text-slate-600">{{ $teacher->years_experience }} anos</span></p>
                            <p><span class="font-bold text-slate-700">Salário:</span> <span class="text-slate-600">{{ number_format($teacher->salary, 2, ',', '.') }} MZN</span></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5 text-xs">
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Qualificação</h4>
                        <p><span class="font-bold text-slate-700">Formação:</span> <span class="text-slate-600">{{ $teacher->qualification }}</span></p>
                        <p><span class="font-bold text-slate-700">Especialização:</span> <span class="text-slate-600">{{ $teacher->specialization }}</span></p>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Status</h4>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $teacher->status == 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $teacher->status == 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                            @if($teacher->user)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Conta de Usuário Ativa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Card de Atribuição de Turma (Posicionado logo por baixo das Informações Pessoais) -->
            @can('edit_teachers')
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-link text-emerald-600"></i> Atribuir Turma ao Docente
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">Turmas Ativas na Escola ({{ count($allClasses) }})</span>
                </div>
                <div class="p-4 bg-slate-50/50">
                    <form action="{{ route('teachers.assign-class', $teacher->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Turma Escolar</label>
                            <select name="class_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-bold focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">
                                <option value="">Selecione uma turma...</option>
                                @foreach($allClasses as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->name }} ({{ $c->grade_level_name ?? $c->grade_level }})
                                        @if($c->teacher_id == $teacher->id) - [Já Atribuída] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Função na Turma</label>
                            <select name="role" required
                                    class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-bold focus:border-emerald-500 focus:ring-emerald-500 shadow-2xs">
                                <option value="main">Professor Principal / Titular</option>
                                <option value="assistant">Professor Auxiliar</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-plus-circle"></i> Confirmar Atribuição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan

            <!-- 3. Turmas Atribuídas -->
            @if($teacher->classes->count() > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chalkboard text-emerald-600"></i> Turmas Atribuídas ({{ $teacher->classes->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                <th class="py-2.5 px-4">Turma</th>
                                <th class="py-2.5 px-3">Ano Letivo</th>
                                <th class="py-2.5 px-3">Nível</th>
                                <th class="py-2.5 px-3">Lotação Alunos</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($teacher->classes as $class)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4 font-bold text-slate-800">{{ $class->name }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $class->school_year ?? $class->academic_year }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $class->grade_level_name ?? $class->grade_level }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="text-slate-700 font-semibold">{{ $class->current_students ?? $class->enrollments_count ?? 0 }}/{{ $class->max_students ?? $class->capacity ?? 40 }}</span>
                                    <div class="w-full bg-slate-100 rounded-full h-1 mt-1">
                                        <div class="h-1 rounded-full bg-emerald-500" style="width: 50%"></div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Ativa
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-4">
            <!-- Stats Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-bar text-emerald-600"></i> Estatísticas
                    </h3>
                </div>
                <div class="p-4">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 rounded-xl bg-emerald-700 text-white font-bold text-lg flex items-center justify-center mx-auto shadow-sm">
                            {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 mt-2">{{ $teacher->first_name }} {{ $teacher->last_name }}</h4>
                        <p class="text-[10px] text-slate-500">{{ $teacher->specialization }}</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600">Turmas Totais:</span>
                            <span class="font-bold text-slate-800">{{ $stats['total_classes'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600">Turmas Atuais:</span>
                            <span class="font-bold text-slate-800">{{ $stats['current_classes'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600">Disciplinas:</span>
                            <span class="font-bold text-slate-800">{{ $stats['total_subjects'] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600">Licenças Pendentes:</span>
                            <span class="font-bold {{ $stats['pending_leave_requests'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $stats['pending_leave_requests'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-cog text-emerald-600"></i> Ações
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    @can('edit_teachers')
                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="no-underline w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                            <i class="fas fa-edit"></i> Editar Professor
                        </a>
                    @endcan

                    @can('delete_teachers')
                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-confirm="Tem certeza que deseja excluir este professor?"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-trash"></i> Excluir Professor
                            </button>
                        </form>
                    @endcan

                    @can('edit_teachers')
                        <form action="{{ route('teachers.toggle-status', $teacher->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg {{ $teacher->status == 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700' }} text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-{{ $teacher->status == 'active' ? 'pause' : 'play' }}"></i>
                                {{ $teacher->status == 'active' ? 'Desativar' : 'Ativar' }} Professor
                            </button>
                        </form>
                    @endcan

                    @if($teacher->user)
                        <a href="{{ route('admin.users.edit', $teacher->user->id) }}" class="no-underline w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white shadow-sm transition">
                            <i class="fas fa-user-cog"></i> Gerenciar Conta
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
