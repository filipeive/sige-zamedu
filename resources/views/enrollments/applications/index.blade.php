@extends('layouts.app')

@section('title', 'Pedidos de Matrícula')
@section('page-title', 'Pedidos de Matrícula')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item text-slate-400">Matrículas</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Pedidos de Matrícula</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header & Quick Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-list text-sm"></i>
                    </span>
                    Lista de Pedidos de Matrícula
                </h2>
                <p class="text-xs text-slate-500 mt-1">Pré-inscrições de novos ingressos e pedidos de renovação anual</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('enrollments.applications.index') }}"
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border {{ !request('status') && !request('type') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }} transition-colors">
                    Todos
                </a>
                <a href="{{ route('enrollments.applications.index', ['status' => 'PENDING']) }}"
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border {{ request('status') === 'PENDING' ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }} transition-colors">
                    <i class="fas fa-clock mr-1"></i> Pendentes
                </a>
                <a href="{{ route('enrollments.applications.index', ['type' => 'NEW']) }}"
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border {{ request('type') === 'NEW' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} transition-colors">
                    <i class="fas fa-user-plus mr-1"></i> Novos Ingressos
                </a>
                <a href="{{ route('enrollments.applications.index', ['type' => 'RENEWAL']) }}"
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border {{ request('type') === 'RENEWAL' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' }} transition-colors">
                    <i class="fas fa-sync mr-1"></i> Renovações
                </a>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Data</th>
                        <th class="py-3.5 px-4">Tipo</th>
                        <th class="py-3.5 px-4">Aluno</th>
                        <th class="py-3.5 px-4">Encarregado</th>
                        <th class="py-3.5 px-4 text-right">Valor</th>
                        <th class="py-3.5 px-4 text-center">Pagamento</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4 font-medium text-slate-700 whitespace-nowrap">
                                <div>{{ $app->submitted_at ? $app->submitted_at->format('d/m/Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $app->submitted_at ? $app->submitted_at->format('H:i') : '' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($app->type === 'NEW')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-user-plus text-[10px]"></i> Novo Ingresso
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-sync text-[10px]"></i> Renovação
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm">
                                    {{ $app->student_data['first_name'] ?? '' }} {{ $app->student_data['last_name'] ?? '' }}
                                </div>
                                @if($app->type === 'RENEWAL' && $app->student_id)
                                    <div class="text-[10px] text-slate-400">ID Aluno: #{{ $app->student_id }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-700">
                                    {{ $app->parent_data['first_name'] ?? '' }} {{ $app->parent_data['last_name'] ?? '' }}
                                </div>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-phone text-[9px]"></i>
                                    {{ $app->parent_data['phone'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                                {{ number_format($app->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($app->payment_status === 'PAID')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-check text-[9px]"></i> Pago
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-clock text-[9px]"></i> Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @php
                                    $statusMap = [
                                        'PENDING' => ['label' => 'Pendente', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                                        'IN_REVIEW' => ['label' => 'Em Análise', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                                        'DOCUMENT_DELIVERED' => ['label' => 'Docs Entregues', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                                        'APPROVED' => ['label' => 'Aprovado', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                                        'REJECTED' => ['label' => 'Rejeitado', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
                                        'ENROLLED' => ['label' => 'Matriculado', 'bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'border' => 'border-slate-300'],
                                    ];
                                    $st = $statusMap[$app->status] ?? ['label' => $app->status, 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $st['bg'] }} {{ $st['text'] }} border {{ $st['border'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('enrollments.applications.show', $app->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition-colors font-semibold text-xs shadow-2xs">
                                    <i class="fas fa-eye text-xs"></i>
                                    Analisar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-inbox text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">Nenhum pedido de matrícula encontrado.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Novas candidaturas ou renovações aparecerão aqui.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($applications, 'links') && $applications->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
