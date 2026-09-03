@extends('layouts.app')

@section('title', 'Meus Filhos')
@section('page-title', 'Meus Filhos')

@section('content')
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-900">Alunos vinculados ao responsavel</h3>
        </header>
        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($children as $child)
                <article class="rounded-xl border border-slate-200 p-4">
                    <div class="text-center">
                        @if ($child->photo_url)
                            <img src="{{ $child->photo_url }}" alt="{{ $child->full_name }}"
                                class="mx-auto h-20 w-20 rounded-full object-cover ring-2 ring-slate-200">
                        @else
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                <i class="fas fa-user text-2xl"></i>
                            </div>
                        @endif
                        <h4 class="mt-3 text-base font-bold text-slate-900">{{ $child->full_name }}</h4>
                    </div>

                    <dl class="mt-4 space-y-2 rounded-lg bg-slate-50 p-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Turma</dt>
                            <dd class="font-semibold text-slate-800">{{ $child->currentEnrollment->class->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Numero</dt>
                            <dd class="font-semibold text-slate-800">{{ $child->student_number }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Idade</dt>
                            <dd class="font-semibold text-slate-800">{{ $child->age }} anos</dd>
                        </div>
                    </dl>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('parent.student-details', $child) }}"
                            class="rounded-lg bg-sky-700 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-sky-800">
                            Detalhes
                        </a>
                        @if(is_private_school())
                        <a href="{{ route('parent.student-payments', $child) }}"
                            class="rounded-lg bg-emerald-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-emerald-700">
                            Pagamentos
                        </a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="col-span-full rounded-lg bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Nenhum filho encontrado.</p>
            @endforelse
        </div>
    </section>
@endsection
