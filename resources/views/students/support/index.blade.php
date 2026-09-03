@extends('layouts.app')

@section('title', 'Acompanhamento - ' . $student->full_name)
@section('page-title', 'Acompanhamento do Aluno')
@section('title-icon', 'fas fa-notes-medical')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->full_name }}</a></li>
    <li class="breadcrumb-item active">Acompanhamento</li>
@endsection

@section('content')
<div class="space-y-4">

    {{-- 1. Header Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-notes-medical text-emerald-600"></i> Acompanhamento
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Observações pedagógicas, registos e ocorrências do aluno.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.show', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left text-slate-500"></i> Volper ao Perfil
            </a>
            @if (!in_array($student->status, ['transferred', 'graduated', 'inactive']))
                @can('manage_student_records')
                    <button type="button" data-bs-toggle="modal" data-bs-target="#markExitModal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white shadow-sm transition">
                        <i class="fas fa-door-open"></i> Marcar Saída do Aluno
                    </button>
                @endcan
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-bold text-slate-600">
                    <i class="fas fa-archive"></i> Aluno Arquivado
                </span>
            @endif

            @if ($student->parent && $student->parent->phone)
                <button type="button" data-bs-toggle="modal" data-bs-target="#smsModalSupport"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-sky-200 bg-sky-50 hover:bg-sky-100 text-xs font-bold text-sky-700 shadow-2xs transition">
                    <i class="fas fa-comment-dots"></i> Enviar SMS ao Encarregado
                </button>
            @endif
        </div>
    </div>

    {{-- 2. Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Aluno</span>
                <span class="text-sm font-black text-slate-800 mt-0.5 block truncate" title="{{ $student->full_name }}">{{ $student->full_name }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-comments"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Observações</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $observations->total() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-folder-open"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Registos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $records->total() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full
                {{ $student->status === 'active' ? 'bg-emerald-500' :
                  ($student->status === 'pending_renewal' ? 'bg-amber-500' :
                  (in_array($student->status, ['transferred','graduated','inactive']) ? 'bg-slate-500' : 'bg-rose-500')) }} shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-slate-50 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Estado Atual</span>
                <span class="text-sm font-black text-slate-800 mt-0.5 block">
                    @php
                        $statusLabels = [
                            'active' => 'Ativo',
                            'pending_renewal' => 'Renovação',
                            'inactive' => 'Inativo',
                            'transferred' => 'Transferido',
                            'graduated' => 'Formado',
                        ];
                    @endphp
                    {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- 3. Two Columns: Forms + Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        {{-- LEFT: Forms --}}
        <div class="lg:col-span-5 space-y-4">
            @can('create_observations')
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/60">
                        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-comment-medical text-blue-600"></i> Nova Observação
                        </h3>
                    </div>
                    <div class="p-4">
                        <form method="POST" action="{{ route('students.support.observations.store', $student) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Observação</label>
                                <textarea class="w-full rounded-lg border-slate-200 text-xs shadow-xs focus:border-blue-500 focus:ring-blue-500 @error('observations') border-rose-400 @enderror"
                                    name="observations" rows="4"
                                    placeholder="Registre observações pedagógicas, comportamentais ou de acompanhamento...">{{ old('observations') }}</textarea>
                                @error('observations')
                                    <div class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                <input class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" type="checkbox" value="1" name="special_needs"
                                    id="special_needs" {{ old('special_needs') ? 'checked' : '' }}>
                                <label class="text-xs text-slate-700 font-semibold" for="special_needs">
                                    Envolve necessidade especial
                                </label>
                            </div>
                            <button class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-save"></i> Guardar Observação
                            </button>
                        </form>
                    </div>
                </div>
            @endcan

            @can('manage_student_records')
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/60">
                        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <i class="fas fa-folder-plus text-emerald-600"></i> Novo Registo
                        </h3>
                    </div>
                    <div class="p-4">
                        <form method="POST" action="{{ route('students.support.records.store', $student) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tipo</label>
                                <select class="w-full rounded-lg border-slate-200 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500 @error('record_type') border-rose-400 @enderror" name="record_type">
                                    <option value="">Selecionar...</option>
                                    <option value="academic" @selected(old('record_type') === 'academic')>Académico</option>
                                    <option value="disciplinary" @selected(old('record_type') === 'disciplinary')>Disciplinar</option>
                                    <option value="health" @selected(old('record_type') === 'health')>Saúde</option>
                                    <option value="achievement" @selected(old('record_type') === 'achievement')>Conquista</option>
                                    <option value="other" @selected(old('record_type') === 'other')>Outro</option>
                                </select>
                                @error('record_type')
                                    <div class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Data do registo</label>
                                <input type="date" class="w-full rounded-lg border-slate-200 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500 @error('record_date') border-rose-400 @enderror"
                                    name="record_date" value="{{ old('record_date', now()->toDateString()) }}">
                                @error('record_date')
                                    <div class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Detalhes</label>
                                <textarea class="w-full rounded-lg border-slate-200 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500 @error('record_details') border-rose-400 @enderror"
                                    name="record_details" rows="4"
                                    placeholder="Descrição do evento/registo...">{{ old('record_details') }}</textarea>
                                @error('record_details')
                                    <div class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 px-3 py-2 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-save"></i> Guardar Registo
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>

        {{-- RIGHT: Lists --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-comments text-blue-600"></i> Observações
                    </h3>
                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-extrabold text-blue-700">{{ $observations->total() }}</span>
                </div>
                <div class="p-4">
                    @forelse ($observations as $observation)
                        <div class="rounded-lg border border-slate-200 p-3 mb-3 hover:bg-slate-50/60 transition">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <small class="text-[11px] text-slate-500 font-semibold">
                                    {{ $observation->created_at->format('d/m/Y H:i') }} · {{ $observation->creator->name ?? 'Sistema' }}
                                </small>
                                @if ($observation->special_needs)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-extrabold text-amber-700">Nec. especial</span>
                                @endif
                            </div>
                            <p class="mb-2 text-xs text-slate-700 leading-relaxed">{{ $observation->observations }}</p>
                            @can('manage_observations')
                                <form method="POST"
                                    action="{{ route('students.support.observations.destroy', [$student, $observation]) }}"
                                    data-confirm="Remover esta observação?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1 rounded-md border border-rose-300 px-2.5 py-1 text-[11px] font-bold text-rose-700 hover:bg-rose-50 transition">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-500">
                            <i class="fas fa-comment-slash fa-2x text-slate-300 mb-2"></i>
                            <p class="mb-0">Nenhuma observação registada.</p>
                        </div>
                    @endforelse
                    <div class="mt-3">{{ $observations->links() }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <i class="fas fa-folder-open text-emerald-600"></i> Registos / Ocorrências
                    </h3>
                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-700">{{ $records->total() }}</span>
                </div>
                <div class="p-4">
                    @forelse ($records as $record)
                        <div class="rounded-lg border border-slate-200 p-3 mb-3 hover:bg-slate-50/60 transition">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <small class="text-[11px] text-slate-500 font-semibold">
                                    {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }} · {{ $record->creator->name ?? 'Sistema' }}
                                </small>
                                <span class="inline-flex rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-extrabold text-cyan-700">{{ $record->record_type_name }}</span>
                            </div>
                            <p class="mb-2 text-xs text-slate-700 leading-relaxed">{{ $record->record_details }}</p>
                            @can('manage_student_records')
                                <form method="POST" action="{{ route('students.support.records.destroy', [$student, $record]) }}"
                                    data-confirm="Remover este registo?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1 rounded-md border border-rose-300 px-2.5 py-1 text-[11px] font-bold text-rose-700 hover:bg-rose-50 transition">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-500">
                            <i class="fas fa-folder-open fa-2x text-slate-300 mb-2"></i>
                            <p class="mb-0">Nenhum registo encontrado.</p>
                        </div>
                    @endforelse
                    <div class="mt-3">{{ $records->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Marcar Saída do Aluno --}}
@can('manage_student_records')
    @if (!in_array($student->status, ['transferred', 'graduated', 'inactive']))
        <div class="modal fade" id="markExitModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
                    <form method="POST" action="{{ route('students.support.mark-exit', $student) }}">
                        @csrf
                        <div class="modal-header bg-rose-600 text-white" style="border-radius:1rem 1rem 0 0;">
                            <h5 class="modal-title font-bold">
                                <i class="fas fa-door-open me-2"></i> Marcar Saída do Aluno
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-warning border-0 rounded-xl mb-3 d-flex align-items-start gap-2">
                                <i class="fas fa-exclamation-triangle mt-1"></i>
                                <div class="text-xs">
                                    Esta acção é <strong>irreversível</strong>. O aluno será movido para <strong>Alunos Arquivados</strong> e o motivo será registado no histórico de ocorrências.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Aluno</label>
                                <input type="text" class="form-control rounded-xl bg-slate-50" value="{{ $student->full_name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Tipo de Saída *</label>
                                <select name="exit_status" class="form-select rounded-xl" required>
                                    <option value="">Selecionar...</option>
                                    <option value="transferred">Transferido (mudou de escola)</option>
                                    <option value="graduated">Formado (concluiu o ciclo)</option>
                                    <option value="inactive">Desistente (abandono / desistência)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Data da Saída *</label>
                                <input type="date" name="exit_date" class="form-control rounded-xl"
                                    value="{{ now()->toDateString() }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Motivo / Observações *</label>
                                <textarea name="exit_reason" class="form-control rounded-xl" rows="3"
                                    placeholder="Descreva o motivo da saída (será arquivado no histórico)..." required></textarea>
                            </div>
                        </div>
<div class="modal-footer bg-light py-2 px-4" style="border-radius:0 0 1rem 1rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-xl" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-rose btn-sm rounded-xl font-semibold text-white" style="background-color:#e11d48;">
                        <i class="fas fa-check me-1"></i> Confirmar Saída
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endif
@endcan

{{-- SMS ao Encarregado --}}
@if ($student->parent && $student->parent->phone)
<div class="modal fade" id="smsModalSupport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <form action="{{ route('students.sms.send', $student) }}" method="POST">
                @csrf
                <div class="modal-header bg-sky-600 text-white" style="border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title font-bold">
                        <i class="fas fa-comment-dots me-2"></i> Enviar SMS ao Encarregado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="alert alert-info border-0 rounded-xl mb-3 d-flex align-items-center gap-2 text-xs">
                        <i class="fas fa-user"></i>
                        <div>
                            <strong>{{ $student->parent->first_name }} {{ $student->parent->last_name }}</strong><br>
                            <span class="font-mono">{{ $student->parent->phone }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Mensagem *</label>
                        <textarea id="smsMsgSupport" name="message" rows="5" required maxlength="480" class="form-control rounded-xl"
                            placeholder="Use {aluno} para o nome do aluno, {escola} para o nome da escola..."></textarea>
                        <small class="text-slate-500 text-[11px]">Máx. 480 caracteres. Enviado via httpSMS.</small>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        @php
                            $templates = [
                                "Bom dia. Gostaríamos de informar que {aluno} faltou hoje às aulas. Pedimos contacto com a secretaria.",
                                "Exmo(a) encarregado(a), informamos que {aluno} tem propinas em atraso. Pedimos regularização.",
                                "Comunicamos que a reunião de pais da turma de {aluno} será brevemente.",
                                "Exmo(a), {aluno} recebeu uma observação positiva pelo bom comportamento.",
                                "Pedimos a comparência do encarregado de {aluno} na secretaria da {escola}.",
                            ];
                        @endphp
                        @foreach ($templates as $tpl)
                            <button type="button" class="badge bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer hover:bg-slate-200"
                                onclick="document.getElementById('smsMsgSupport').value = @js($tpl);">
                                {{ mb_substr($tpl, 0, 35) }}…
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4" style="border-radius:0 0 1rem 1rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-xl" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sky-600 btn-sm rounded-xl font-semibold text-white" style="background-color:#0284c7;">
                        <i class="fas fa-paper-plane me-1"></i> Enviar SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection