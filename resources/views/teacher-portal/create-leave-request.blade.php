@extends('layouts.app')

@section('title', 'Nova Solicitação de Licença')
@section('page-title', 'Nova Solicitação de Licença')
@section('page-title-icon', 'fas fa-calendar-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.leave-requests.index') }}" class="hover:text-emerald-600 transition">Licenças</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Nova Solicitação</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white font-bold text-sm flex items-center gap-2">
            <i class="fas fa-edit text-emerald-400"></i>
            <span>Preencha os dados da solicitação</span>
        </div>

        <div class="p-6 space-y-4">
            <form action="{{ route('teacher.leave-requests.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tipo de Licença <span class="text-rose-500">*</span></label>
                    <select name="leave_type" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('leave_type') border-rose-500 @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="sick">Licença Médica</option>
                        <option value="personal">Assuntos Pessoais</option>
                        <option value="vacation">Férias</option>
                        <option value="maternity">Maternidade</option>
                        <option value="other">Outro</option>
                    </select>
                    @error('leave_type')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Data de Início <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date"
                            class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('start_date') border-rose-500 @enderror"
                            value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                        @error('start_date')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Data de Término <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date"
                            class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('end_date') border-rose-500 @enderror"
                            value="{{ old('end_date') }}" required min="{{ date('Y-m-d') }}">
                        @error('end_date')
                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Motivo / Justificativa <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="4" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-medium focus:border-emerald-500 focus:ring-emerald-500 @error('reason') border-rose-500 @enderror" required placeholder="Descreva o motivo da sua solicitação...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex items-center justify-between">
                    <a href="{{ route('teacher.leave-requests.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                        <i class="fas fa-paper-plane"></i> Enviar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

