@extends('layouts.app')

@section('title', 'Meus Educandos')
@section('page-title', 'Meus Educandos')

@section('content')
<div class="space-y-6">

    {{-- Top Header Banner --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-sky-100 text-sky-700 rounded-xl text-xl font-bold">
                <i class="fas fa-child"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Meus Educandos</h1>
                <p class="text-xs text-slate-500 font-medium">Gestão e acompanhamento dos alunos vinculados ao seu perfil de encarregado</p>
            </div>
        </div>

        <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i> Painel Principal
        </a>
    </div>

    {{-- Grid de Educandos --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($children as $child)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-emerald-300 hover:shadow-md flex flex-col justify-between">
                <div>
                    {{-- Header Card Aluno --}}
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                        @if ($child->photo_url)
                            <img src="{{ $child->photo_url }}" alt="{{ $child->full_name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-500/30">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 font-bold text-xl ring-2 ring-emerald-500/20">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-base font-black text-slate-900">{{ $child->full_name }}</h3>
                            <p class="text-xs font-semibold text-emerald-700 mt-0.5">
                                {{ $child->currentEnrollment->class->name ?? 'Sem Turma' }}
                            </p>
                            <span class="inline-block mt-1 font-mono text-3xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                N.º {{ $child->student_number }}
                            </span>
                        </div>
                    </div>

                    {{-- Atributos Principais --}}
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                            <span class="block text-3xs font-bold text-slate-400 uppercase">Idade</span>
                            <span class="text-sm font-bold text-slate-800">{{ $child->age }} anos</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                            <span class="block text-3xs font-bold text-slate-400 uppercase">Gênero</span>
                            <span class="text-sm font-bold text-slate-800">{{ $child->gender === 'female' ? 'Feminino' : 'Masculino' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-5 space-y-2 pt-4 border-t border-slate-100">
                    <a href="{{ route('parent.student-grades', $child) }}"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-file-invoice"></i> Boletim de Notas
                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('parent.student-attendance', $child) }}"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-sky-600 px-3 py-2 text-xs font-bold text-white hover:bg-sky-700 transition-colors">
                            <i class="fas fa-calendar-check"></i> Presenças
                        </a>
                        <a href="{{ route('parent.student-details', $child) }}"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 transition-colors">
                            <i class="fas fa-user-circle"></i> Perfil
                        </a>
                    </div>
                    @if(is_private_school())
                    <a href="{{ route('parent.student-payments', $child) }}"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-50 border border-indigo-200 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-colors">
                        <i class="fas fa-credit-card"></i> Extrato de Propinas
                    </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200 p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                    <i class="fas fa-child text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-800">Nenhum educando associado</p>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Não foi encontrado nenhum aluno sob a sua responsabilidade no sistema.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
