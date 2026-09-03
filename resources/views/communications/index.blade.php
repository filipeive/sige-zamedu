@extends('layouts.app')

@section('title', 'Central de Comunicação & SMS')
@section('page-title', 'Central de Comunicação & SMS')
@section('page-title-icon', 'fas fa-bullhorn')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Comunicados & SMS</li>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-4">

    <!-- 1. Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-bullhorn text-emerald-600"></i> Central de Comunicação & Mensagens SMS
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Envio de avisos oficiais, circulares pedagógicas e envio de SMS para a comunidade escolar.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @can('send_notifications')
                <button type="button" onclick="openSmsModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-2xs cursor-pointer">
                    <i class="fas fa-sms text-emerald-600"></i> Enviar SMS Direto
                </button>
                <a href="{{ route('communications.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-plus"></i> Novo Comunicado Geral
                </a>
            @endcan
            <a href="{{ route('sms.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-history text-slate-500"></i> Histórico SMS
            </a>
        </div>
    </div>

    <!-- 2. Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Comunicados</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>
            <div class="text-xl font-black text-slate-800 mt-1">{{ number_format($communications->total()) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Circulares e avisos</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Encarregados</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="text-xl font-black text-blue-700 mt-1">Ativo</div>
            <span class="text-[10px] text-blue-600/80 font-medium">Canal direto via App/SMS</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Corpo Docente</span>
                <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="text-xl font-black text-purple-700 mt-1">Ativo</div>
            <span class="text-[10px] text-purple-600/80 font-medium">Avisos a professores</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Serviço SMS</span>
                <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-comment-dots"></i>
                </div>
            </div>
            <div class="text-xl font-black text-teal-700 mt-1">Operacional</div>
            <span class="text-[10px] text-teal-600/80 font-medium">Envios em tempo real</span>
        </div>
    </div>

    <!-- 3. Communications Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Histórico de Comunicados Emitidos
            </h3>
            <span class="text-[11px] font-bold text-slate-500">Exibindo {{ $communications->firstItem() ?? 0 }} - {{ $communications->lastItem() ?? 0 }} de {{ $communications->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-4">Data / Hora</th>
                        <th class="py-2.5 px-3">Título & Resumo</th>
                        <th class="py-2.5 px-3 text-center">Público-Alvo</th>
                        <th class="py-2.5 px-3 text-center">Prioridade</th>
                        <th class="py-2.5 px-3">Autor</th>
                        <th class="py-2.5 px-3 text-center">Estado</th>
                        <th class="py-2.5 px-4 text-end">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($communications as $comm)
                        @php
                            $safeTitle = addslashes($comm->title);
                            $safeMessage = addslashes(str_replace(["\r\n", "\r", "\n"], [" ", " ", " "], $comm->message));
                            $safeAudience = addslashes($comm->target_audience_name);
                            $safePriority = ucfirst($comm->priority);
                            $safeAuthor = addslashes($comm->creator->name ?? 'Sistema');
                            $safeDate = $comm->created_at->format('d/m/Y H:i');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 text-slate-600 font-bold font-mono">
                                {{ $comm->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-bold text-slate-800 block text-xs">{{ $comm->title }}</span>
                                <span class="text-[11px] text-slate-500 block truncate max-w-xs">{{ $comm->excerpt }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $comm->target_audience_name }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($comm->priority === 'high')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Alta</span>
                                @elseif($comm->priority === 'medium')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Média</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Baixa</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-semibold">
                                {{ $comm->creator->name ?? 'Sistema' }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($comm->is_published)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle"></i> Publicado
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-clock"></i> Agendado
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <!-- Botão Ver Detalhes Direto -->
                                <button type="button" 
                                        onclick="openCommModal('{{ $safeTitle }}', '{{ $safeMessage }}', '{{ $safeAudience }}', '{{ $safePriority }}', '{{ $safeAuthor }}', '{{ $safeDate }}')"
                                        class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-2xs cursor-pointer">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fas fa-bullhorn text-3xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum comunicado enviado ainda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($communications->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $communications->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- 4. Modal de Detalhes do Comunicado (Native Modal) -->
    <div id="commDetailModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-emerald-600"></i>
                    <span id="commModalTitle">Detalhes do Comunicado</span>
                </h3>
                <button type="button" onclick="closeCommModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs grid grid-cols-2 gap-2 text-slate-600 font-medium">
                <p><span class="font-bold text-slate-800">Público-Alvo:</span> <span id="commModalAudience">-</span></p>
                <p><span class="font-bold text-slate-800">Prioridade:</span> <span id="commModalPriority">-</span></p>
                <p><span class="font-bold text-slate-800">Emitido por:</span> <span id="commModalAuthor">-</span></p>
                <p><span class="font-bold text-slate-800">Data:</span> <span id="commModalDate">-</span></p>
            </div>

            <div id="commModalMessage" class="p-4 rounded-xl border border-slate-200 bg-white text-xs leading-relaxed text-slate-700 max-h-72 overflow-y-auto font-medium whitespace-pre-wrap"></div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCommModal()" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">Fechar</button>
            </div>
        </div>
    </div>

    <!-- 5. Modal de Envio Rápido de SMS -->
    <div id="quickSmsModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-sms text-emerald-600"></i> Enviar Mensagem SMS Direta
                </h3>
                <button type="button" onclick="closeSmsModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('communications.sms.send-single') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Selecione o Estudante / Encarregado *</label>
                    <select name="student_id" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Selecione o aluno destinatário...</option>
                        @foreach(\App\Models\Student::with('parent')->orderBy('first_name')->take(100)->get() as $st)
                            <option value="{{ $st->id }}">
                                {{ $st->full_name }} ({{ $st->parent?->full_name ?? 'Encarregado sem nome' }} - {{ $st->parent?->phone ?? $st->emergency_phone ?? 'Sem Tel' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Contacto Alternativo (Opcional)</label>
                    <input type="text" name="phone" placeholder="Ex: 841234567" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mensagem SMS (máx 480 caracteres) *</label>
                    <textarea name="message" rows="4" maxlength="480" required placeholder="Escreva a mensagem SMS..." class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Variáveis disponíveis: {aluno}, {escola}</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeSmsModal()" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-paper-plane"></i> Enviar SMS
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openCommModal(title, message, audience, priority, author, date) {
        document.getElementById('commModalTitle').innerText = title;
        document.getElementById('commModalAudience').innerText = audience;
        document.getElementById('commModalPriority').innerText = priority;
        document.getElementById('commModalAuthor').innerText = author;
        document.getElementById('commModalDate').innerText = date;
        document.getElementById('commModalMessage').innerText = message || 'Sem mensagem cadastrada.';
        
        const modal = document.getElementById('commDetailModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeCommModal() {
        const modal = document.getElementById('commDetailModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function openSmsModal() {
        const modal = document.getElementById('quickSmsModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeSmsModal() {
        const modal = document.getElementById('quickSmsModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    // Fechar modais ao clicar no fundo escuro
    document.getElementById('commDetailModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCommModal();
    });

    document.getElementById('quickSmsModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSmsModal();
    });

    // Fechar modais ao pressionar a tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCommModal();
            closeSmsModal();
        }
    });
</script>
@endpush
@endsection
