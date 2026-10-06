@extends('layouts.app')

@section('title', 'Gestão de Ano Lectivo')
@section('page-title', 'Gestão de Ano Lectivo')
@section('page-title-icon', 'fas fa-calendar-days')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Gestão de Ano Lectivo</li>
@endsection

@section('content')
<div class="w-full space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <!-- Header with MINEDH Green Gradient -->
        <div class="bg-gradient-to-r from-[#034522] via-[#007A3D] to-[#059669] px-6 py-5 text-white flex items-center justify-between">
            <h3 class="text-xl font-black tracking-tight flex items-center gap-2.5 font-heading">
                <i class="fa-solid fa-calendar-days text-amber-300"></i>
                <span>Configuração & Transição do Ano Lectivo</span>
            </h3>
            <span class="px-3 py-1 rounded-full bg-white/15 border border-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-md">
                MINEDH · SNE
            </span>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            <!-- Year Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
                <div class="bg-slate-50 dark:bg-slate-800/60 p-6 rounded-2xl border-2 border-slate-200 dark:border-slate-700 shadow-xs">
                    <h6 class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Ano Lectivo Actual</h6>
                    <div class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white font-mono tracking-tight">{{ $currentYear }}</div>
                </div>

                <div class="bg-emerald-50/80 dark:bg-emerald-950/40 p-6 rounded-2xl border-2 border-emerald-300 dark:border-emerald-700 shadow-xs">
                    <h6 class="text-xs font-extrabold text-[#007A3D] dark:text-emerald-400 uppercase tracking-wider mb-2">Próximo Ano (Em Preparação)</h6>
                    <div class="text-4xl sm:text-5xl font-black text-[#007A3D] dark:text-emerald-300 font-mono tracking-tight">{{ $nextYear }}</div>
                </div>
            </div>

            <!-- Informational Box -->
            <div class="bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 rounded-2xl p-5">
                <h5 class="text-sm font-bold flex items-center gap-2 mb-2 text-sky-800 dark:text-sky-300">
                    <i class="fa-solid fa-circle-info text-sky-600 text-base"></i> Como funciona a transição de ano lectivo?
                </h5>
                <p class="text-xs text-sky-800 dark:text-sky-300 mb-2 font-medium">Ao iniciar a transição para o novo ano lectivo:</p>
                <ul class="text-xs space-y-1.5 list-disc list-inside text-sky-800 dark:text-sky-300 font-medium">
                    <li>O <strong>Ano Actual</strong> passará a ser <strong>{{ $nextYear }}</strong>.</li>
                    <li>Todas as matrículas activas de <strong>{{ $currentYear }}</strong> serão marcadas como <strong>Concluídas</strong>.</li>
                    <li>Os alunos serão marcados como <strong>Inactivos</strong>, activando o botão de <strong>Renovação</strong> no portal dos pais.</li>
                    <li>O sistema estará pronto para receber novas inscrições e turmas para <strong>{{ $nextYear }}</strong>.</li>
                </ul>
            </div>

            <!-- Warning Box -->
            <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl shrink-0 mt-0.5"></i>
                    <div>
                        <h5 class="text-sm font-bold text-amber-900 dark:text-amber-300 mb-1">Atenção Importante!</h5>
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-medium">
                            Esta acção é irreversível e deve ser feita apenas quando o ano lectivo actual terminar e os resultados finais (Pautas de Exame) forem homologados.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <form action="{{ route('admin.academic-years.transition') }}" method="POST"
                data-confirm="Tem certeza que deseja iniciar a transição para o novo ano lectivo? Esta acção afectará todas as matrículas activas.">
                @csrf
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-[#007A3D] to-[#034522] hover:from-[#059669] hover:to-[#007A3D] text-white font-extrabold text-sm uppercase tracking-wider shadow-lg shadow-emerald-900/20 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 group">
                    <i class="fa-solid fa-forward text-amber-300 group-hover:translate-x-1 transition-transform"></i>
                    <span>Iniciar Transição para {{ $nextYear }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection