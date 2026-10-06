@extends('layouts.app')

@section('title', 'Detalhes da Matrícula')
@section('page-title', 'Detalhes da Matrícula')
@section('page-title-icon', 'fas fa-file-signature')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}" class="no-underline">Matrículas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Detalhes</li>
@endsection

@section('title', 'Ficha de Matrícula - ' . $enrollment->student->full_name)
@section('content')
<div class="max-w-full mx-auto space-y-4" x-data="{ transferOpen: false }">

    <!-- 1. Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center font-bold text-lg shadow-md shrink-0">
                @if($enrollment->student->passport_photo)
                    <img src="{{ Storage::url($enrollment->student->passport_photo) }}" alt="{{ $enrollment->student->full_name }}" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ strtoupper(substr($enrollment->student->first_name, 0, 1) . substr($enrollment->student->last_name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black text-slate-800 tracking-tight">
                        {{ $enrollment->student->full_name }}
                    </h1>
                    @php
                        $statusClasses = [
                            'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'transferred' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'completed' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'suspended' => 'bg-slate-800 text-white border-slate-900',
                            'pending_renewal' => 'bg-amber-100 text-amber-800 border-amber-200',
                        ];
                        $statusLabels = [
                            'active' => 'Ativa',
                            'inactive' => 'Inativa',
                            'transferred' => 'Transferida',
                            'pending' => 'Pendente',
                            'cancelled' => 'Cancelada',
                            'completed' => 'Concluída',
                            'suspended' => 'Suspensa',
                            'pending_renewal' => 'Pendente Renovação'
                        ];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border shadow-2xs {{ $statusClasses[$enrollment->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $statusLabels[$enrollment->status] ?? ucfirst($enrollment->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Matrícula #<span class="font-mono font-bold text-slate-700">{{ $enrollment->id }}</span> · Turma <strong class="text-slate-800">{{ $enrollment->class->name }}</strong> (Ano Letivo {{ $enrollment->school_year }})
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('edit_enrollments')
                <a href="{{ route('enrollments.edit', $enrollment->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('enrollments.print', $enrollment->id) }}" target="_blank" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-print text-blue-600"></i> Imprimir
            </a>
            <a href="{{ route('enrollments.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- 2. Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left & Center Column (2/3): Details & Financial (Private Only) -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Dados do Estudante e da Matrícula -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-graduate text-emerald-600"></i> Dados da Matrícula & Estudante
                    </h3>
                    <a href="{{ route('students.show', $enrollment->student_id) }}" class="no-underline text-[11px] font-bold text-emerald-700 hover:underline">
                        Ver Perfil do Aluno <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <!-- Coluna Aluno -->
                    <div class="space-y-2.5">
                        <h4 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-1">Identificação do Aluno</h4>
                        <div class="space-y-1.5">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Nome Completo:</span>
                                <span class="font-bold text-slate-800">{{ $enrollment->student->full_name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Nº de Estudante:</span>
                                <span class="font-mono font-bold text-slate-700">{{ $enrollment->student->student_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Gênero:</span>
                                <span class="font-semibold text-slate-700">{{ $enrollment->student->gender == 'male' ? 'Masculino' : 'Feminino' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Data Nasc. / Idade:</span>
                                <span class="font-semibold text-slate-700">
                                    {{ $enrollment->student->birthdate ? $enrollment->student->birthdate->format('d/m/Y') . ' (' . $enrollment->student->age . ' anos)' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500 font-semibold">Encarregado:</span>
                                <span class="font-bold text-slate-800">
                                    @if($enrollment->student->parent)
                                        {{ $enrollment->student->parent->full_name }}
                                    @else
                                        <span class="text-slate-400 font-normal">Não associado</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Matrícula -->
                    <div class="space-y-2.5">
                        <h4 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-1">Dados de Inscrição</h4>
                        <div class="space-y-1.5">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Turma Atribuída:</span>
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">{{ $enrollment->class->name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Nível / Gração:</span>
                                <span class="font-semibold text-slate-700">{{ $enrollment->class->grade_level_name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Ano Lectivo:</span>
                                <span class="font-bold text-slate-800">{{ $enrollment->school_year }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 font-semibold">Data da Matrícula:</span>
                                <span class="font-semibold text-slate-700">{{ $enrollment->enrollment_date?->format('d/m/Y') ?? 'N/A' }}</span>
                            </div>
                            @if ($enrollment->cancellation_date)
                                <div class="flex justify-between py-1 text-rose-700">
                                    <span class="font-semibold">Data Cancelamento:</span>
                                    <span class="font-bold">{{ $enrollment->cancellation_date?->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($enrollment->observations)
                    <div class="px-5 py-3 bg-slate-50/60 border-t border-slate-100 text-xs">
                        <span class="font-bold text-slate-700 block mb-0.5">Observações da Matrícula:</span>
                        <p class="text-slate-600 leading-relaxed">{{ $enrollment->observations }}</p>
                    </div>
                @endif
            </div>

            <!-- Informações Financeiras & Pagamentos (APENAS ESCOLA PRIVADA) -->
            @if(is_private_school())
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden space-y-0">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-coins text-amber-500"></i> Informações Financeiras & Propinas
                        </h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">Escola Privada</span>
                    </div>

                    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs bg-white">
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/40">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Mensalidade Fixa</span>
                            <span class="text-base font-black text-slate-800">{{ number_format($enrollment->monthly_fee, 2, ',', '.') }} MT</span>
                        </div>
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/40">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dia Limite de Pagamento</span>
                            <span class="text-base font-black text-slate-800">Dia {{ $enrollment->payment_day ?? 5 }}</span>
                        </div>
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/40">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Taxa de Inscrição</span>
                            @if ($enrollmentPayment)
                                <span class="text-sm font-black text-slate-800">{{ number_format($enrollmentPayment->amount, 2, ',', '.') }} MT</span>
                                <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $enrollmentPayment->status == 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $enrollmentPayment->status == 'paid' ? 'Pago' : 'Pendente' }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-slate-400">Sem taxa registada</span>
                            @endif
                        </div>
                    </div>

                    <!-- Tabela de Pagamentos Relacionados -->
                    @if ($enrollment->payments && $enrollment->payments->count() > 0)
                        <div class="border-t border-slate-100">
                            <div class="px-5 py-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-700">Histórico de Recibos / Pagamentos ({{ $enrollment->payments->count() }})</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100/70 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                            <th class="py-2.5 px-4">Referência</th>
                                            <th class="py-2.5 px-3">Tipo</th>
                                            <th class="py-2.5 px-3">Valor</th>
                                            <th class="py-2.5 px-3">Vencimento</th>
                                            <th class="py-2.5 px-3">Estado</th>
                                            <th class="py-2.5 px-4 text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($enrollment->payments as $payment)
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-2.5 px-4 font-mono font-bold text-slate-800">{{ $payment->reference_number }}</td>
                                                <td class="py-2.5 px-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $payment->type == 'matricula' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                                        {{ ucfirst($payment->type) }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 font-bold text-slate-800">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                                                <td class="py-2.5 px-3 text-slate-600">{{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                                <td class="py-2.5 px-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $payment->status == 'paid' ? 'bg-emerald-100 text-emerald-800' : ($payment->status == 'overdue' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                                        {{ $payment->status == 'paid' ? 'Pago' : ($payment->status == 'overdue' ? 'Atrasado' : 'Pendente') }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-4 text-end">
                                                    <a href="{{ route('payments.show', $payment->id) }}" class="no-underline h-7 w-7 inline-flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="Ver Detalhes">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Right Column (1/3): Sidebar Actions Panel -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fas fa-sliders-h text-emerald-600"></i> Painel de Ações
                </h3>

                <div class="space-y-2">
                    @can('edit_enrollments')
                        <a href="{{ route('enrollments.edit', $enrollment->id) }}" class="no-underline w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition">
                            <i class="fas fa-edit"></i> Editar Dados da Matrícula
                        </a>
                    @endcan

                    <a href="{{ route('students.show', $enrollment->student_id) }}" class="no-underline w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-xs font-bold text-white shadow-2xs transition">
                        <i class="fas fa-user-graduate"></i> Ver Perfil Completo do Aluno
                    </a>

                    <a href="{{ route('enrollments.print', $enrollment->id) }}" target="_blank" class="no-underline w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                        <i class="fas fa-print text-blue-600"></i> Imprimir Comprovativo
                    </a>

                    <!-- Status Action Buttons -->
                    @if ($enrollment->status == 'active')
                        @can('edit_enrollments')
                            <button type="button" @click="transferOpen = true" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                <i class="fas fa-exchange-alt"></i> Transferir de Turma
                            </button>

                            <form action="{{ route('enrollments.cancel', $enrollment->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta matrícula?')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                    <i class="fas fa-ban"></i> Cancelar Matrícula
                                </button>
                            </form>
                        @endcan
                    @elseif($enrollment->status == 'cancelled')
                        @can('edit_enrollments')
                            <form action="{{ route('enrollments.activate', $enrollment->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja reativar esta matrícula?')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                    <i class="fas fa-check-circle"></i> Reativar Matrícula
                                </button>
                            </form>
                        @endcan
                    @elseif($enrollment->status == 'pending')
                        @can('edit_enrollments')
                            @if (is_private_school() && $enrollmentPayment && $enrollmentPayment->status == 'pending')
                                <form action="{{ route('enrollments.confirm-payment', $enrollment->id) }}" method="POST" onsubmit="return confirm('Confirmar pagamento da taxa e ativar matrícula?')">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                        <i class="fas fa-check-double"></i> Confirmar Taxa & Ativar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('enrollments.activate', $enrollment->id) }}" method="POST" onsubmit="return confirm('Ativar esta matrícula agora?')">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                                        <i class="fas fa-check-circle"></i> Ativar Matrícula
                                    </button>
                                </form>
                            @endif
                        @endcan
                    @endif
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-[11px] text-slate-500">
                <div class="flex justify-between">
                    <span>Criado em:</span>
                    <span class="font-semibold text-slate-700">{{ $enrollment->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Última atualização:</span>
                    <span class="font-semibold text-slate-700">{{ $enrollment->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Transfer Modal (Teleported to Body) -->
    @can('edit_enrollments')
    <template x-teleport="body">
        <div x-show="transferOpen" x-transition.opacity
             style="position: fixed; inset: 0; z-index: 999999 !important; display: none;"
             class="flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100" @click.outside="transferOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-indigo-600"></i> Transferir Aluno de Turma
                    </h3>
                    <button type="button" @click="transferOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <form action="{{ route('enrollments.transfer', $enrollment->id) }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nova Turma de Destino *</label>
                        <select name="new_class_id" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione a nova turma...</option>
                            @foreach (\App\Models\ClassRoom::all() as $class)
                                @if ($class->id != $enrollment->class_id)
                                    <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level_name }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Data da Transferência *</label>
                        <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if(is_private_school())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Taxa de Transferência (MT)</label>
                            <input type="number" step="0.01" name="transfer_fee" value="0.00" min="0" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-[10px] text-slate-400">Deixe 0.00 se não houver cobrança adicional</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="transferOpen = false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-2xs transition">Confirmar Transferência</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endcan

</div>
@endsection
