@extends('layouts.app')

@section('title', 'Auditoria do Sistema — Logs de Atividade')

@section('content')
<div class="space-y-8 p-4 sm:p-6 lg:p-8">

    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-2xl border-b-4 border-amber-400">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 flex items-center justify-center text-2xl shadow-inner shrink-0">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-900/80 border border-emerald-400/40 text-emerald-300 text-[11px] font-black uppercase tracking-wider mb-1">
                        <i class="fas fa-eye text-amber-400"></i> AUDITORIA E SEGURANÇA NACIONAL
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-heading text-white tracking-tight">Logs de Atividade & Rastreabilidade</h1>
                    <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1">Registo detalhado de todas as alterações, acessos e operações efetuadas pelos utilizadores no sistema.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-slate-900/90 border border-slate-700 text-xs font-bold text-slate-300">
                    <i class="fas fa-database text-emerald-400 me-1"></i> {{ number_format($totalLogs) }} Registos no Sistema
                </div>
            </div>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Registos</span>
                <i class="fas fa-list-check text-slate-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ number_format($totalLogs) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Histórico completo de auditoria</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ações Hoje</span>
                <i class="fas fa-calendar-day text-blue-500 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-blue-600 dark:text-blue-400 font-heading">{{ number_format($todayLogs) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Operações registadas nas últimas 24h</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Criações</span>
                <i class="fas fa-plus-circle text-emerald-500 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-heading">{{ number_format($createdLogs) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Novos objetos e registos criados</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alterações</span>
                <i class="fas fa-pen-to-square text-amber-500 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 font-heading">{{ number_format($updatedLogs) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Edições e modificações de dados</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Eliminações</span>
                <i class="fas fa-trash-can text-rose-500 text-lg"></i>
            </div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 font-heading">{{ number_format($deletedLogs) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Registos removidos do sistema</div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-search me-1"></i> Pesquisar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Palavra-chave, descrição ou utilizador..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
            </div>

            <!-- Event Filter -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-filter me-1"></i> Tipo de Ação</label>
                <select name="event" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                    <option value="">Todas as Ações</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Criação (created)</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Alteração (updated)</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminação (deleted)</option>
                    <option value="impersonated" {{ request('event') == 'impersonated' ? 'selected' : '' }}>Modo Suporte (impersonated)</option>
                </select>
            </div>

            <!-- User Filter -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-user me-1"></i> Utilizador</label>
                <select name="user_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                    <option value="">Todos os Utilizadores</option>
                    @foreach($causers as $causer)
                        <option value="{{ $causer->id }}" {{ request('user_id') == $causer->id ? 'selected' : '' }}>{{ $causer->name }} ({{ $causer->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5"><i class="fas fa-calendar me-1"></i> Data De</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-black transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('admin.audit.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition" title="Limpar Filtros">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- LOGS TABLE -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="p-3.5 rounded-l-xl">Data & Hora</th>
                        <th class="p-3.5">Utilizador / Autor</th>
                        <th class="p-3.5">Ação Efetuada</th>
                        <th class="p-3.5">Objeto / Módulo</th>
                        <th class="p-3.5 text-right rounded-r-xl">Ação / Inspeção</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900 dark:text-white">{{ $activity->created_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-500 font-mono"><i class="far fa-clock me-1"></i>{{ $activity->created_at->format('H:i:s') }}</div>
                            </td>

                            <td class="p-3.5">
                                @if($activity->causer)
                                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div>{{ $activity->causer->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-normal">{{ $activity->causer->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold text-[10px]">
                                        <i class="fas fa-robot text-slate-400"></i> Sistema Automático
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5">
                                @php
                                    $eventBadge = match($activity->event) {
                                        'created' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300',
                                        'updated' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950 dark:text-amber-300',
                                        'deleted' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950 dark:text-rose-300',
                                        default => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950 dark:text-blue-300'
                                    };
                                    $eventLabel = match($activity->event) {
                                        'created' => 'Criação',
                                        'updated' => 'Alteração',
                                        'deleted' => 'Eliminação',
                                        default => ucfirst($activity->event ?? 'Operação')
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full border font-extrabold text-[10px] inline-block uppercase tracking-wider {{ $eventBadge }}">
                                    {{ $eventLabel }}
                                </span>
                                <div class="text-xs text-slate-700 dark:text-slate-300 font-semibold mt-1">
                                    {{ $activity->description }}
                                </div>
                            </td>

                            <td class="p-3.5">
                                @if($activity->subject_type)
                                    <div class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ class_basename($activity->subject_type) }} <span class="text-slate-400 font-mono">#{{ $activity->subject_id }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $activity->subject_type }}</div>
                                @else
                                    <span class="text-slate-400 italic">Módulo Geral / Configuração</span>
                                @endif
                            </td>

                            <td class="p-3.5 text-right">
                                <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-300 font-bold text-xs border border-slate-700 shadow-sm transition inline-flex items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#auditModal{{ $activity->id }}">
                                    <i class="fas fa-magnifying-glass text-amber-400"></i>
                                    <span>Ver O Que Fez</span>
                                </button>

                                <!-- AUDIT DETAIL MODAL -->
                                <div class="modal fade text-left" id="auditModal{{ $activity->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content rounded-3xl border-0 shadow-2xl overflow-hidden bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                                            
                                            <!-- Modal Header -->
                                            <div class="bg-gradient-to-r from-slate-950 to-emerald-950 text-white p-6 border-b-2 border-amber-400 flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 font-black flex items-center justify-center text-lg">
                                                        <i class="fas fa-file-shield"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-base font-black font-heading">Detalhe de Auditoria #{{ $activity->id }}</h4>
                                                        <p class="text-xs text-slate-300 font-medium">Inspeção completa dos dados alterados e metadados do utilizador.</p>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="p-6 space-y-6">
                                                <!-- Metadata Summary Bar -->
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                                                    <div>
                                                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Utilizador / Autor</span>
                                                        <span class="font-extrabold text-slate-900 dark:text-white text-xs block mt-0.5">{{ $activity->causer->name ?? 'Sistema Automático' }}</span>
                                                        <span class="text-[10px] text-slate-500">{{ $activity->causer->email ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Data & Hora</span>
                                                        <span class="font-mono font-bold text-slate-900 dark:text-white text-xs block mt-0.5">{{ $activity->created_at->format('d/m/Y H:i:s') }}</span>
                                                        <span class="text-[10px] text-slate-500">{{ $activity->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ação & Módulo</span>
                                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-xs block mt-0.5 uppercase">{{ $activity->event }}</span>
                                                        <span class="text-[10px] text-slate-500">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</span>
                                                    </div>
                                                </div>

                                                <!-- Description -->
                                                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 p-3.5 rounded-xl">
                                                    <span class="text-[10px] font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider block mb-1">Descrição Registada:</span>
                                                    <span class="text-xs font-extrabold text-amber-950 dark:text-amber-200">{{ $activity->description }}</span>
                                                </div>

                                                <!-- FIELD BY FIELD DIFF TABLE -->
                                                @php
                                                    $properties = json_decode(json_encode($activity->properties), true);
                                                    $oldValues = $properties['old'] ?? [];
                                                    $newValues = $properties['attributes'] ?? [];

                                                    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                                                @endphp

                                                @if(count($allKeys) > 0)
                                                    <div>
                                                        <h5 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                                            <i class="fas fa-arrows-split-up-and-left text-emerald-600"></i>
                                                            O Que o Utilizador Alterou Exatamente (Comparativo de Campos):
                                                        </h5>
                                                        
                                                        <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm">
                                                            <table class="w-full text-left text-xs border-collapse">
                                                                <thead>
                                                                    <tr class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider">
                                                                        <th class="p-3">Campo Modificado</th>
                                                                        <th class="p-3 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-300">Valor Anterior (Old)</th>
                                                                        <th class="p-3 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300">Novo Valor (New)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                                                    @foreach($allKeys as $keyName)
                                                                        @php
                                                                            $oldVal = $oldValues[$keyName] ?? null;
                                                                            $newVal = $newValues[$keyName] ?? null;

                                                                            // Format array or object values
                                                                            if (is_array($oldVal)) $oldVal = json_encode($oldVal);
                                                                            if (is_array($newVal)) $newVal = json_encode($newVal);

                                                                            $fieldLabels = [
                                                                                'name' => 'Nome',
                                                                                'first_name' => 'Primeiro Nome',
                                                                                'last_name' => 'Apelido / Sobrenome',
                                                                                'email' => 'Endereço de E-mail',
                                                                                'phone' => 'Telefone',
                                                                                'grade' => 'Nota (Avaliação)',
                                                                                'status' => 'Estado / Status',
                                                                                'school_id' => 'ID da Escola',
                                                                                'class_id' => 'ID da Turma',
                                                                                'subject_id' => 'ID da Disciplina',
                                                                                'amount' => 'Valor Monetário',
                                                                                'bi_number' => 'N.º de BI',
                                                                                'birthdate' => 'Data de Nascimento',
                                                                                'is_active' => 'Estado de Ativação',
                                                                            ];
                                                                            $displayLabel = $fieldLabels[$keyName] ?? ucfirst(str_replace('_', ' ', $keyName));
                                                                        @endphp
                                                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                                                            <td class="p-3 font-extrabold text-slate-900 dark:text-white bg-slate-50/50 dark:bg-slate-800/30">
                                                                                {{ $displayLabel }}
                                                                                <span class="block text-[10px] text-slate-400 font-mono">{{ $keyName }}</span>
                                                                            </td>
                                                                            <td class="p-3 bg-rose-50/30 dark:bg-rose-950/20 text-rose-900 dark:text-rose-200">
                                                                                @if(!is_null($oldVal))
                                                                                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-200 font-mono text-[11px] font-bold line-through me-1">
                                                                                        {{ $oldVal }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-slate-400 italic text-[11px]">(Nulo / Vazio)</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="p-3 bg-emerald-50/30 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-200">
                                                                                @if(!is_null($newVal))
                                                                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-200 font-mono text-[11px] font-bold me-1">
                                                                                        {{ $newVal }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-slate-400 italic text-[11px]">(Removido)</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-800 text-center text-slate-500 text-xs">
                                                        <i class="fas fa-info-circle me-1"></i> Esta ação não registou alterações de atributos específicos.
                                                    </div>
                                                @endif

                                                <!-- RAW JSON TOGGLE -->
                                                <details class="bg-slate-900 text-emerald-300 p-4 rounded-2xl text-xs font-mono">
                                                    <summary class="cursor-pointer font-bold text-slate-300 hover:text-white uppercase tracking-wider text-[11px]">
                                                        <i class="fas fa-code me-1"></i> Ver Código JSON Bruto (Modo Desenvolvedor)
                                                    </summary>
                                                    <pre class="mt-3 overflow-x-auto text-[11px] leading-relaxed text-emerald-400">@json($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                                                </details>
                                            </div>

                                            <div class="bg-slate-100 dark:bg-slate-800 px-6 py-4 flex justify-end">
                                                <button type="button" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition" data-bs-dismiss="modal">
                                                    Fechar Janela
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                <i class="fas fa-shield-cat text-4xl mb-3 text-slate-300 block"></i>
                                Nenhum registo de auditoria encontrado com os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection