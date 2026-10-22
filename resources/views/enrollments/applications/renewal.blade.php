@extends('layouts.app')

@section('title', 'Renovação de Matrícula')
@section('page-title', 'Renovação de Matrícula')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item text-slate-400">Matrículas</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Renovação de Matrícula</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-emerald-300">
                <i class="fas fa-sync text-lg"></i>
            </span>
            <div>
                <h1 class="text-xl font-bold text-white">Renovação de Matrícula - Ano Letivo {{ $academicYear }}</h1>
                <p class="text-xs text-slate-300 mt-0.5">Confirme e atualize os dados para a renovação de <strong>{{ $student->full_name }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Student Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-medium block">Nome do Aluno:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $student->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-medium block">Nº de Matrícula:</span>
                <span class="font-bold font-mono text-emerald-700 text-sm">{{ $student->student_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-medium block">Classe Atual:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $student->current_class->name ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('parent.student-renewal.store', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Contact & Address Section -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-address-book text-xs"></i>
                    </span>
                    Atualização de Contactos e Endereço
                </h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Telefone / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="parent[phone]" value="{{ old('parent.phone', $student->parent->phone ?? '') }}" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email do Encarregado</label>
                    <input type="email" name="parent[email]" value="{{ old('parent.email', $student->parent->user->email ?? '') }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Endereço Residencial <span class="text-rose-500">*</span></label>
                    <textarea name="student[address]" rows="2" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">{{ old('student.address', $student->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nome do Contacto de Emergência <span class="text-rose-500">*</span></label>
                    <input type="text" name="student[emergency_contact]" value="{{ old('student.emergency_contact', $student->emergency_contact) }}" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Telefone de Emergência <span class="text-rose-500">*</span></label>
                    <input type="text" name="student[emergency_phone]" value="{{ old('student.emergency_phone', $student->emergency_phone) }}" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                </div>
            </div>
        </div>

        <!-- Medical Info & Documents -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-heartbeat text-xs"></i>
                    </span>
                    Informações Médicas e Documentos
                </h2>
            </div>

            <div class="p-6 space-y-5">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="student[has_special_needs]" value="1" id="special_needs" {{ $student->has_special_needs ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-semibold text-slate-800">O aluno possui necessidades educativas especiais ou restrições de saúde?</span>
                    </label>
                    <div id="special_needs_desc_wrapper" class="{{ $student->has_special_needs ? '' : 'hidden' }} mt-3">
                        <textarea name="student[special_needs_description]" id="special_needs_desc" rows="2" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" placeholder="Descreva as condições específicas ou cuidados recomendados...">{{ $student->special_needs_description }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Atestado Médico Atualizado (PDF / JPG)</label>
                        <input type="file" name="medical_certificate" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @if($student->medical_certificate)
                            <div class="mt-1 text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Documento já arquivado no sistema
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Fotografia tipo Passe (JPG / PNG)</label>
                        <input type="file" name="passport_photo" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        @if($student->passport_photo)
                            <div class="mt-1 text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Foto já cadastrada no sistema
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Debts (if any) -->
        @if(isset($pendingDebts) && $pendingDebts->count() > 0)
            <div class="bg-rose-50 rounded-2xl border border-rose-200 p-5 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 text-rose-800 font-bold text-sm">
                    <i class="fas fa-exclamation-circle text-base"></i>
                    <span>Dívidas Pendentes de Anos Anteriores</span>
                </div>
                <div class="divide-y divide-rose-200/60 text-xs">
                    @php $debtTotal = 0; @endphp
                    @foreach($pendingDebts as $debt)
                        <div class="py-2 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">{{ $debt->month_name }} / {{ $debt->year }}</span>
                                <span class="text-slate-500 ml-2 font-normal">({{ $debt->type }})</span>
                            </div>
                            <span class="font-bold font-mono text-rose-600">{{ number_format($debt->total_amount, 2, ',', '.') }} MT</span>
                        </div>
                        @php $debtTotal += $debt->total_amount; @endphp
                    @endforeach
                </div>
                <div class="pt-3 border-t border-rose-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-rose-900">Total em Dívida Anterior:</span>
                    <span class="text-base font-bold font-mono text-rose-700">{{ number_format($debtTotal, 2, ',', '.') }} MT</span>
                </div>
                <p class="text-[11px] text-rose-600">Estas dívidas devem ser liquidadas juntamente com a taxa de renovação.</p>
            </div>
        @endif

        <!-- Renewal Fees Breakdown -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3 flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-600"></i>
                Taxas de Renovação para o Novo Ano Letivo
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                @php $total = 0; @endphp
                @foreach($fees as $fee)
                    @if(!$fee->grade_level || $fee->grade_level === $student->grade_level)
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-700">{{ $fee->name }}</span>
                            <span class="font-mono font-semibold text-slate-800">{{ number_format($fee->amount, 2, ',', '.') }} MT</span>
                        </div>
                        @php $total += $fee->amount; @endphp
                    @endif
                @endforeach
            </div>
            <div class="pt-4 mt-2 border-t border-slate-200 flex items-center justify-between">
                <span class="font-bold text-slate-800 text-sm">Total da Renovação:</span>
                <span class="text-xl font-bold font-mono text-emerald-700">{{ number_format($total, 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- Terms and Conditions Acceptance -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <label class="inline-flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" name="terms_accepted" required id="terms" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <span class="text-xs text-slate-700 leading-relaxed">
                    Confirmo a veracidade das informações prestadas e concordo com os termos e o regulamento escolar para o novo ano letivo.
                </span>
            </label>
        </div>

        <!-- Notice Alert -->
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center gap-3 text-xs">
            <i class="fas fa-info-circle text-amber-600 text-base flex-shrink-0"></i>
            <span>A renovação só será formalmente confirmada após a validação e comprovativo do pagamento na secretaria da escola.</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('parent.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Submeter Renovação
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const check = document.getElementById('special_needs');
        const wrapper = document.getElementById('special_needs_desc_wrapper');
        if (check && wrapper) {
            check.addEventListener('change', function () {
                if (this.checked) {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
