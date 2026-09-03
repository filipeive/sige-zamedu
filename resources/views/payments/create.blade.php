@extends('layouts.app')

@section('title', is_public_school() ? 'Registrar Taxa Escolar' : 'Novo Pagamento')
@section('page-title', is_public_school() ? 'Registrar Taxa Escolar' : 'Novo Pagamento')
@section('page-title-icon', 'fas fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Novo</li>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-4">

    <!-- 1. Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-emerald-600"></i> {{ is_public_school() ? 'Registrar Taxa Escolar' : 'Novo Pagamento de Propina / Taxa' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Selecione o estudante, tipo de taxa e emita o recibo de pagamento.</p>
        </div>
        <a href="{{ route('payments.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- 2. Form Container Card with Emerald Accent -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5">
        <form action="{{ route('payments.store') }}" method="POST" id="payment-form" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Selecione o Aluno *</label>
                <input type="text" id="student_search" placeholder="Pesquisar aluno..." class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 mb-2" />
                <select name="student_id" id="student_id"
                        class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 @error('student_id') border-rose-500 @enderror" required>
                    <option value="">Selecione o aluno...</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-fee="{{ $student->currentEnrollment?->monthly_fee ?? $student->monthly_fee }}"
                            data-class="{{ $student->currentEnrollment?->class?->name ?? 'Sem turma' }}"
                            {{ old('student_id', $selectedStudent?->id) == $student->id ? 'selected' : '' }}>
                            {{ $student->student_number }} - {{ $student->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div id="student-info" class="hidden rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 font-semibold">
                <strong>Turma:</strong> <span id="info-class">-</span>
                @if(is_private_school())
                    | <strong>Mensalidade:</strong> <span id="info-fee">-</span> MT
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tipo de Pagamento *</label>
                    <select name="type" id="payment_type"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">Selecione...</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="month-group">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mês de Referência</label>
                    <select name="month" id="month"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Selecione...</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ old('month', date('n')) == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Valor (MT) *</label>
                    <input type="number" step="0.01" min="0" name="amount" id="amount" value="{{ old('amount') }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data de Vencimento *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status de Pagamento *</label>
                    <select name="status" id="payment_status"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="paid" {{ old('status', 'paid') == 'paid' ? 'selected' : '' }}>Pago (Liquidado)</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    </select>
                </div>

                <div id="payment-method-group">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Método de Pagamento</label>
                    <select name="payment_method" id="payment_method"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="numerario" {{ old('payment_method') == 'numerario' ? 'selected' : '' }}>Numerário (Dinheiro)</option>
                        <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="emola" {{ old('payment_method') == 'emola' ? 'selected' : '' }}>e-Mola</option>
                        <option value="pos" {{ old('payment_method') == 'pos' ? 'selected' : '' }}>Cartão POS / TPA</option>
                        <option value="transferencia" {{ old('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferência Bancária</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Observações / Descrição</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Observações adicionais...">{{ old('description') }}</textarea>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('payments.index') }}" class="no-underline px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-check"></i> Registar Pagamento
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_id');
        const studentSearch = document.getElementById('student_search');
        const infoDiv = document.getElementById('student-info');
        const infoClass = document.getElementById('info-class');
        const infoFee = document.getElementById('info-fee');
        const typeSelect = document.getElementById('payment_type');
        const amountInput = document.getElementById('amount');

        // Filter options based on search input
        function filterStudentOptions() {
            const query = studentSearch.value.toLowerCase();
            for (let i = 0; i < studentSelect.options.length; i++) {
                const opt = studentSelect.options[i];
                const text = opt.text.toLowerCase();
                opt.style.display = text.includes(query) ? '' : 'none';
            }
            // If current selected option is hidden, reset selection
            if (studentSelect.selectedIndex >= 0) {
                const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
                if (selectedOpt && selectedOpt.style.display === 'none' && selectedOpt.value !== "") {
                    studentSelect.value = '';
                    updateStudentInfo();
                }
            }
        }

        function updateStudentInfo() {
            const opt = studentSelect.options[studentSelect.selectedIndex];
            if (opt && opt.value) {
                infoClass.textContent = opt.dataset.class || '-';
                if (infoFee) infoFee.textContent = opt.dataset.fee ? parseFloat(opt.dataset.fee).toLocaleString('pt-MZ') : '-';
                infoDiv.classList.remove('hidden');
                
                if (typeSelect.value === 'mensalidade' && opt.dataset.fee) {
                    amountInput.value = opt.dataset.fee;
                }
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        studentSearch.addEventListener('input', filterStudentOptions);
        studentSelect.addEventListener('change', updateStudentInfo);
        
        // Initialize filtering in case of pre-filled value
        filterStudentOptions();
        if (studentSelect.value) updateStudentInfo();
    });
</script>
@endpush
@endsection
