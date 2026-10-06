@extends('layouts.app')

@section('title', 'Gestão de Licença - ZamEdu')
@section('page-title', 'Gestão de Licença')
@section('page-title-icon', 'fas fa-key')

@section('breadcrumbs')
    <li class="breadcrumb-item">License</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Licença</li>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-6 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
            <span class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-certificate text-emerald-600"></i> Estado da Licença ZamEdu
            </span>
            @if($license)
                @if($license->status === 'active')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                        <i class="fa-solid fa-circle-check"></i> Ativa
                    </span>
                @elseif($license->status === 'grace_period')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i> Período de Tolerância
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">
                        <i class="fa-solid fa-ban"></i> Suspensa
                    </span>
                @endif
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                    Não Registada
                </span>
            @endif
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('admin.license.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nome da Entidade / Cliente</label>
                    <input type="text" name="client_name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" value="{{ old('client_name', $license->client_name ?? '') }}" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chave da Licença</label>
                    <input type="text" name="license_key" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-900 font-mono tracking-wider focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50" value="{{ old('license_key', $license->license_key ?? '') }}" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Data de Expiração</label>
                        <input type="date" name="expires_at" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" value="{{ old('expires_at', $license?->expires_at ? $license->expires_at->format('Y-m-d') : '') }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Plano de Subscrição</label>
                        <select name="plan" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="standard" {{ ($license->plan ?? '') === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ ($license->plan ?? '') === 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ ($license->plan ?? '') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Licença
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

