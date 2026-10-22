@extends('layouts.app')

@section('title', 'Minhas Licenças')
@section('page-title', 'Minhas Licenças')
@section('page-title-icon', 'fas fa-calendar-times')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Licenças</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-1">
                    <i class="fas fa-calendar-times"></i> Recursos Humanos
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Minhas Licenças e Ausências</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Histórico de dispensas, licenças de saúde e solicitações à direção
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.leave-requests.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition-all">
                    <i class="fas fa-plus"></i>
                    Nova Solicitação
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-blue-500">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pedidos</div>
            <div class="text-2xl font-bold font-mono text-slate-800 mt-1">{{ $statistics['total'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-amber-500">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Em Análise</div>
            <div class="text-2xl font-bold font-mono text-amber-600 mt-1">{{ $statistics['pending'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-emerald-500">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aprovados</div>
            <div class="text-2xl font-bold font-mono text-emerald-600 mt-1">{{ $statistics['approved'] }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-rose-500">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rejeitados</div>
            <div class="text-2xl font-bold font-mono text-rose-600 mt-1">{{ $statistics['rejected'] }}</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-list text-xs"></i>
                </span>
                Histórico de Solicitações
            </h2>
        </div>

        @if($leaveRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Tipo de Licença</th>
                            <th class="py-3 px-4">Período</th>
                            <th class="py-3 px-4">Duração</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4">Data da Solicitação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($leaveRequests as $request)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800">{{ $request->leave_type }}</div>
                                    @if($request->reason)
                                        <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $request->reason }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                    <span class="text-slate-400 mx-1">&rarr;</span>
                                    {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-slate-800 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} dias
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($request->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check text-[9px]"></i> Aprovado
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-times text-[9px]"></i> Rejeitado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fas fa-clock text-[9px]"></i> Em Análise
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($leaveRequests, 'links') && $leaveRequests->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-calendar-times text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Nenhuma solicitação registada</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto mb-4">
                    Você ainda não submeteu nenhum pedido de licença ou dispensa.
                </p>
                <a href="{{ route('teacher.leave-requests.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition-colors">
                    <i class="fas fa-plus"></i>
                    Solicitar Licença
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
