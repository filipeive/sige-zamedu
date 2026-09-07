@extends('layouts.app')

@section('title', 'Novo Comunicado')
@section('page-title', 'Novo Comunicado')
@section('page-title-icon', 'fas fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('communications.index') }}">Comunicados</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-plus-circle text-emerald-600"></i> Criar Novo Comunicado
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Envio de aviso ou mensagem oficial para a comunidade escolar.</p>
        </div>
        <a href="{{ route('communications.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="w-full bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5 max-w-3xl">
        <form action="{{ route('communications.send') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Título do Comunicado *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Ex: Reunião de Pais e Encarregados"
                       class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('title') border-rose-500 @enderror">
                @error('title')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="target_audience" class="block text-xs font-bold text-slate-700 mb-1">Público-Alvo *</label>
                    <select name="target_audience" id="target_audience" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('target_audience') border-rose-500 @enderror">
                        <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="parents" {{ old('target_audience') == 'parents' ? 'selected' : '' }}>Encarregados</option>
                        <option value="teachers" {{ old('target_audience') == 'teachers' ? 'selected' : '' }}>Professores</option>
                        <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Alunos</option>
                    </select>
                    @error('target_audience')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-700 mb-1">Prioridade *</label>
                    <select name="priority" id="priority" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('priority') border-rose-500 @enderror">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>Média</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                    </select>
                    @error('priority')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="message" class="block text-xs font-bold text-slate-700 mb-1">Mensagem *</label>
                <textarea name="message" id="message" rows="6" required placeholder="Escreva aqui o conteúdo do comunicado..."
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('message') border-rose-500 @enderror">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2 pt-2">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="is_public" class="text-xs font-bold text-slate-700">Tornar este comunicado público (visível sem login)</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="send_sms" id="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }}
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="send_sms" class="text-xs font-bold text-slate-700">Enviar também notificação via SMS para os Encarregados de Educação</label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('communications.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-paper-plane"></i> Enviar Comunicado
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
