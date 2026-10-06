@extends('layouts.app')

@section('title', 'Painel da Secretaria')
@section('page-title', 'Painel da Secretaria')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Painel da Secretaria</li>
@endsection

@section('content')
<div class="w-full mx-auto px-4 py-6 space-y-6">

    <!-- Header Contextual -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1">{{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}</p>
            <!-- Informação da escola e ano lectivo -->
            <p class="text-sm text-slate-500 mt-1">Bem-vindo ao painel da secretaria. Aqui você pode gerir alunos, matrículas, pagamentos e muito mais.</p>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl inline-block">{{ $greetingData['current_date'] }}</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Alunos</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">
                    <i class="fas fa-arrow-trend-up"></i> {{ $stats['new_enrollments_month'] }} este mês
                </span>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fas fa-user-graduate text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Matrículas Pendentes</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['pending_enrollments']) }}</p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    Aguardando revisão
                </span>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>

        @if(is_private_school())
        <div class="bg-gradient-to-br from-teal-700 to-teal-900 rounded-2xl p-5 shadow-md text-white flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-teal-200">Receita Mensal</p>
                <p class="text-2xl font-black text-white mt-1">{{ number_format($stats['monthly_revenue'], 0, ',', '.') }} <span class="text-xs font-semibold text-teal-200">MT</span></p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-medium backdrop-blur-md">
                    {{ $stats['todays_payments'] }} pagamentos hoje
                </span>
            </div>
            <div class="p-3 bg-white/20 text-white rounded-xl backdrop-blur-md">
                <i class="fas fa-wallet text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Propinas em Atraso</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['overdue_payments']) }}</p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold">
                    {{ number_format($stats['overdue_amount'], 0, ',', '.') }} MT
                </span>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
        </div>
        @endif
    </div>

    <!-- Ações Rápidas -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-1">Ações Rápidas</h2>
        <p class="text-xs text-slate-500 mb-4">Acesso directo às tarefas e formulários mais frequentes</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <a href="{{ route('students.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-slate-700 hover:text-emerald-700 transition group">
                <i class="fas fa-user-plus text-xl text-slate-500 group-hover:text-emerald-600 mb-2"></i>
                <span class="text-xs font-bold">Novo Aluno</span>
            </a>
            @if(is_private_school())
            <a href="{{ route('payments.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-slate-700 hover:text-emerald-700 transition group">
                <i class="fas fa-money-bill-wave text-xl text-slate-500 group-hover:text-emerald-600 mb-2"></i>
                <span class="text-xs font-bold">Pagamento</span>
            </a>
            @endif
            <a href="{{ route('enrollments.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-slate-700 hover:text-emerald-700 transition group">
                <i class="fas fa-id-card text-xl text-slate-500 group-hover:text-emerald-600 mb-2"></i>
                <span class="text-xs font-bold">Matrícula</span>
            </a>
            <a href="{{ route('communications.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 text-slate-700 hover:text-emerald-700 transition group">
                <i class="fas fa-bullhorn text-xl text-slate-500 group-hover:text-emerald-600 mb-2"></i>
                <span class="text-xs font-bold">Comunicado</span>
            </a>
        </div>
    </div>

    @if(is_private_school())
    <!-- Tabelas de Pagamentos -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Últimos Pagamentos</h2>
                    <p class="text-xs text-slate-500">Recebidos recentemente</p>
                </div>
                <a href="{{ route('payments.index') }}" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Histórico</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Aluno</th>
                            <th class="py-3 px-4">Data</th>
                            <th class="py-3 px-6 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($recentPayments as $payment)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-6 font-bold text-slate-900">{{ $payment->student->full_name }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $payment->payment_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-right font-bold text-emerald-600">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-6 text-slate-400 font-medium">Nenhum pagamento registado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Pagamentos em Atraso</h2>
                    <p class="text-xs text-slate-500">Requerem acção da secretaria</p>
                </div>
                <a href="{{ route('payments.index') }}?status=overdue" class="px-3 py-1 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition">Ver todos</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Aluno</th>
                            <th class="py-3 px-4">Vencimento</th>
                            <th class="py-3 px-6 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($overduePayments as $payment)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-6 font-bold text-slate-900">{{ $payment->student->full_name }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-right font-bold text-rose-600">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-6 text-slate-400 font-medium">Nenhum pagamento em atraso.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Matrículas Pendentes -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Pré-Matrículas Pendentes</h2>
                <p class="text-xs text-slate-500">Aguardando aprovação da secretaria</p>
            </div>
            <a href="{{ route('enrollments.index') }}?status=pending" class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Aluno</th>
                        <th class="py-3 px-4">Turma</th>
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-6 text-right">Acção</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($pendingEnrollments as $enrollment)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-6 font-bold text-slate-900">{{ $enrollment->student->full_name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $enrollment->class->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $enrollment->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-right">
                                <a href="{{ route('enrollments.show', $enrollment) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 font-semibold transition">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-6 text-slate-400 font-medium">Nenhuma matrícula pendente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
