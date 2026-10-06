@extends('layouts.app')

@section('title', 'Central de Mensagens')
@section('page-title', 'Central de Mensagens SMS')
@section('page-title-icon', 'fas fa-comment-dots')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Central de Mensagens</li>
@endsection

@section('title-icon', 'fas fa-comment-sms')

@section('content')
<div class="space-y-4">

    {{-- 1. Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-comment-sms text-emerald-600"></i> Central de Mensagens SMS
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Auditoria completa de todos os SMS enviados pelo sistema.
                @if ($simulated ?? false)
                    <span class="inline-flex items-center gap-1 ml-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-extrabold border border-rose-200">
                        <i class="fas fa-triangle-exclamation text-[8px]"></i>
                        MODO SIMULAÇÃO — SMS_SIMULATE=true (não envia real)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 ml-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold
                        {{ $enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        <i class="fas fa-circle text-[6px]"></i>
                        {{ $enabled ? 'Driver '.$driver.' activo' : 'SMS desactivado (defina SMS_ENABLED=true)' }}
                    </span>
                @endif
                @if ($from)
                    <span class="ml-1 text-slate-400">From: <span class="font-mono">{{ $from }}</span></span>
                @endif
            </p>
        </div>
    </div>

    {{-- 2. Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Enviados este Mês</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['sent_this_month'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Falhas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['failed'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa de Entrega</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['delivery_rate'] }}%</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Registos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['total'] }}</span>
            </div>
        </div>
    </div>

    {{-- 3. Tabs (links com query string) --}}
    @php $activeTab = request('tab', 'logs'); @endphp
    <div class="border-b border-slate-200 flex items-center gap-1 overflow-x-auto">
        <a href="{{ route('sms.index', ['tab' => 'logs']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $activeTab === 'logs' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-clock-rotate-left"></i> Histórico de Envios
        </a>
        <a href="{{ route('sms.index', ['tab' => 'inbox']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $activeTab === 'inbox' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-inbox"></i> Caixa de Entrada
            @if(($unreadInbox ?? 0) > 0)
                <span class="ml-1 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">
                    {{ $unreadInbox }}
                </span>
            @endif
        </a>
        <a href="{{ route('sms.index', ['tab' => 'send']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $activeTab === 'send' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-paper-plane"></i> Enviar SMS Individual
        </a>
    </div>

    {{-- TAB: HISTÓRICO --}}
    @if($activeTab === 'logs')
    <div class="space-y-4">
        {{-- Filtros --}}
        <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
            <form action="{{ route('sms.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-5 items-center">
                <input type="hidden" name="tab" value="logs">
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Pesquisar destinatário, telefone, mensagem..." value="{{ request('search') }}">
                        <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>
                <div>
                    <select name="channel" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Todos os Canais</option>
                        <option value="guardian" @selected(request('channel') === 'guardian')>Encarregado</option>
                        <option value="event" @selected(request('channel') === 'event')>Evento</option>
                        <option value="payment" @selected(request('channel') === 'payment')>Pagamento</option>
                        <option value="bulk" @selected(request('channel') === 'bulk')>Em massa</option>
                        <option value="system" @selected(request('channel') === 'system')>Sistema</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">Todos os Status</option>
                        <option value="sent" @selected(request('status') === 'sent')>Enviados</option>
                        <option value="failed" @selected(request('status') === 'failed')>Falhados</option>
                        <option value="disabled" @selected(request('status') === 'disabled')>Desactivados</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pendentes</option>
                    </select>
                </div>
                <div class="flex gap-1">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    @if(request('search') || request('channel') || request('status'))
                        <a href="{{ route('sms.index', ['tab' => 'logs']) }}" class="inline-flex items-center justify-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-extrabold">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Data & Hora</th>
                            <th class="px-4 py-2.5 text-left">Destinatário</th>
                            <th class="px-4 py-2.5 text-left">Telefone</th>
                            <th class="px-4 py-2.5 text-left">Canal</th>
                            <th class="px-4 py-2.5 text-left">Mensagem</th>
                            <th class="px-4 py-2.5 text-left">Status</th>
                            <th class="px-4 py-2.5 text-end">Acções</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            @php $badge = $log->status_badge; @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-4 py-2.5 text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-2.5">
                                    @if ($log->student)
                                        <a href="{{ route('students.show', $log->student) }}" class="font-bold text-slate-800 hover:text-emerald-700">
                                            {{ $log->recipient_name ?? $log->student->full_name }}
                                        </a>
                                        <small class="block text-[10px] text-slate-400 font-mono">{{ $log->student->student_number }}</small>
                                    @else
                                        <span class="font-bold text-slate-800">{{ $log->recipient_name ?? '—' }}</span>
                                        @if ($log->user)
                                            <small class="block text-[10px] text-slate-400">por {{ $log->user->name }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 font-mono text-slate-700 font-semibold">{{ $log->phone }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold border border-slate-200">
                                        {{ $log->channel_label }}
                                    </span>
                                    @if ($log->context)
                                        <small class="block text-[10px] text-slate-400 font-mono mt-0.5">{{ $log->context }}</small>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 max-w-xs">
                                    <p class="text-xs text-slate-700 line-clamp-2" title="{{ $log->message }}">{{ $log->message }}</p>
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badge['class'] }}">
                                        <i class="fas {{ $badge['icon'] }} text-[8px]"></i> {{ $badge['label'] }}
                                    </span>
                                    @if ($log->error && $log->status === 'failed')
                                        <small class="block text-[10px] text-rose-600 font-mono mt-1" title="{{ $log->error }}">
                                            <i class="fas fa-terminal"></i> {{ \Illuminate\Support\Str::limit($log->error, 40) }}
                                        </small>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-end">
                                    @can('view_students')
                                        <form action="{{ route('sms.resend', $log) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-extrabold transition"
                                                onclick="return confirm('Reenviar este SMS?')">
                                                <i class="fas fa-rotate-right"></i> Reenviar
                                            </button>
                                        </form>
                                        <!--delete-->
                                        <form action="{{ route('sms.destroy', $log) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-800 text-[10px] font-extrabold transition"
                                                onclick="return confirm('Apagar este SMS?')">
                                                <i class="fas fa-trash"></i> Apagar
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    <i class="fas fa-comment-slash text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-xs font-bold">Nenhum registo de envio de SMS encontrado.</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Os envios aparecerão aqui assim que forem feitos pelo sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="p-3 border-t border-slate-100">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- TAB: CAIXA DE ENTRADA --}}
    @if($activeTab === 'inbox')
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-inbox text-emerald-600"></i> Caixa de Entrada (SMS Recebidas)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                            <th class="p-3 text-left">Remetente</th>
                            <th class="p-3 text-left">Conteúdo</th>
                            <th class="p-3 text-left">Data</th>
                            <th class="p-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inboxMessages ?? [] as $msg)
                            <tr class="hover:bg-slate-50/80 transition {{ !$msg->is_read ? 'bg-emerald-50/30 font-semibold' : '' }}">
                                <td class="p-3 font-mono text-slate-800">{{ $msg->from_phone }}</td>
                                <td class="p-3 text-slate-700 max-w-md">{{ $msg->content }}</td>
                                <td class="p-3 text-slate-500 whitespace-nowrap">{{ $msg->received_at ? $msg->received_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3">
                                    @if($msg->is_read)
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">Lida</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Nova</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">Nenhuma mensagem recebida na Caixa de Entrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($inboxMessages) && method_exists($inboxMessages, 'hasPages') && $inboxMessages->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $inboxMessages->links() }}
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- TAB: ENVIAR --}}
    @if($activeTab === 'send')
    @php
        $templates = [
            'falta'       => 'Exmo(a) Encarregado(a), informamos que {aluno} faltou hoje às aulas. Pedimos contacto com a secretaria da '.config('app.name').'.',
            'propina'     => 'Exmo(a), lembramos que {aluno} tem propinas em atraso na '.config('app.name').'. Pedimos a regularização.',
            'reuniao'     => 'Exmo(a) Encarregado(a), comunicamos que a reunião de pais da turma de {aluno} será brevemente.',
            'positivo'    => 'Exmo(a), {aluno} recebeu uma observação positiva pelo bom comportamento.',
            'comparencia' => 'Pedimos a comparência do encarregado de {aluno} na secretaria da '.config('app.name').'.',
        ];
    @endphp
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-6 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-paper-plane text-emerald-600"></i> Envio Individual de SMS
            </h3>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">{{ $driver }} Provider</span>
        </div>

        <form action="{{ route('communications.sms.send-single') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Aluno *</label>
                    <select name="student_id" id="studentSelect"
                        onchange="
                            const opt = this.options[this.selectedIndex];
                            const name = opt.getAttribute('data-nome') || '';
                            const tplSelect = document.getElementById('templateType');
                            const tplKey = tplSelect.value;
                            const text = {{ json_encode($templates) }}[tplKey] || '';
                            const finalText = text.replace('{aluno}', name);
                            document.getElementById('customText').value = finalText;
                            document.getElementById('charCount').textContent = finalText.length;
                        "
                        required class="w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Escolha o aluno...</option>
                        @foreach ($students as $s)
                            @php
                                $phone = $s->parent?->phone ?? $s->emergency_phone;
                            @endphp
                            <option value="{{ $s->id }}"
                                data-nome="{{ $s->full_name }}"
                                data-fone="{{ $phone }}">
                                {{ $s->full_name }}{{ $phone ? ' — '.$phone : ' (sem telefone)' }}
                                @if ($s->student_number) · {{ $s->student_number }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Tipo de Mensagem</label>
                    <select id="templateType" name="template_type"
                        onchange="
                            const tplSelect = document.getElementById('templateType');
                            const tplKey = this.value;
                            const text = {{ json_encode($templates) }}[tplKey] || '';
                            const opt = document.getElementById('studentSelect').options[document.getElementById('studentSelect').selectedIndex];
                            const name = opt ? (opt.getAttribute('data-nome') || '') : '';
                            const finalText = text.replace('{aluno}', name);
                            document.getElementById('customText').value = finalText;
                            document.getElementById('charCount').textContent = finalText.length;
                        "
                        class="w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="falta">Falta às Aulas</option>
                        <option value="propina">Propinas em Atraso</option>
                        <option value="reuniao">Reunião de Pais</option>
                        <option value="positivo">Observação Positiva</option>
                        <option value="comparencia">Comparência na Secretaria</option>
                    </select>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 mb-0 uppercase tracking-wider">Mensagem SMS *</label>
                    <span id="charCount" class="text-[10px] font-mono text-slate-400">0 / 480 caracteres</span>
                </div>
                <textarea id="customText" name="message" rows="4" required maxlength="480"
                    oninput="document.getElementById('charCount').textContent = this.value.length + ' / 480 caracteres'"
                    class="w-full rounded-lg border-slate-200 text-xs focus:border-emerald-500 focus:ring-emerald-500 font-sans leading-relaxed">{{ $templates['falta'] }}</textarea>
                <p class="text-[10px] text-slate-400 mt-1">
                    Variáveis suportadas: <code class="px-1 bg-slate-100 rounded text-emerald-700">{aluno}</code>, <code class="px-1 bg-slate-100 rounded text-emerald-700">{escola}</code>.
                    Substituídas automaticamente antes do envio.
                </p>
            </div>

            <div class="pt-2 flex justify-end">
                <button class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-paper-plane"></i> Enviar SMS
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection