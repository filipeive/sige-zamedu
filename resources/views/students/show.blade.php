@extends('layouts.app')

@section('title', 'Ficha 360º - ' . $student->full_name)
@section('page-title', 'Perfil 360º do Aluno')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Alunos</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'resumo' }">

    {{-- Header de Operação / Ações Rápidas --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-id-card me-1"></i> {{ $student->student_number }}
                </span>
                @php
                    $statusColors = [
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'inactive' => 'bg-rose-100 text-rose-800 border-rose-200',
                        'transferred' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'graduated' => 'bg-purple-100 text-purple-800 border-purple-200',
                        'pending_renewal' => 'bg-amber-100 text-amber-800 border-amber-200',
                    ];
                    $statusLabels = [
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        'transferred' => 'Transferido',
                        'graduated' => 'Formado',
                        'pending_renewal' => 'Pendente Renovação',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold border uppercase tracking-wider {{ $statusColors[$student->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    <i class="fas fa-circle text-[8px] me-1 {{ $student->status === 'active' ? 'text-emerald-500' : 'text-slate-400' }}"></i> {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                </span>
                @if ($currentEnrollment)
                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-teal-100 text-teal-800 border border-teal-200 uppercase tracking-wider">
                        <i class="fas fa-chalkboard me-1"></i> Turma: {{ $currentEnrollment->class->name }}
                    </span>
                @endif
            </div>

            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-graduate text-emerald-600"></i> {{ $student->full_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Visão integrada 360º de desempenho pedagógico, assiduidade, dados pessoais e histórico financeiro.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @can('edit_students')
                <a href="{{ route('students.edit', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
            @endcan

            <a href="{{ route('students.grades', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-medal"></i> Pautas
            </a>
            <a href="{{ route('students.attendance', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-sky-700 hover:bg-sky-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-calendar-check"></i> Assiduidade
            </a>
            @can('manage_payments')
                <a href="{{ route('students.payments', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-file-invoice-dollar"></i> {{ is_public_school() ? 'Taxas' : 'Propinas' }}
                </a>
            @endcan

            <a href="{{ route('students.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- Grelha Principal Dividida em 2 Colunas --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Coluna Esquerda (4/12): Ficha do Aluno & Contactos --}}
        <div class="lg:col-span-4 space-y-4">
            
            {{-- Cartão do Aluno --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 text-white text-center relative">
                    <div class="mb-3 relative inline-block">
                        @if ($student->passport_photo)
                            <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}"
                                class="w-28 h-28 rounded-full shadow-md border-4 border-white object-cover mx-auto">
                        @else
                            <div class="w-28 h-28 bg-white text-emerald-800 rounded-full flex items-center justify-center mx-auto shadow-md border-4 border-white text-3xl font-black">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="font-black text-lg text-white mb-0.5">{{ $student->full_name }}</h3>
                    <p class="text-emerald-100 font-mono text-xs mb-2">{{ $student->student_number }}</p>
                    
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {{ $statusColors[$student->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                        <i class="fas fa-circle text-[8px] me-1 {{ $student->status === 'active' ? 'text-emerald-500' : 'text-slate-400' }}"></i> {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                    </span>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    @if ($currentEnrollment)
                        <div class="p-3.5 bg-emerald-50/80 border border-emerald-200/80 rounded-xl text-emerald-950 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shrink-0">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-black text-emerald-700 tracking-wider block">Turma Atual</span>
                                <h4 class="font-black text-sm text-slate-900 mb-0">{{ $currentEnrollment->class->name }}</h4>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <a href="{{ route('students.support.index', $student) }}" class="no-underline flex items-center justify-center gap-1.5 w-full py-2.5 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 text-xs font-bold shadow-2xs transition">
                            <i class="fas fa-exclamation-triangle text-amber-600"></i> Registar Ocorrência / Histórico
                        </a>

                        @if ($student->parent && $student->parent->phone)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#smsModal{{ $student->id }}"
                                class="flex items-center justify-center gap-1.5 w-full py-2.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200/80 text-xs font-bold shadow-2xs transition cursor-pointer">
                                <i class="fas fa-comment-dots text-sky-600"></i> Enviar SMS ao Encarregado
                            </button>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                <i class="fas fa-map-marker-alt text-emerald-600"></i>
                            </div>
                            <div>
                                <strong class="block text-[10px] uppercase font-bold text-slate-400">Morada Residencial</strong>
                                <span class="font-medium text-slate-700">{{ $student->address ?? 'Não registada' }}</span>
                            </div>
                        </div>

                        @if ($student->parent)
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fas fa-user-friends text-teal-600"></i>
                                </div>
                                <div>
                                    <strong class="block text-[10px] uppercase font-bold text-slate-400">Encarregado de Educação</strong>
                                    <span class="font-bold text-slate-800 block">{{ $student->parent->first_name }} {{ $student->parent->last_name }}</span>
                                    <span class="font-mono text-slate-500 text-2xs block">{{ $student->parent->phone }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                <i class="fas fa-phone-alt text-rose-600"></i>
                            </div>
                            <div>
                                <strong class="block text-[10px] uppercase font-bold text-slate-400">Contacto de Emergência</strong>
                                <span class="font-bold text-slate-800 block">{{ $student->emergency_contact ?? 'Não indicado' }}</span>
                                <span class="font-mono text-slate-500 text-2xs block">{{ $student->emergency_phone ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Coluna Direita (8/12): Dashboard 360º com Separadores --}}
        <div class="lg:col-span-8 space-y-5">
            
            {{-- 4 Stat Cards Superiores --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                {{-- Card Presença --}}
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-400 block truncate">Presença</span>
                        <h4 class="text-lg font-black text-slate-800 mt-0.5 mb-0">{{ $attendanceStats['attendance_rate'] }}%</h4>
                    </div>
                </div>

                {{-- Card Média Geral --}}
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5 hover:border-blue-300 transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base font-bold shrink-0">
                        <i class="fas fa-medal"></i>
                    </div>
                    @php
                        $studentMts = [];
                        if (isset($matrix) && !empty($matrix)) {
                            foreach ($matrix as $mItem) {
                                foreach ([1, 2, 3] as $tNum) {
                                    if (isset($mItem['terms'][$tNum]['mt']) && $mItem['terms'][$tNum]['mt'] !== null) {
                                        $studentMts[] = $mItem['terms'][$tNum]['mt'];
                                    }
                                }
                            }
                        }
                        $totalSubs = isset($matrix) ? count($matrix) : 1;
                        $gradedSubs = count($studentMts);
                        $avgMethod = setting('average_method', 'all_subjects');
                        if ($gradedSubs > 0) {
                            $overallStudentAvg = ($avgMethod === 'all_subjects' && $totalSubs > 0)
                                ? round(array_sum($studentMts) / $totalSubs, 1)
                                : round(array_sum($studentMts) / $gradedSubs, 1);
                        } else {
                            $overallStudentAvg = 0;
                        }
                    @endphp
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-400 block truncate">Média Geral</span>
                        <h4 class="text-lg font-black text-slate-800 mt-0.5 mb-0">
                            {{ number_format($overallStudentAvg, 1) }}
                            @if($gradedSubs > 0 && $gradedSubs < $totalSubs)
                                <span class="text-slate-400 text-xs font-normal">({{ $gradedSubs }}/{{ $totalSubs }})</span>
                            @endif
                        </h4>
                    </div>
                </div>

                {{-- Card Pagamentos --}}
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5 hover:border-teal-300 transition">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base font-bold shrink-0">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-400 block truncate">{{ is_public_school() ? 'Taxas Pagas' : 'Pagamentos' }}</span>
                        <h4 class="text-lg font-black text-slate-800 mt-0.5 mb-0">{{ $student->payments->filter(fn($p) => is_private_school() || $p->type !== 'mensalidade')->where('status', 'paid')->count() }}</h4>
                    </div>
                </div>

                {{-- Card Idade --}}
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center gap-3.5 hover:border-amber-300 transition">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shrink-0">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase font-extrabold tracking-wider text-slate-400 block truncate">Idade</span>
                        <h4 class="text-lg font-black text-slate-800 mt-0.5 mb-0">{{ $student->age ?? 'N/A' }} <span class="text-xs font-semibold text-slate-400">anos</span></h4>
                    </div>
                </div>
            </div>

            {{-- Painel de Separadores 360º --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 p-2">
                    <div class="flex items-center gap-1 overflow-x-auto">
                        <button type="button" @click="activeTab = 'resumo'" :class="activeTab === 'resumo' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200/60'" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-chart-pie"></i> Resumo 360º
                        </button>
                        <button type="button" @click="activeTab = 'academico'" :class="activeTab === 'academico' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200/60'" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-graduation-cap"></i> Pautas &amp; Notas
                        </button>
                        <button type="button" @click="activeTab = 'frequencia'" :class="activeTab === 'frequencia' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200/60'" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-calendar-check"></i> Assiduidade
                        </button>
                        <button type="button" @click="activeTab = 'financeiro'" :class="activeTab === 'financeiro' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200/60'" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-receipt"></i> {{ is_public_school() ? 'Taxas & Recibos' : 'Financeiro' }}
                        </button>
                        <button type="button" @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200/60'" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fas fa-stream"></i> Timeline
                        </button>
                    </div>
                </div>

                <div class="p-5">
                    
                    {{-- TAB 1: RESUMO 360º --}}
                    <div x-show="activeTab === 'resumo'" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-7 p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <i class="fas fa-chart-bar text-emerald-600"></i> Desempenho por Disciplina
                                </h4>
                                <div class="h-56">
                                    <canvas id="studentSubjectChart"></canvas>
                                </div>
                            </div>
                            <div class="md:col-span-5 p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <i class="fas fa-pie-chart text-teal-600"></i> Distribuição de Frequência
                                </h4>
                                <div class="h-56">
                                    <canvas id="studentAttendancePieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: PAUTAS & NOTAS --}}
                    <div x-show="activeTab === 'academico'" x-cloak class="space-y-4">
                        {{-- Barra de Ações para Notas --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                            <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                                <button type="button" class="term-btn px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 transition active" data-term="1" onclick="switchTerm('1')">1º Trimestre</button>
                                <button type="button" class="term-btn px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 transition" data-term="2" onclick="switchTerm('2')">2º Trimestre</button>
                                <button type="button" class="term-btn px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 transition" data-term="3" onclick="switchTerm('3')">3º Trimestre</button>
                                <button type="button" class="term-btn px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 transition" data-term="annual" onclick="switchTerm('annual')">Ficha Anual</button>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('students.grades.pdf', ['student' => $student->id, 'term' => 1]) }}" id="pdfDownloadBtn" class="no-underline inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs transition cursor-pointer" data-bs-toggle="modal" data-bs-target="#shareMailModal">
                                    <i class="fas fa-envelope text-emerald-600"></i> E-mail
                                </button>
                                <a href="#" id="whatsappShareBtn" target="_blank" class="no-underline inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        </div>

                        {{-- 1º Trimestre Table --}}
                        <div class="term-table-wrapper" id="termTable-1">
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-slate-800 text-white font-black">
                                        <tr>
                                            <th class="py-3 px-4">Disciplina</th>
                                            <th class="py-3 px-3 text-center">ACS 1</th>
                                            <th class="py-3 px-3 text-center">ACS 2</th>
                                            <th class="py-3 px-3 text-center">ACS 3</th>
                                            <th class="py-3 px-3 text-center">MACS</th>
                                            <th class="py-3 px-3 text-center">ACP</th>
                                            <th class="py-3 px-3 text-center">Média Trim (MT)</th>
                                            <th class="py-3 px-3 text-center">Aproveitamento</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                        @forelse($matrix as $subjectId => $data)
                                            @php $tData = $data['terms'][1]; @endphp
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 px-4 font-black text-slate-900">{{ $data['subject']->name }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center font-bold text-sky-700">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $tData['mt'] >= 9.5 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                            {{ number_format($tData['mt'], 1) }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="font-bold text-xs {{ $tData['mt'] >= 9.5 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $tData['mt'] >= 9.5 ? 'Positiva' : 'Negativa' }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-6 text-center text-slate-400">Sem disciplinas ou notas disponíveis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 2º Trimestre Table --}}
                        <div class="term-table-wrapper" id="termTable-2" style="display:none;">
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-slate-800 text-white font-black">
                                        <tr>
                                            <th class="py-3 px-4">Disciplina</th>
                                            <th class="py-3 px-3 text-center">ACS 1</th>
                                            <th class="py-3 px-3 text-center">ACS 2</th>
                                            <th class="py-3 px-3 text-center">ACS 3</th>
                                            <th class="py-3 px-3 text-center">MACS</th>
                                            <th class="py-3 px-3 text-center">ACP</th>
                                            <th class="py-3 px-3 text-center">Média Trim (MT)</th>
                                            <th class="py-3 px-3 text-center">Aproveitamento</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                        @forelse($matrix as $subjectId => $data)
                                            @php $tData = $data['terms'][2]; @endphp
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 px-4 font-black text-slate-900">{{ $data['subject']->name }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center font-bold text-sky-700">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $tData['mt'] >= 9.5 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                            {{ number_format($tData['mt'], 1) }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="font-bold text-xs {{ $tData['mt'] >= 9.5 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $tData['mt'] >= 9.5 ? 'Positiva' : 'Negativa' }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-6 text-center text-slate-400">Sem disciplinas ou notas disponíveis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 3º Trimestre Table --}}
                        <div class="term-table-wrapper" id="termTable-3" style="display:none;">
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-slate-800 text-white font-black">
                                        <tr>
                                            <th class="py-3 px-4">Disciplina</th>
                                            <th class="py-3 px-3 text-center">ACS 1</th>
                                            <th class="py-3 px-3 text-center">ACS 2</th>
                                            <th class="py-3 px-3 text-center">ACS 3</th>
                                            <th class="py-3 px-3 text-center">MACS</th>
                                            <th class="py-3 px-3 text-center">ACP</th>
                                            <th class="py-3 px-3 text-center">Média Trim (MT)</th>
                                            <th class="py-3 px-3 text-center">Aproveitamento</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                        @forelse($matrix as $subjectId => $data)
                                            @php $tData = $data['terms'][3]; @endphp
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 px-4 font-black text-slate-900">{{ $data['subject']->name }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center font-bold text-sky-700">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $tData['mt'] >= 9.5 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                            {{ number_format($tData['mt'], 1) }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($tData['mt'] !== null)
                                                        <span class="font-bold text-xs {{ $tData['mt'] >= 9.5 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $tData['mt'] >= 9.5 ? 'Positiva' : 'Negativa' }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-6 text-center text-slate-400">Sem disciplinas ou notas disponíveis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Ficha Anual Table --}}
                        <div class="term-table-wrapper" id="termTable-annual" style="display:none;">
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-slate-800 text-white font-black">
                                        <tr>
                                            <th class="py-3 px-4">Disciplina</th>
                                            <th class="py-3 px-3 text-center">MT1</th>
                                            <th class="py-3 px-3 text-center">MT2</th>
                                            <th class="py-3 px-3 text-center">MT3</th>
                                            <th class="py-3 px-3 text-center">Média Anual (MF)</th>
                                            <th class="py-3 px-3 text-center">Exame</th>
                                            <th class="py-3 px-3 text-center">Nota Final (NFD)</th>
                                            <th class="py-3 px-3 text-center">Situação Final</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                        @forelse($matrix as $subjectId => $data)
                                            @php
                                                $annual = $data['annual'];
                                                $isPositive = $annual['mfd'] !== null && $annual['mfd'] >= 9.5;
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 px-4 font-black text-slate-900">{{ $data['subject']->name }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $annual['mt1'] !== null ? number_format($annual['mt1'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $annual['mt2'] !== null ? number_format($annual['mt2'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $annual['mt3'] !== null ? number_format($annual['mt3'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center font-bold text-sky-700">{{ $annual['mf'] !== null ? number_format($annual['mf'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center text-slate-500">{{ $annual['exam'] !== null ? number_format($annual['exam'], 1) : '-' }}</td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($annual['mfd'] !== null)
                                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $isPositive ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                            {{ number_format($annual['mfd'], 1) }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($annual['status'] === 'Aprovado')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">Aprovado</span>
                                                    @elseif($annual['status'] === 'Reprovado')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">Reprovado</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200">Em Curso</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-6 text-center text-slate-400">Sem disciplinas ou notas disponíveis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: ASSIDUIDADE --}}
                    <div x-show="activeTab === 'frequencia'" x-cloak class="space-y-4">
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-slate-800 text-white font-black">
                                    <tr>
                                        <th class="py-3 px-4">Data</th>
                                        <th class="py-3 px-4">Turma</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4">Observações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                    @forelse($student->attendances as $att)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3 px-4 font-mono text-slate-800">{{ $att->attendance_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td class="py-3 px-4 font-bold text-slate-900">{{ $att->class->name ?? 'N/A' }}</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($att->status === 'absent' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                                    {{ $att->status === 'present' ? 'Presente' : ($att->status === 'absent' ? 'Ausente' : 'Atrasado') }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-slate-500">{{ $att->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-slate-400">
                                                <i class="fas fa-calendar-times text-2xl mb-2 text-slate-300 block"></i>
                                                <span>Nenhum registo de assiduidade encontrado.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 4: FINANCEIRO / TAXAS --}}
                    <div x-show="activeTab === 'financeiro'" x-cloak class="space-y-4">
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-slate-800 text-white font-black">
                                    <tr>
                                        <th class="py-3 px-4">Referência</th>
                                        <th class="py-3 px-4">Tipo</th>
                                        <th class="py-3 px-4">Mês/Ano</th>
                                        <th class="py-3 px-4">Valor</th>
                                        <th class="py-3 px-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                    @forelse($student->payments->filter(fn($pay) => is_private_school() || $pay->type !== 'mensalidade') as $pay)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $pay->reference_number }}</td>
                                            <td class="py-3 px-4 font-bold text-slate-900">{{ ucfirst($pay->type) }}</td>
                                            <td class="py-3 px-4 text-slate-500">{{ $pay->month ? $pay->month.'/'.$pay->year : '-' }}</td>
                                            <td class="py-3 px-4 font-black text-emerald-700">{{ number_format($pay->amount, 2, ',', '.') }} MT</td>
                                            <td class="py-3 px-4">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $pay->status === 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($pay->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-rose-100 text-rose-800 border border-rose-200') }}">
                                                    {{ $pay->status === 'paid' ? 'Pago' : ($pay->status === 'pending' ? 'Pendente' : 'Atrasado') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400">
                                                <i class="fas fa-receipt text-2xl mb-2 text-slate-300 block"></i>
                                                <span>Nenhum registo de taxas/pagamentos encontrado.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 5: TIMELINE --}}
                    <div x-show="activeTab === 'timeline'" x-cloak class="space-y-4">
                        <div class="relative pl-6 border-l-2 border-emerald-200 ml-3 space-y-4">
                            @forelse($timelineEvents as $event)
                                <div class="relative">
                                    <div class="absolute -left-[31px] top-1.5 w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                                        <i class="{{ $event['icon'] }}"></i>
                                    </div>
                                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="flex justify-between items-center mb-1">
                                            <h4 class="text-xs font-black text-slate-800">{{ $event['title'] }}</h4>
                                            <span class="text-3xs font-mono font-bold text-slate-400">{{ $event['date']?->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 mb-0 font-medium">{{ $event['description'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400 text-center py-6 text-xs">Sem eventos registados na linha do tempo.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal Partials / Share Mail & SMS (Standard Bootstrap Dialogs Compatible) -->
<div class="modal fade" id="shareMailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <div class="px-5 py-4 bg-emerald-700 text-white flex items-center justify-between">
                <h3 class="text-sm font-black text-white tracking-tight flex items-center gap-2 mb-0">
                    <i class="fas fa-envelope-open-text"></i> Enviar Boletim por E-mail
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('students.grades.share-email', $student) }}" method="POST">
                @csrf
                <input type="hidden" name="term" id="modalTermInput" value="1">
                <div class="p-5 space-y-3">
                    <p class="text-xs text-slate-500">O boletim oficial será gerado em formato PDF e anexado diretamente ao e-mail enviado ao encarregado.</p>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail do Encarregado</label>
                        <input type="email" name="email" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-emerald-500 focus:ring-emerald-500" value="{{ $student->parent->email ?? '' }}" placeholder="exemplo@email.com" required>
                    </div>
                </div>
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs">
                        <i class="fas fa-paper-plane me-1"></i> Enviar Agora
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($student->parent && $student->parent->phone)
<div class="modal fade" id="smsModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <form action="{{ route('students.sms.send', $student) }}" method="POST">
                @csrf
                <div class="px-5 py-4 bg-sky-700 text-white flex items-center justify-between">
                    <h3 class="text-sm font-black text-white tracking-tight flex items-center gap-2 mb-0">
                        <i class="fas fa-comment-dots"></i> Enviar SMS ao Encarregado
                    </h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="p-5 space-y-3">
                    <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl flex items-center gap-3 text-xs text-sky-900">
                        <i class="fas fa-user text-sky-600 text-base"></i>
                        <div>
                            <strong class="font-black block">{{ $student->parent->first_name }} {{ $student->parent->last_name }}</strong>
                            <span class="font-mono text-sky-700">{{ $student->parent->phone }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone (opcional)</label>
                        <input type="text" name="phone" class="w-full rounded-lg border-slate-200 text-xs font-semibold py-2 focus:border-sky-500 focus:ring-sky-500"
                            placeholder="Deixe vazio para usar o do encarregado"
                            value="{{ $student->parent->phone }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mensagem *</label>
                        <textarea id="smsMsg{{ $student->id }}" name="message" rows="4" required maxlength="480" class="w-full rounded-lg border-slate-200 text-xs font-medium py-2 focus:border-sky-500 focus:ring-sky-500"
                            placeholder="Use {aluno} para o nome do aluno, {escola} para o nome da escola..."></textarea>
                        <small class="text-slate-400 text-[10px]">Máx. 480 caracteres.</small>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @php
                            $templates = [
                                "Bom dia. Gostaríamos de informar que {aluno} faltou hoje às aulas. Pedimos contacto com a secretaria.",
                                "Exmo(a) encarregado(a), informamos que {aluno} tem propinas em atraso. Pedimos regularização.",
                                "Comunicamos que a reunião de pais da turma de {aluno} será brevemente.",
                                "Exmo(a), {aluno} recebeu uma observação positiva pelo bom comportamento.",
                                "Pedimos a comparência do encarregado de {aluno} na secretaria da {escola}.",
                            ];
                        @endphp
                        @foreach ($templates as $tpl)
                            <button type="button" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold border border-slate-200 cursor-pointer"
                                onclick="document.getElementById('smsMsg{{ $student->id }}').value = @js($tpl);">
                                {{ mb_substr($tpl, 0, 32) }}…
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-sky-700 hover:bg-sky-800 text-white text-xs font-bold shadow-2xs">
                        <i class="fas fa-paper-plane me-1"></i> Enviar SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gráfico de Média por Disciplina
    const perfData = @json($subjectPerformance);
    const labels = perfData.length > 0 ? perfData.map(p => p.subject) : ['Sem Notas'];
    const averages = perfData.length > 0 ? perfData.map(p => p.average) : [0];

    new Chart(document.getElementById('studentSubjectChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Média (0-20)',
                data: averages,
                backgroundColor: '#007A3D',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { min: 0, max: 20 } },
            plugins: { legend: { display: false } }
        }
    });

    // 2. Gráfico de Assiduidade
    const attPres = {{ $attendanceStats['present'] }};
    const attAbs = {{ $attendanceStats['absent'] }};
    const attLate = {{ $attendanceStats['late'] }};
    const attTot = attPres + attAbs + attLate;

    new Chart(document.getElementById('studentAttendancePieChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: attTot > 0 ? ['Presente', 'Ausente', 'Atrasado'] : ['Sem Registos'],
            datasets: [{
                data: attTot > 0 ? [attPres, attAbs, attLate] : [1],
                backgroundColor: attTot > 0 ? ['#059669', '#e11d48', '#d97706'] : ['#cbd5e1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Term Switching & WhatsApp Share (Tab Académico)
    let currentActiveTerm = '1';

    window.switchTerm = function(term) {
        currentActiveTerm = term;

        document.querySelectorAll('.term-btn').forEach(btn => {
            if (btn.dataset.term === term) {
                btn.classList.add('bg-emerald-700', 'text-white', 'shadow-2xs');
                btn.classList.remove('text-slate-700');
            } else {
                btn.classList.remove('bg-emerald-700', 'text-white', 'shadow-2xs');
                btn.classList.add('text-slate-700');
            }
        });

        document.querySelectorAll('.term-table-wrapper').forEach(wrapper => {
            wrapper.style.display = wrapper.id === `termTable-${term}` ? 'block' : 'none';
        });

        const pdfBtn = document.getElementById('pdfDownloadBtn');
        if (pdfBtn) {
            let url = new URL(pdfBtn.href);
            url.searchParams.set('term', term);
            pdfBtn.href = url.toString();
        }

        const modalTermInput = document.getElementById('modalTermInput');
        if (modalTermInput) {
            modalTermInput.value = term;
        }

        updateWhatsAppShareLink();
    };

    function updateWhatsAppShareLink() {
        const shareBtn = document.getElementById('whatsappShareBtn');
        if (!shareBtn) return;

        const parentPhone = "{{ $student->parent->phone ?? '' }}".replace(/\s+/g, '');
        const studentName = "{{ $student->full_name }}";
        const termLabel = currentActiveTerm === 'annual' ? 'Ano Lectivo' : `${currentActiveTerm}º Trimestre`;

        let message = `Olá! Segue o aproveitamento escolar de *${studentName}* relativo ao *${termLabel}*:\n\n`;

        const activeTable = document.querySelector(`#termTable-${currentActiveTerm} tbody`);
        if (activeTable) {
            const rows = activeTable.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    const subjectName = cells[0].textContent.trim();
                    const grade = cells[6] ? cells[6].textContent.trim() : '-';
                    message += `• *${subjectName}*: Média ${grade}\n`;
                }
            });
        }

        message += `\nPara ver detalhes completos, consulte o portal do encarregado.`;
        shareBtn.href = `https://api.whatsapp.com/send?phone=${parentPhone}&text=${encodeURIComponent(message)}`;
    }

    updateWhatsAppShareLink();
});
</script>
@endpush
