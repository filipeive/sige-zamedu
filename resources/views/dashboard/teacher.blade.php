@extends('layouts.app')

@section('title', 'Dashboard Professor')
@section('page-title', 'Dashboard Professor')
@section('page-title-icon', 'fas fa-chalkboard-teacher')

@section('breadcrumbs')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Dashboard Professor</li>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl" x-data="{ seconds: 3 }" x-init="setInterval(() => { if (seconds > 0) seconds--; }, 1000); setTimeout(() => window.location.href = '{{ route('teacher.dashboard') }}', 3000)">
        <div class="mb-5 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sky-900">
            <p class="text-sm">
                <strong>Portal do Professor:</strong>
                <a href="{{ route('teacher.dashboard') }}" class="font-semibold underline">clique aqui para acessar o portal completo</a>.
            </p>
        </div>

        <article class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Portal do Professor</h2>
            <p class="mt-2 text-sm text-slate-600">Voce esta logado como professor.</p>

            <a href="{{ route('teacher.dashboard') }}"
                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800">
                <i class="fas fa-external-link-alt"></i>
                Acessar Portal do Professor
            </a>

            <p class="mt-5 text-xs text-slate-500">
                Redirecionamento automatico em
                <span class="font-semibold text-slate-700" x-text="seconds"></span>s.
            </p>
        </article>
    </div>
@endsection
