@extends('layouts.app')

@section('title', 'Detalhe da Licença')
@section('page-title', 'Detalhe da Licença')
@section('page-title-icon', 'fas fa-calendar-minus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff-leave-requests.index') }}">Licenças</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Detalhe</li>
@endsection

@section('title-icon', 'fas fa-calendar-check')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700"><i class="fas fa-file-alt mr-2"></i>Pedido #{{ $leaveRequest->id }}</h3>
                    @if ($leaveRequest->status === 'approved')
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aprovada</span>
                    @elseif($leaveRequest->status === 'rejected')
                        <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">Rejeitada</span>
                    @else
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Pendente</span>
                    @endif
                </div>
                <div class="p-4 space-y-4 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Professor</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->staff?->full_name ?? 'N/A' }}</div>
                            <small class="text-slate-500">{{ $leaveRequest->staff?->email }}</small>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Tipo de licença</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->leave_type_name }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Início</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->start_date?->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Fim</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->end_date?->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Dias solicitados</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->days_requested }} dia(s)</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Motivo</div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-slate-700">
                            {{ $leaveRequest->reason ?: 'Sem descrição.' }}
                        </div>
                    </div>

                    @if ($leaveRequest->status === 'rejected' && $leaveRequest->rejection_reason)
                        <div>
                            <div class="text-xs uppercase tracking-wide text-rose-600 mb-1">Motivo da rejeição</div>
                            <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-rose-700">
                                {{ $leaveRequest->rejection_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700"><i class="fas fa-history mr-2"></i>Trilha de Decisão</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">Solicitado em</div>
                        <div class="font-semibold text-slate-900">{{ $leaveRequest->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if ($leaveRequest->approved_at)
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Analisado em</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->approved_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500">Analisado por</div>
                            <div class="font-semibold text-slate-900">{{ $leaveRequest->approvedBy?->name ?? 'Não identificado' }}</div>
                        </div>
                    @else
                        <div class="text-slate-500">Ainda não analisado.</div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700"><i class="fas fa-clipboard-check mr-2"></i>Auditoria</h3>
                </div>
                <div class="p-4 space-y-2">
                    @forelse ($activities as $activity)
                        <div class="rounded-lg border border-slate-200 p-2">
                            <div class="text-xs text-slate-500">
                                {{ $activity->created_at->format('d/m/Y H:i') }}
                                • {{ $activity->causer?->name ?? 'Sistema' }}
                            </div>
                            <div class="font-semibold text-sm text-slate-800">{{ ucfirst(str_replace('_', ' ', $activity->description)) }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Sem eventos de auditoria.</div>
                    @endforelse
                </div>
            </div>

            @if ($leaveRequest->status === 'pending')
                @can('approve_leave_requests')
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-4 py-3">
                            <h3 class="text-sm font-semibold text-slate-700"><i class="fas fa-gavel mr-2"></i>Ações</h3>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('staff-leave-requests.approve', $leaveRequest) }}"
                                class="mb-3">
                                @csrf
                                <button
                                    class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                    <i class="fas fa-check mr-2"></i>Aprovar Pedido
                                </button>
                            </form>

                            <form method="POST" action="{{ route('staff-leave-requests.reject', $leaveRequest) }}">
                                @csrf
                                <label class="block text-sm font-medium text-slate-700 mb-1">Motivo da rejeição</label>
                                <textarea name="rejection_reason" rows="3"
                                    class="mb-2 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                    placeholder="Descreva o motivo da rejeição..."></textarea>
                                <button
                                    class="inline-flex w-full items-center justify-center rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                                    <i class="fas fa-times mr-2"></i>Rejeitar Pedido
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            @endif
        </div>
    </div>
@endsection
