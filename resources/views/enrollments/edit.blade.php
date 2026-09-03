@extends('layouts.app')

@section('title', 'Editar Matrícula')
@section('page-title', 'Editar Matrícula')
@section('page-title-icon', 'fas fa-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Matrículas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('enrollments.show', $enrollment->id) }}">Detalhes</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> Editar Matrícula #{{ $enrollment->id }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize os dados da matrícula do estudante {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}.</p>
        </div>
        <a href="{{ route('enrollments.show', $enrollment->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5">
        <form action="{{ route('enrollments.update', $enrollment->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <!-- Readonly info fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Aluno</label>
                    <div class="w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-800 py-2 px-3 text-xs font-semibold">
                        <span class="font-bold block text-slate-900">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</span>
                        <span class="text-[10px] text-slate-400 font-normal">Nº: {{ $enrollment->student->student_number ?? 'N/A' }} @if($enrollment->student->birthdate) | {{ $enrollment->student->age }} anos @endif</span>
                    </div>
                    <p class="mt-1 text-[10px] text-slate-400">Não é possível alterar o aluno. Crie uma nova matrícula se necessário.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ano Letivo</label>
                    <div class="w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-800 py-2 px-3 text-xs font-semibold">
                        <span class="font-bold text-slate-900">{{ $enrollment->school_year }}</span>
                    </div>
                    <p class="mt-1 text-[10px] text-slate-400">Ano letivo não pode ser alterado.</p>
                </div>
            </div>

            <!-- Editable fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Turma *</label>
                    <select name="class_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('class_id') border-rose-500 @enderror">
                        <option value="">Selecione a turma</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $enrollment->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('status') border-rose-500 @enderror">
                        <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>Ativa</option>
                        <option value="inactive" {{ old('status', $enrollment->status) == 'inactive' ? 'selected' : '' }}>Inativa</option>
                        <option value="cancelled" {{ old('status', $enrollment->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>  
                        <option value="pending" {{ old('status', $enrollment->status) == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="transferred" {{ old('status', $enrollment->status) == 'transferred' ? 'selected' : '' }}>Transferida</option>
                    </select>
                    @error('status')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @if(is_private_school())
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mensalidade (MZN) *</label>
                    <input type="number" step="0.01" name="monthly_fee" value="{{ old('monthly_fee', $enrollment->monthly_fee) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('monthly_fee') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Valor da mensalidade mensal</p>
                    @error('monthly_fee')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dia de Pagamento *</label>
                    <input type="number" name="payment_day" value="{{ old('payment_day', $enrollment->payment_day) }}" min="1" max="28" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('payment_day') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Dia do mês para pagamento</p>
                    @error('payment_day')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                @else
                    <input type="hidden" name="monthly_fee" value="0">
                    <input type="hidden" name="payment_day" value="10">
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data da Matrícula</label>
                    <input type="date" name="enrollment_date" value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}" readonly
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-800 py-2 px-3 text-xs font-semibold">
                    <p class="mt-1 text-[10px] text-slate-400">Data original não pode ser alterada</p>
                </div>
            </div>

            @if($enrollmentPayment || $enrollment->cancellation_date)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($enrollmentPayment)
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Taxa de Matrícula</label>
                    <div class="w-full rounded-lg border border-slate-200 bg-slate-50 p-2 text-xs flex items-center justify-between">
                        <span class="font-bold text-slate-800">{{ number_format($enrollmentPayment->amount, 2, ',', '.') }} MZN</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $enrollmentPayment->status == 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                            {{ $enrollmentPayment->status == 'paid' ? 'Pago' : 'Pendente' }}
                        </span>
                    </div>
                </div>
                @endif

                @if($enrollment->cancellation_date)
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data de Cancelamento</label>
                    <div class="w-full rounded-lg border border-slate-200 bg-slate-50 p-2 text-xs font-bold text-slate-800">
                        {{ $enrollment->cancellation_date->format('d/m/Y') }}
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Observações</label>
                <textarea name="observations" rows="3" placeholder="Observações sobre a matrícula..."
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('observations') border-rose-500 @enderror">{{ old('observations', $enrollment->observations) }}</textarea>
                @error('observations')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <!-- Audit Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-[10px] text-slate-400">
                <p><i class="fas fa-calendar-plus mr-1"></i> Criado em: {{ $enrollment->created_at->format('d/m/Y H:i') }}</p>
                <p class="sm:text-right"><i class="fas fa-calendar-check mr-1"></i> Última atualização: {{ $enrollment->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('enrollments.show', $enrollment->id) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Atualizar Matrícula
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Actions Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-bolt text-amber-500"></i> Ações Rápidas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <a href="{{ route('enrollments.print', $enrollment->id) }}" class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition" target="_blank">
                <i class="fas fa-print"></i> Imprimir
            </a>
            <a href="{{ route('students.show', $enrollment->student_id) }}" class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                <i class="fas fa-user-graduate"></i> Ver Aluno
            </a>
            @if($enrollmentPayment)
            <a href="{{ route('payments.show', $enrollmentPayment->id) }}" class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                <i class="fas fa-money-bill-wave"></i> Ver Pagamento
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
