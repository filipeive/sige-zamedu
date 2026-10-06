@extends('layouts.app')

@section('title', 'Nova Matrícula')
@section('page-title', 'Nova Matrícula')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Matrículas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Nova Matrícula</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-plus text-emerald-600"></i> Nova Matrícula
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Registo de nova matrícula escolar de estudante em turma do ano letivo.</p>
        </div>
        <a href="{{ route('enrollments.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5">
        <form action="{{ route('enrollments.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Aluno *</label>
                    <select name="student_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('student_id') border-rose-500 @enderror">
                        <option value="">Selecione o aluno</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->student_number }} - {{ $student->first_name }} {{ $student->last_name }}
                                @if($student->birthdate) ({{ $student->age }} anos) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                    @if($students->isEmpty())
                        <p class="mt-1 text-[10px] text-amber-600 font-semibold">
                            <i class="fas fa-exclamation-triangle"></i> Todos os alunos já possuem matrícula ativa para o ano corrente.
                            <a href="{{ route('students.create') }}" class="underline font-bold">Cadastrar novo aluno</a>
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Turma *</label>
                    <select name="class_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('class_id') border-rose-500 @enderror">
                        <option value="">Selecione a turma</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ano Letivo *</label>
                    <input type="number" name="school_year" value="{{ old('school_year', $currentYear) }}" min="2020" max="2030" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('school_year') border-rose-500 @enderror">
                    @error('school_year')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data da Matrícula *</label>
                    <input type="date" name="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('enrollment_date') border-rose-500 @enderror">
                    @error('enrollment_date')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                @if(is_private_school())
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dia de Pagamento *</label>
                    <input type="number" name="payment_day" value="{{ old('payment_day', 10) }}" min="1" max="28" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('payment_day') border-rose-500 @enderror">
                    @error('payment_day')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                @else
                    <input type="hidden" name="monthly_fee" value="0">
                    <input type="hidden" name="payment_day" value="10">
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if(is_private_school())
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mensalidade (MZN) *</label>
                    <input type="number" step="0.01" name="monthly_fee" value="{{ old('monthly_fee', 2500.00) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('monthly_fee') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Valor que o aluno pagará mensalmente</p>
                    @error('monthly_fee')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Taxa de Matrícula (MZN) {{ is_private_school() ? '*' : '(Opcional)' }}</label>
                    <input type="number" step="0.01" name="enrollment_fee" value="{{ old('enrollment_fee', 0.00) }}" {{ is_private_school() ? 'required' : '' }}
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('enrollment_fee') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Valor pago no ato da matrícula (se houver)</p>
                    @error('enrollment_fee')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Immediate Payment Checkbox -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <label class="flex items-start gap-2 cursor-pointer text-xs text-slate-700">
                    <input type="checkbox" name="pay_now" id="pay_now" value="1" {{ old('pay_now') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="font-bold block">Pagamento realizado agora</span>
                        <span class="text-[10px] text-slate-400">Marque esta opção se a taxa de matrícula já foi paga. A matrícula será ativada automaticamente.</span>
                    </div>
                </label>

                <div id="payment_method_field" style="display: none;">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Método de Pagamento</label>
                    <select name="payment_method" class="w-full sm:w-1/2 rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="cash">Dinheiro</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">e-Mola</option>
                        <option value="bank">Transferência Bancária</option>
                        <option value="multicaixa">Multicaixa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Observações</label>
                <textarea name="observations" rows="3"
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('observations') border-rose-500 @enderror">{{ old('observations') }}</textarea>
                @error('observations')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <!-- Status Notice -->
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                <span id="status_info">Se a taxa de matrícula for maior que 0, a matrícula ficará com status "Pendente" até a confirmação do pagamento.</span>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('enrollments.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition disabled:opacity-50" {{ $students->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-save"></i> Realizar Matrícula
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollmentFeeInput = document.querySelector('input[name="enrollment_fee"]');
    const payNowCheckbox = document.querySelector('input[name="pay_now"]');
    const paymentMethodField = document.getElementById('payment_method_field');
    const statusInfo = document.getElementById('status_info');

    function updateStatusInfo() {
        const enrollmentFee = parseFloat(enrollmentFeeInput.value) || 0;
        const payNow = payNowCheckbox.checked;

        if (enrollmentFee === 0) {
            statusInfo.textContent = 'Matrícula será ativada automaticamente (sem taxa de matrícula).';
        } else if (payNow) {
            statusInfo.textContent = 'Matrícula será ativada automaticamente (pagamento confirmado).';
        } else {
            statusInfo.textContent = 'Matrícula ficará com status "Pendente" até a confirmação do pagamento.';
        }
    }

    if (enrollmentFeeInput) enrollmentFeeInput.addEventListener('input', updateStatusInfo);
    if (payNowCheckbox) {
        payNowCheckbox.addEventListener('change', function() {
            paymentMethodField.style.display = this.checked ? 'block' : 'none';
            updateStatusInfo();
        });
    }

    updateStatusInfo();
    if (payNowCheckbox && payNowCheckbox.checked) {
        paymentMethodField.style.display = 'block';
    }
});
</script>
@endpush
