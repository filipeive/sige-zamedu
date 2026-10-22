@extends('layouts.app')

@section('title', 'Análise de Pedido de Matrícula #' . $application->id)
@section('page-title', 'Análise de Pedido')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.applications.index') }}" class="text-slate-400 hover:text-slate-600">Matrículas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Pedido #{{ $application->id }}</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-2">
                    <i class="fas fa-file-signature text-xs"></i>
                    Pedido #{{ $application->id }} &bull; {{ $application->type === 'NEW' ? 'Novo Ingresso' : 'Renovação' }}
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    {{ $application->student_data['first_name'] ?? '' }} {{ $application->student_data['last_name'] ?? '' }}
                </h1>
                <p class="text-xs text-slate-300 mt-1 flex items-center gap-4">
                    <span><i class="fas fa-calendar-alt text-slate-400 mr-1.5"></i> Submetido em {{ $application->submitted_at ? $application->submitted_at->format('d/m/Y \à\s H:i') : '-' }}</span>
                    <span><i class="fas fa-graduation-cap text-slate-400 mr-1.5"></i> Classe: {{ $application->student_data['grade_level'] ?? 'N/A' }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('enrollments.applications.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs border border-white/10 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Voltar aos Pedidos
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Details & Documents (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Student & Guardian Information -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-id-card text-xs"></i>
                        </span>
                        Dados do Processo de Candidatura
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $application->type === 'NEW' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                        {{ $application->type === 'NEW' ? 'Novo Ingresso' : 'Renovação' }}
                    </span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Column -->
                    <div class="space-y-4">
                        <div class="pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                <i class="fas fa-user-graduate"></i>
                                Dados do Aluno
                            </h3>
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nome Completo:</span>
                                <span class="font-bold text-slate-800">{{ $application->student_data['first_name'] ?? '' }} {{ $application->student_data['last_name'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Data de Nascimento:</span>
                                <span class="font-medium text-slate-700">
                                    {{ isset($application->student_data['birth_date']) ? \Carbon\Carbon::parse($application->student_data['birth_date'])->format('d/m/Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Género:</span>
                                <span class="font-medium text-slate-700">
                                    {{ ($application->student_data['gender'] ?? '') === 'male' ? 'Masculino' : 'Feminino' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Classe Pretendida:</span>
                                <span class="font-bold text-emerald-700">{{ $application->student_data['grade_level'] ?? 'N/A' }}</span>
                            </div>
                            @if(!empty($application->student_data['special_needs']))
                                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                                    <div class="font-bold flex items-center gap-1.5 mb-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Necessidades Educativas Especiais:
                                    </div>
                                    <p class="text-xs">{{ $application->student_data['special_needs'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Guardian Column -->
                    <div class="space-y-4">
                        <div class="pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                <i class="fas fa-user-tie"></i>
                                Dados do Encarregado
                            </h3>
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nome:</span>
                                <span class="font-bold text-slate-800">{{ $application->parent_data['first_name'] ?? '' }} {{ $application->parent_data['last_name'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Telefone:</span>
                                <span class="font-medium text-slate-700">{{ $application->parent_data['phone'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">E-mail:</span>
                                <span class="font-medium text-slate-700">{{ $application->parent_data['email'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Grau de Parentesco:</span>
                                <span class="font-medium text-slate-700">{{ ucfirst($application->parent_data['relationship'] ?? 'Encarregado') }}</span>
                            </div>
                            @if(!empty($application->parent_data['address']))
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Endereço:</span>
                                    <span class="font-medium text-slate-700">{{ $application->parent_data['address'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-file-pdf text-xs"></i>
                        </span>
                        Documentação Anexada
                    </h2>
                    <span class="text-xs text-slate-400 font-medium">{{ count($application->documents) }} ficheiro(s)</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @forelse($application->documents as $doc)
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-emerald-300 hover:shadow-xs transition-all flex flex-col items-center text-center bg-slate-50/50">
                                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-3">
                                    <i class="fas fa-file-pdf text-xl"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800 line-clamp-1 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                                </h3>
                                <p class="text-[10px] text-slate-400 mb-3">Documento Digitalizado</p>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                   class="mt-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:text-emerald-700 hover:border-emerald-300 shadow-2xs transition-colors">
                                    <i class="fas fa-eye text-xs text-emerald-600"></i>
                                    Visualizar
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-slate-400">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-folder-open text-base"></i>
                                </div>
                                <p class="text-xs font-medium text-slate-500">Nenhum documento digital anexado a este pedido.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Actions, Status & Payment (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Status Management Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-tasks text-xs"></i>
                        </span>
                        Gestão do Estado
                    </h2>
                </div>

                <div class="p-6 space-y-5">
                    @php
                        $statusMap = [
                            'PENDING' => ['label' => 'Pendente', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                            'IN_REVIEW' => ['label' => 'Em Análise', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                            'DOCUMENT_DELIVERED' => ['label' => 'Docs Entregues', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                            'APPROVED' => ['label' => 'Aprovado', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                            'REJECTED' => ['label' => 'Rejeitado', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200'],
                            'ENROLLED' => ['label' => 'Matriculado Ativo', 'bg' => 'bg-slate-900', 'text' => 'text-white', 'border' => 'border-slate-900'],
                        ];
                        $curr = $statusMap[$application->status] ?? ['label' => $application->status, 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200'];
                    @endphp

                    <div class="text-center p-3 rounded-xl {{ $curr['bg'] }} border {{ $curr['border'] }}">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Estado do Pedido</div>
                        <div class="text-sm font-bold {{ $curr['text'] }}">{{ $curr['label'] }}</div>
                    </div>

                    <form action="{{ route('enrollments.applications.update-status', $application->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alterar Estado</label>
                            <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                                <option value="PENDING" {{ $application->status === 'PENDING' ? 'selected' : '' }}>Pendente</option>
                                <option value="IN_REVIEW" {{ $application->status === 'IN_REVIEW' ? 'selected' : '' }}>Em Análise</option>
                                <option value="DOCUMENT_DELIVERED" {{ $application->status === 'DOCUMENT_DELIVERED' ? 'selected' : '' }}>Documentos Entregues</option>
                                <option value="APPROVED" {{ $application->status === 'APPROVED' ? 'selected' : '' }}>Aprovado</option>
                                <option value="REJECTED" {{ $application->status === 'REJECTED' ? 'selected' : '' }}>Rejeitado</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Observações Internas</label>
                            <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800 placeholder-slate-400" placeholder="Anotações para a equipa da secretaria...">{{ $application->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-all shadow-xs flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            Atualizar Estado
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-xs"></i>
                        </span>
                        Pagamento da Taxa
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="text-center p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total a Liquidar</div>
                        <div class="text-2xl font-bold font-mono text-slate-900">
                            {{ number_format($application->total_amount, 2, ',', '.') }} MT
                        </div>
                        <div class="mt-2">
                            @if($application->payment_status === 'PAID')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check"></i> Pago e Confirmado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fas fa-clock"></i> Aguardando Pagamento
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($application->payment_status !== 'PAID')
                        <form action="{{ route('enrollments.applications.confirm-payment', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Referência de Depósito / M-Pesa <span class="text-rose-500">*</span></label>
                                <input type="text" name="payment_reference" required placeholder="Ex: DEP-12345 ou MP2409..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Data do Pagamento <span class="text-rose-500">*</span></label>
                                <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Comprovativo Digital (Opcional)</label>
                                <input type="file" name="payment_proof" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-xs flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Confirmar Pagamento
                            </button>
                        </form>
                    @else
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                            <div class="flex justify-between">
                                <span class="font-medium text-emerald-700">Referência:</span>
                                <span class="font-bold font-mono">{{ $application->payment_reference ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-emerald-700">Data de Liquidação:</span>
                                <span class="font-semibold">{{ $application->payment_date ? $application->payment_date->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Finalize Enrollment Card (if Approved & Paid) -->
            @if($application->status === 'APPROVED' && $application->payment_status === 'PAID')
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-lg space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                            <i class="fas fa-user-check text-sm"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-sm text-white">Efetivar Matrícula</h3>
                            <p class="text-[11px] text-emerald-100">Criar aluno ativo no sistema</p>
                        </div>
                    </div>

                    <form action="{{ route('enrollments.applications.finalize', $application->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-emerald-100 mb-1.5">Atribuir Turma <span class="text-rose-200">*</span></label>
                            <select name="class_id" required class="w-full px-3 py-2 text-xs rounded-xl border-0 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-white">
                                <option value="">Selecione a turma...</option>
                                @foreach(\App\Models\ClassRoom::all() as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs transition-all shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-check-double"></i>
                            Converter em Aluno Ativo
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
