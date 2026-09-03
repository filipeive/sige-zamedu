@extends('layouts.app')

@section('title', 'Perfil do Encarregado - ' . $parent->full_name)
@section('page-title', $parent->full_name)
@section('page-title-icon', 'fas fa-user-shield')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('parents.index') }}">Encarregados</a></li>
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-shield text-emerald-600"></i> {{ $parent->full_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perfil do encarregado de educação e educandos associados.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit_students')
                <a href="{{ route('parents.edit', $parent->user_id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('parents.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Profile Sidebar -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 rounded-xl bg-emerald-700 text-white font-bold text-lg flex items-center justify-center mx-auto shadow-sm">
                        {{ strtoupper(substr($parent->first_name, 0, 1) . substr($parent->last_name, 0, 1)) }}
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 mt-2">{{ $parent->full_name }}</h4>
                    <p class="text-[10px] text-slate-500">Encarregado de Educação</p>
                </div>
                <div class="px-5 pb-5 border-t border-slate-100 pt-3 space-y-2 text-xs">
                    <p><span class="font-bold text-slate-700"><i class="fas fa-phone mr-1 text-slate-400"></i> Telefone:</span> <span class="text-slate-600">{{ $parent->phone }}</span></p>
                    <p><span class="font-bold text-slate-700"><i class="fas fa-envelope mr-1 text-slate-400"></i> E-mail:</span> <span class="text-slate-600">{{ $parent->email }}</span></p>
                    <p><span class="font-bold text-slate-700"><i class="fas fa-id-card mr-1 text-slate-400"></i> Documento:</span> <span class="text-slate-600">{{ $parent->bi_number ?? 'Não informado' }}</span></p>
                    <p><span class="font-bold text-slate-700"><i class="fas fa-briefcase mr-1 text-slate-400"></i> Profissão:</span> <span class="text-slate-600">{{ $parent->profession ?? 'N/A' }}</span></p>
                    <p><span class="font-bold text-slate-700"><i class="fas fa-map-marker-alt mr-1 text-slate-400"></i> Endereço:</span> <span class="text-slate-600">{{ $parent->address ?? 'N/A' }}</span></p>
                </div>
            </div>
        </div>

        <!-- Students Table (2/3) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-graduate text-emerald-600"></i> Educando(s) sob Responsabilidade ({{ count($parent->students) }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                <th class="py-2.5 px-4">Aluno</th>
                                <th class="py-2.5 px-3">N.º de Estudante</th>
                                <th class="py-2.5 px-3">Turma Atual</th>
                                <th class="py-2.5 px-4 text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($parent->students as $student)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4 font-bold text-slate-800">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $student->student_number }}</td>
                                <td class="py-2.5 px-3">
                                    @if($student->currentEnrollment?->class)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            {{ $student->currentEnrollment->class->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Sem turma ativa</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-end">
                                    <a href="{{ route('students.show', $student->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition">
                                        <i class="fas fa-eye"></i> Ver Ficha
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">
                                    <i class="fas fa-user-slash text-2xl text-slate-300 mb-2"></i>
                                    <p class="text-xs font-semibold">Nenhum educando associado a este encarregado.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
