@extends('layouts.app')

@section('title', 'Arquivo - ' . $student->full_name)
@section('page-title', 'Arquivo: ' . $student->full_name)
@section('page-title-icon', 'fas fa-archive')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Matrículas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.students-archive.index') }}">Alunos Arquivados</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 text-center">
                @if ($student->passport_photo)
                    <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}"
                        class="rounded-circle shadow-md border border-4 border-white mb-3"
                        style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-md mb-3"
                        style="width: 120px; height: 120px; font-size: 40px; font-weight: 800;">
                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                    </div>
                @endif
                <h4 class="fw-bold mb-1 text-slate-800">{{ $student->full_name }}</h4>
                <p class="text-muted small font-mono">{{ $student->student_number }}</p>

                @php
                    $statusColors = [
                        'transferred' => 'bg-info text-white',
                        'graduated' => 'bg-success text-white',
                        'inactive' => 'bg-secondary text-white',
                    ];
                    $statusLabels = [
                        'transferred' => 'Transferido',
                        'graduated' => 'Formado',
                        'inactive' => 'Desistente',
                    ];
                @endphp
                <span class="badge {{ $statusColors[$student->status] ?? 'bg-secondary text-white' }} px-3 py-1.5 rounded-pill text-xs font-semibold">
                    <i class="fas fa-archive me-1"></i> {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                </span>

                <hr>

                <div class="text-start text-sm space-y-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Idade:</span>
                        <strong>{{ $student->age ?? 'N/A' }} anos</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Gênero:</span>
                        <strong>{{ $student->gender === 'M' ? 'Masculino' : ($student->gender === 'F' ? 'Feminino' : 'N/A') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Contacto:</span>
                        <strong class="font-mono">{{ $student->emergency_phone ?? 'N/A' }}</strong>
                    </div>
                    @if ($student->parent)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Encarregado:</span>
                            <strong>{{ $student->parent->first_name }} {{ $student->parent->last_name }}</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Arquivado em:</span>
                        <strong>{{ $student->updated_at?->format('d/m/Y') ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-success rounded-pill py-2 text-xs font-bold shadow-xs"
                        data-bs-toggle="modal" data-bs-target="#reactivateModal">
                        <i class="fas fa-redo me-1.5"></i> Reativar Aluno
                    </button>
                    <a href="{{ route('students.support.index', $student) }}"
                        class="btn btn-outline-warning rounded-pill py-2 text-xs font-bold shadow-xs">
                        <i class="fas fa-exclamation-triangle me-1.5"></i> Ver Histórico / Ocorrências
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold mb-0 text-slate-800"><i class="fas fa-school me-2 text-primary"></i>Histórico de Matrículas</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ano Lectivo</th>
                                <th>Turma</th>
                                <th>Estado</th>
                                <th>Data Matrícula</th>
                                <th>Mensalidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->enrollments as $enrollment)
                                <tr>
                                    <td><strong>{{ $enrollment->school_year }}</strong></td>
                                    <td>{{ $enrollment->class->name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $enrollStatus = [
                                                'active' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'completed' => 'bg-info',
                                                'cancelled' => 'bg-danger',
                                                'transferred' => 'bg-info',
                                            ];
                                        @endphp
                                        <span class="badge {{ $enrollStatus[$enrollment->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $enrollment->enrollment_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ number_format($enrollment->monthly_fee ?? 0, 2, ',', '.') }} MT</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 text-slate-300"></i>
                                        <p class="mb-0">Nenhuma matrícula registada.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold mb-0 text-slate-800"><i class="fas fa-comments me-2 text-info"></i>Observações Registadas</h5>
            </div>
            <div class="card-body p-4">
                @forelse($student->observations()->with('creator')->latest()->limit(10)->get() as $observation)
                    <div class="rounded-lg border border-slate-200 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-slate-500">
                                {{ $observation->created_at->format('d/m/Y H:i') }} por
                                {{ $observation->creator->name ?? 'Sistema' }}
                            </small>
                            @if ($observation->special_needs)
                                <span class="badge bg-amber-100 text-amber-700">Nec. especial</span>
                            @endif
                        </div>
                        <p class="mb-0 text-sm text-slate-700">{{ $observation->observations }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 mb-0 text-center py-3">Nenhuma observação registada.</p>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold mb-0 text-slate-800"><i class="fas fa-folder-open me-2 text-emerald-600"></i>Registos / Ocorrências</h5>
            </div>
            <div class="card-body p-4">
                @forelse($student->studentRecords()->with('creator')->latest('record_date')->limit(10)->get() as $record)
                    <div class="rounded-lg border border-slate-200 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-slate-500">
                                {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }} por
                                {{ $record->creator->name ?? 'Sistema' }}
                            </small>
                            <span class="badge bg-cyan-100 text-cyan-700">{{ $record->record_type_name }}</span>
                        </div>
                        <p class="mb-0 text-sm text-slate-700">{{ $record->record_details }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 mb-0 text-center py-3">Nenhuma ocorrência/registo encontrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reactivateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-start">
            <form action="{{ route('admin.students-archive.reactivate', $student) }}" method="POST">
                @csrf
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-slate-800">
                        <i class="fas fa-redo me-2 text-success"></i>
                        Reativar: {{ $student->full_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-xl mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        O aluno será reativado no ano letivo <strong>{{ current_school_year() }}</strong>.
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Nova Turma *</label>
                        <select name="class_id" class="form-select rounded-xl" required>
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\ClassRoom::where('school_year', current_school_year())->where('is_active', true)->get() as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} ({{ $class->grade_level_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Mensalidade (MT) *</label>
                            <input type="number" name="monthly_fee" class="form-control rounded-xl"
                                value="{{ $student->monthly_fee ?? 0 }}" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Observações</label>
                            <input type="text" name="reason" class="form-control rounded-xl"
                                placeholder="Motivo da reativação">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-xl" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-xl font-semibold">
                        <i class="fas fa-check me-1"></i> Confirmar Reativação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection