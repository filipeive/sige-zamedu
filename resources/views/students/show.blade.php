@extends('layouts.app')

@section('title', 'Ficha 360º - ' . $student->full_name)
@section('page-title', 'Perfil 360º do Aluno')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Alunos</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Coluna Esquerda: Cartão do Aluno & Contactos -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-gradient-to-r from-emerald-800 to-teal-800 p-4 text-white text-center position-relative">
                    <div class="mb-3 position-relative d-inline-block">
                        @if ($student->passport_photo)
                            <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}"
                                class="rounded-circle shadow-md border border-4 border-white"
                                style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-white text-emerald-800 rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-md border border-4 border-white"
                                style="width: 120px; height: 120px; font-size: 40px; font-weight: 800;">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1 text-white">{{ $student->full_name }}</h4>
                    <p class="text-emerald-100 font-mono small mb-2">{{ $student->student_number }}</p>
                    
                    @php
                        $statusColors = [
                            'active' => 'bg-emerald-500 text-white',
                            'inactive' => 'bg-rose-500 text-white',
                            'transferred' => 'bg-sky-500 text-white',
                            'graduated' => 'bg-emerald-600 text-white',
                            'pending_renewal' => 'bg-amber-500 text-white',
                        ];
                        $statusLabels = [
                            'active' => 'Ativo',
                            'inactive' => 'Inativo',
                            'transferred' => 'Transferido',
                            'graduated' => 'Formado',
                            'pending_renewal' => 'Pendente Renovação',
                        ];
                    @endphp
                    <span class="badge {{ $statusColors[$student->status] ?? 'bg-secondary' }} px-3 py-1.5 rounded-pill text-xs font-semibold">
                        <i class="fas fa-circle text-[8px] me-1"></i> {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    @if ($currentEnrollment)
                        <div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-3 text-emerald-900 mb-4 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-emerald-100 p-2.5 text-emerald-700">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div>
                                <small class="text-xs uppercase font-bold text-emerald-700 tracking-wider">Turma Atual</small>
                                <h6 class="fw-bold mb-0 text-emerald-950">{{ $currentEnrollment->class->name }}</h6>
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mb-4">
                        @can('edit_students')
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-warning rounded-pill py-2 text-xs font-bold shadow-xs">
                                <i class="fas fa-edit me-1.5"></i> Editar Perfil
                            </a>
                        @endcan

                        <div class="btn-group w-100 rounded-pill overflow-hidden shadow-xs">
                            <a href="{{ route('students.grades', $student) }}" class="btn btn-outline-primary btn-sm" title="Notas">
                                <i class="fas fa-medal"></i> Pautas
                            </a>
                            <a href="{{ route('students.attendance', $student) }}" class="btn btn-outline-info btn-sm" title="Presenças">
                                <i class="fas fa-calendar-check"></i> Frequência
                            </a>
                            @can('manage_payments')
                                <a href="{{ route('students.payments', $student) }}" class="btn btn-outline-success btn-sm" title="{{ is_public_school() ? 'Taxas & Recibos' : 'Financeiro' }}">
                                    <i class="fas fa-file-invoice-dollar"></i> {{ is_public_school() ? 'Taxas & Recibos' : 'Propinas' }}
                                </a>
                            @endcan
                        </div>

                        <a href="{{ route('students.support.index', $student) }}" class="btn btn-outline-warning btn-sm rounded-pill py-2 text-xs font-bold shadow-xs w-100 mt-2">
                            <i class="fas fa-exclamation-triangle me-1.5"></i> Registar Ocorrência / Histórico
                        </a>

                        @if ($student->parent && $student->parent->phone)
                            <button type="button" data-bs-toggle="modal" data-bs-target="#smsModal{{ $student->id }}"
                                class="btn btn-outline-info btn-sm rounded-pill py-2 text-xs font-bold shadow-xs w-100 mt-2">
                                <i class="fas fa-comment-dots me-1.5"></i> Enviar SMS ao Encarregado
                            </button>
                        @endif
                    </div>

                    <div class="border-top pt-3 space-y-3 text-sm">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <div>
                                <strong class="d-block text-xs text-muted">Morada</strong>
                                <span>{{ $student->address }}</span>
                            </div>
                        </div>

                        @if ($student->parent)
                            <div class="d-flex align-items-start gap-2.5">
                                <i class="fas fa-user-friends text-success mt-1"></i>
                                <div>
                                    <strong class="d-block text-xs text-muted">Encarregado de Educação</strong>
                                    <span>{{ $student->parent->first_name }} {{ $student->parent->last_name }}</span>
                                    <span class="d-block text-xs text-muted font-mono">{{ $student->parent->phone }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fas fa-phone text-danger mt-1"></i>
                            <div>
                                <strong class="d-block text-xs text-muted">Contacto de Emergência</strong>
                                <span>{{ $student->emergency_contact }}</span>
                                <span class="d-block text-xs text-muted font-mono">{{ $student->emergency_phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Dashboard 360º com Separadores -->
        <div class="col-lg-8">
            <!-- Stat Cards Superiores -->
            <div class="row g-3 mb-4">
                <div class="col-sm-3">
                    <div class="card border-0 shadow-xs rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-emerald-100 p-2.5 text-emerald-700 text-lg">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <small class="text-xs text-muted uppercase font-bold">Presença</small>
                                <h5 class="fw-black mb-0 text-slate-800">{{ $attendanceStats['attendance_rate'] }}%</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card border-0 shadow-xs rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-blue-100 p-2.5 text-blue-700 text-lg">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div>
                                <small class="text-xs text-muted uppercase font-bold">Média Geral</small>
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
                                <h5 class="fw-black mb-0 text-slate-800">
                                    {{ number_format($overallStudentAvg, 1) }}
                                    @if($gradedSubs > 0 && $gradedSubs < $totalSubs)
                                        <small class="text-muted text-xs font-normal">({{ $gradedSubs }}/{{ $totalSubs }})</small>
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card border-0 shadow-xs rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-purple-100 p-2.5 text-purple-700 text-lg">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <small class="text-xs text-muted uppercase font-bold">{{ is_public_school() ? 'Taxas Pagas' : 'Pagamentos' }}</small>
                                <h5 class="fw-black mb-0 text-slate-800">{{ $student->payments->filter(fn($p) => is_private_school() || $p->type !== 'mensalidade')->where('status', 'paid')->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card border-0 shadow-xs rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-amber-100 p-2.5 text-amber-700 text-lg">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div>
                                <small class="text-xs text-muted uppercase font-bold">Idade</small>
                                <h5 class="fw-black mb-0 text-slate-800">{{ $student->age ?? 'N/A' }} anos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Separadores 360º -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-2">
                    <ul class="nav nav-pills nav-justified gap-1" id="student360Tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-2 text-xs font-bold" id="resumo-tab" data-bs-toggle="tab" data-bs-target="#resumo" type="button" role="tab">
                                <i class="fas fa-chart-pie me-1"></i> Resumo 360º
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 text-xs font-bold" id="academico-tab" data-bs-toggle="tab" data-bs-target="#academico" type="button" role="tab">
                                <i class="fas fa-graduation-cap me-1"></i> Pautas & Notas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 text-xs font-bold" id="frequencia-tab" data-bs-toggle="tab" data-bs-target="#frequencia" type="button" role="tab">
                                <i class="fas fa-calendar-check me-1"></i> Assiduidade
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 text-xs font-bold" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#financeiro" type="button" role="tab">
                                <i class="fas fa-receipt me-1"></i> {{ is_public_school() ? 'Taxas & Recibos' : 'Financeiro' }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 text-xs font-bold" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                                <i class="fas fa-stream me-1"></i> Timeline
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="student360TabContent">
                        
                        <!-- TAB 1: RESUMO 360º -->
                        <div class="tab-pane fade show active" id="resumo" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="p-3 border rounded-3 bg-light/50">
                                        <h6 class="fw-bold mb-3 text-slate-800"><i class="fas fa-chart-bar text-emerald-600 me-2"></i>Desempenho por Disciplina</h6>
                                        <div style="height: 220px;">
                                            <canvas id="studentSubjectChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-3 border rounded-3 bg-light/50">
                                        <h6 class="fw-bold mb-3 text-slate-800"><i class="fas fa-pie-chart text-teal-600 me-2"></i>Distribuição de Frequência</h6>
                                        <div style="height: 220px;">
                                            <canvas id="studentAttendancePieChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PAUTAS & NOTAS -->
                        <div class="tab-pane fade" id="academico" role="tabpanel">
                            <!-- Action Toolbar for Grades -->
                            <div class="d-flex flex-wrap gap-2 mb-4 justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success btn-sm term-btn active" data-term="1" onclick="switchTerm('1')">1º Trimestre</button>
                                    <button type="button" class="btn btn-outline-success btn-sm term-btn" data-term="2" onclick="switchTerm('2')">2º Trimestre</button>
                                    <button type="button" class="btn btn-outline-success btn-sm term-btn" data-term="3" onclick="switchTerm('3')">3º Trimestre</button>
                                    <button type="button" class="btn btn-outline-success btn-sm term-btn" data-term="annual" onclick="switchTerm('annual')">Ficha Anual</button>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('students.grades.pdf', ['student' => $student->id, 'term' => 1]) }}" id="pdfDownloadBtn" class="btn btn-success btn-sm rounded-pill px-3">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                    </a>
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#shareMailModal">
                                        <i class="fas fa-envelope me-1"></i> E-mail
                                    </button>
                                    <a href="#" id="whatsappShareBtn" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3" style="border-color: #25D366; color: #25D366;">
                                        <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                    </a>
                                </div>
                            </div>

                            <!-- ── 1º TRIMESTRE TABLE ── -->
                            <div class="term-table-wrapper" id="termTable-1">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Disciplina</th>
                                                <th class="text-center">ACS 1</th>
                                                <th class="text-center">ACS 2</th>
                                                <th class="text-center">ACS 3</th>
                                                <th class="text-center">Média ACS</th>
                                                <th class="text-center">ACP</th>
                                                <th class="text-center">Média Trim (MT)</th>
                                                <th class="text-center">Aproveitamento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matrix as $subjectId => $data)
                                                @php $tData = $data['terms'][1]; @endphp
                                                <tr>
                                                    <td class="fw-bold">{{ $data['subject']->name }}</td>
                                                    <td class="text-center">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                    <td class="text-center fw-bold">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="badge bg-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }}">
                                                                {{ number_format($tData['mt'], 1) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="text-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }} fw-bold">{{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">Sem disciplinas ou notas disponíveis.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ── 2º TRIMESTRE TABLE ── -->
                            <div class="term-table-wrapper" id="termTable-2" style="display:none;">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Disciplina</th>
                                                <th class="text-center">ACS 1</th>
                                                <th class="text-center">ACS 2</th>
                                                <th class="text-center">ACS 3</th>
                                                <th class="text-center">Média ACS</th>
                                                <th class="text-center">ACP</th>
                                                <th class="text-center">Média Trim (MT)</th>
                                                <th class="text-center">Aproveitamento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matrix as $subjectId => $data)
                                                @php $tData = $data['terms'][2]; @endphp
                                                <tr>
                                                    <td class="fw-bold">{{ $data['subject']->name }}</td>
                                                    <td class="text-center">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                    <td class="text-center fw-bold">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="badge bg-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }}">
                                                                {{ number_format($tData['mt'], 1) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="text-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }} fw-bold">{{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">Sem disciplinas ou notas disponíveis.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ── 3º TRIMESTRE TABLE ── -->
                            <div class="term-table-wrapper" id="termTable-3" style="display:none;">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Disciplina</th>
                                                <th class="text-center">ACS 1</th>
                                                <th class="text-center">ACS 2</th>
                                                <th class="text-center">ACS 3</th>
                                                <th class="text-center">Média ACS</th>
                                                <th class="text-center">ACP</th>
                                                <th class="text-center">Média Trim (MT)</th>
                                                <th class="text-center">Aproveitamento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matrix as $subjectId => $data)
                                                @php $tData = $data['terms'][3]; @endphp
                                                <tr>
                                                    <td class="fw-bold">{{ $data['subject']->name }}</td>
                                                    <td class="text-center">{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                                                    <td class="text-center fw-bold">{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="badge bg-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }}">
                                                                {{ number_format($tData['mt'], 1) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($tData['mt'] !== null)
                                                            <span class="text-{{ $tData['mt'] >= 10 ? 'success' : 'danger' }} fw-bold">{{ $tData['mt'] >= 10 ? 'Positiva' : 'Negativa' }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">Sem disciplinas ou notas disponíveis.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ── ANUAL TABLE ── -->
                            <div class="term-table-wrapper" id="termTable-annual" style="display:none;">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Disciplina</th>
                                                <th class="text-center">MT1</th>
                                                <th class="text-center">MT2</th>
                                                <th class="text-center">MT3</th>
                                                <th class="text-center">Média Anual (MF)</th>
                                                <th class="text-center">Exame</th>
                                                <th class="text-center">Média Geral (MGD)</th>
                                                <th class="text-center">Situação Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($matrix as $subjectId => $data)
                                                @php
                                                    $annual = $data['annual'];
                                                    $isPositive = $annual['mfd'] !== null && $annual['mfd'] >= 10;
                                                @endphp
                                                <tr>
                                                    <td class="fw-bold">{{ $data['subject']->name }}</td>
                                                    <td class="text-center">{{ $annual['mt1'] !== null ? number_format($annual['mt1'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $annual['mt2'] !== null ? number_format($annual['mt2'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $annual['mt3'] !== null ? number_format($annual['mt3'], 1) : '-' }}</td>
                                                    <td class="text-center fw-bold">{{ $annual['mf'] !== null ? number_format($annual['mf'], 1) : '-' }}</td>
                                                    <td class="text-center">{{ $annual['exam'] !== null ? number_format($annual['exam'], 1) : '-' }}</td>
                                                    <td class="text-center">
                                                        @if($annual['mfd'] !== null)
                                                            <span class="badge bg-{{ $isPositive ? 'success' : 'danger' }}">
                                                                {{ number_format($annual['mfd'], 1) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($annual['status'] === 'Aprovado')
                                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-bold">Aprovado</span>
                                                        @elseif($annual['status'] === 'Reprovado')
                                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 font-bold">Reprovado</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 font-bold">Em Curso</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">Sem disciplinas ou notas disponíveis.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: ASSIDUIDADE -->
                        <div class="tab-pane fade" id="frequencia" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Turma</th>
                                            <th>Status</th>
                                            <th>Observações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($student->attendances as $att)
                                            <tr>
                                                <td>{{ $att->attendance_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                                <td>{{ $att->class->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $att->status === 'present' ? 'success' : ($att->status === 'absent' ? 'danger' : 'warning') }}">
                                                        {{ $att->status === 'present' ? 'Presente' : ($att->status === 'absent' ? 'Ausente' : 'Atrasado') }}
                                                    </span>
                                                </td>
                                                <td>{{ $att->notes ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-calendar-times fa-2x mb-2 text-slate-300"></i>
                                                    <p class="mb-0">Nenhum registo de assiduidade encontrado.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 4: FINANCEIRO / TAXAS -->
                        <div class="tab-pane fade" id="financeiro" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Referência</th>
                                            <th>Tipo</th>
                                            <th>Mês/Ano</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($student->payments->filter(fn($pay) => is_private_school() || $pay->type !== 'mensalidade') as $pay)
                                            <tr>
                                                <td class="font-mono"><code>{{ $pay->reference_number }}</code></td>
                                                <td class="fw-bold">{{ ucfirst($pay->type) }}</td>
                                                <td>{{ $pay->month ? $pay->month.'/'.$pay->year : '-' }}</td>
                                                <td class="fw-bold text-success">{{ number_format($pay->amount, 2, ',', '.') }} MT</td>
                                                <td>
                                                    <span class="badge bg-{{ $pay->status === 'paid' ? 'success' : ($pay->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ $pay->status === 'paid' ? 'Pago' : ($pay->status === 'pending' ? 'Pendente' : 'Atrasado') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-receipt fa-2x mb-2 text-slate-300"></i>
                                                    <p class="mb-0">Nenhum registo de taxas/pagamentos encontrado.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 5: TIMELINE DE EVENTOS -->
                        <div class="tab-pane fade" id="timeline" role="tabpanel">
                            <div class="position-relative ps-4 border-start border-2 border-emerald-200 ms-3 space-y-4">
                                @forelse($timelineEvents as $event)
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-{{ $event['color'] }} text-white d-flex align-items-center justify-content-center"
                                             style="width: 28px; height: 28px; left: -17px !important;">
                                            <i class="{{ $event['icon'] }} text-xs"></i>
                                        </div>
                                        <div class="ps-3 p-3 bg-light rounded-3 border">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold mb-0 text-slate-800">{{ $event['title'] }}</h6>
                                                <small class="text-xs text-muted font-mono">{{ $event['date']?->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <p class="text-xs text-slate-600 mb-0">{{ $event['description'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-4">Sem eventos na linha do tempo.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Share via Mail Modal -->
<div class="modal fade" id="shareMailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <div class="modal-header bg-success text-white" style="border-radius:1rem 1rem 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-envelope-open-text me-2"></i> Enviar Boletim por E-mail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('students.grades.share-email', $student) }}" method="POST">
                @csrf
                <input type="hidden" name="term" id="modalTermInput" value="1">
                <div class="modal-body p-4">
                    <p class="text-xs text-muted mb-3">O boletim oficial será gerado em formato PDF e anexado diretamente ao e-mail enviado ao encarregado.</p>
                    <div class="mb-3">
                        <label class="form-label text-slate-700 fw-bold">E-mail do Encarregado</label>
                        <input type="email" name="email" class="form-control" value="{{ $student->parent->email ?? '' }}" placeholder="exemplo@email.com" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3" style="border-radius:0 0 1rem 1rem;">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                        <i class="fas fa-paper-plane me-1"></i> Enviar Agora
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SMS Modal -->
@if ($student->parent && $student->parent->phone)
<div class="modal fade" id="smsModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <form action="{{ route('students.sms.send', $student) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white" style="border-radius:1rem 1rem 0 0;">
                    <h5 class="modal-title font-bold">
                        <i class="fas fa-comment-dots me-2"></i> Enviar SMS ao Encarregado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="alert alert-info border-0 rounded-xl mb-3 d-flex align-items-center gap-2 text-xs">
                        <i class="fas fa-user"></i>
                        <div>
                            <strong>{{ $student->parent->first_name }} {{ $student->parent->last_name }}</strong><br>
                            <span class="font-mono">{{ $student->parent->phone }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Telefone (opcional)</label>
                        <input type="text" name="phone" class="form-control rounded-xl"
                            placeholder="Deixe vazio para usar o do encarregado"
                            value="{{ $student->parent->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Mensagem *</label>
                        <textarea id="smsMsg{{ $student->id }}" name="message" rows="5" required maxlength="480" class="form-control rounded-xl"
                            placeholder="Use {aluno} para o nome do aluno, {escola} para o nome da escola..."></textarea>
                        <small class="text-slate-500 text-[11px]">Máx. 480 caracteres. Será enviado via httpSMS.</small>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
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
                            <button type="button" class="badge bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer hover:bg-slate-200"
                                onclick="document.getElementById('smsMsg{{ $student->id }}').value = @js($tpl);">
                                {{ mb_substr($tpl, 0, 35) }}…
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4" style="border-radius:0 0 1rem 1rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-xl" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info btn-sm rounded-xl font-semibold text-white">
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
                backgroundColor: '#047857',
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
                backgroundColor: attTot > 0 ? ['#10b981', '#ef4444', '#f59e0b'] : ['#cbd5e1']
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
            btn.classList.toggle('active', btn.dataset.term === term);
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
