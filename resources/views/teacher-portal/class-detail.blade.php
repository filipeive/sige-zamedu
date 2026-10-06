@extends('layouts.app')

@section('title', 'Detalhes da Turma')
@section('page-title', 'Detalhes da Turma')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}">Minhas Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">{{ $class->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Cabeçalho da Turma -->
            <div class="school-card mb-4">
                <div class="school-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3>{{ $class->name }}</h3>
                            <p class="text-muted mb-0">
                                <strong>{{ $class->grade_level_name }}</strong> |
                                Ano Letivo: {{ $class->school_year }} |
                                Sala: {{ $class->classroom ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="class-avatar mx-auto">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Estatísticas da Turma -->
                <div class="col-md-4 mb-4">
                    <div class="school-card h-100">
                        <div class="school-card-header">
                            <i class="fas fa-chart-bar"></i>
                            Estatísticas
                        </div>
                        <div class="school-card-body">
                            <div class="text-center mb-4">
                                @php
                                    $capacityPercentage = $class->max_students > 0 
                                        ? round(($class->students_count / $class->max_students) * 100) 
                                        : 0;
                                @endphp
                                <div class="progress-circle mx-auto mb-3" data-percentage="{{ $capacityPercentage }}">
                                    <span class="progress-value">{{ $capacityPercentage }}%</span>
                                </div>
                                <h5>Capacidade</h5>
                                <p class="text-muted">{{ $class->students_count }}/{{ $class->max_students ?? 0 }} alunos</p>
                            </div>

                            <div class="stats-grid">
                                <div class="stat-item text-center">
                                    <div class="stat-number text-primary">{{ $class->students_count }}</div>
                                    <div class="stat-label">Alunos</div>
                                </div>
                                <div class="stat-item text-center">
                                    <div class="stat-number text-success">{{ $class->subjects->count() }}</div>
                                    <div class="stat-label">Disciplinas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Alunos -->
                <div class="col-md-8 mb-4">
                    <div class="school-card">
                        <div class="school-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-graduate"></i>
                                Lista de Alunos
                            </div>
                            <span class="badge bg-primary">{{ $class->students_count }} alunos</span>
                        </div>
                        <div class="school-card-body">
                            @if ($class->students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Aluno</th>
                                                <th>Presença Hoje</th>
                                                <th>Contacto</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($class->students as $student)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar-sm me-3">
                                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $student->student_number ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if (isset($todayAttendance[$student->id]))
                                                            @php
                                                                $attendance = $todayAttendance[$student->id];
                                                                $statusColors = [
                                                                    'present' => 'success',
                                                                    'absent' => 'danger',
                                                                    'late' => 'warning',
                                                                ];
                                                                $statusLabels = [
                                                                    'present' => 'Presente',
                                                                    'absent' => 'Ausente',
                                                                    'late' => 'Atrasado',
                                                                ];
                                                            @endphp
                                                            <span class="badge bg-{{ $statusColors[$attendance->status] ?? 'secondary' }}">
                                                                {{ $statusLabels[$attendance->status] ?? 'Desconhecido' }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Não registrado</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>
                                                            @if ($student->phone)
                                                                <i class="fas fa-phone text-muted"></i> {{ $student->phone }}<br>
                                                            @endif
                                                            @if ($student->emergency_phone)
                                                                <i class="fas fa-phone-alt text-muted"></i> {{ $student->emergency_phone }}
                                                            @endif
                                                            @if (!$student->phone && !$student->emergency_phone)
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('teacher.attendance.class', $class->id) }}" 
                                                               class="btn btn-sm btn-success" title="Registrar Presença">
                                                                <i class="fas fa-clipboard-check"></i>
                                                            </a>
                                                            <a href="{{ route('teacher.gradebook', $class->id) }}?student_id={{ $student->id }}" 
                                                               class="btn btn-sm btn-warning" title="Ver Notas">
                                                                <i class="fas fa-tasks"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-4">
                                    <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                    Nenhum aluno matriculado nesta turma
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disciplinas da Turma -->
            <div class="school-card">
                <div class="school-card-header">
                    <i class="fas fa-book"></i>
                    Disciplinas da Turma
                </div>
                <div class="school-card-body">
                    @if ($class->subjects->count() > 0)
                        <div class="row">
                            @foreach ($class->subjects as $subject)
                                <div class="col-md-4 mb-3">
                                    <div class="subject-card">
                                        <div class="subject-icon">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                        <div class="subject-info">
                                            <h6>{{ $subject->name }}</h6>
                                            <small class="text-muted">Código: {{ $subject->code ?? 'N/A' }}</small>
                                            <br>
                                            <small class="text-muted">{{ $subject->weekly_hours ?? 0 }}h/semana</small>
                                        </div>
                                        <div class="subject-actions mt-2">
                                            <a href="{{ route('teacher.gradebook', $class->id) }}?subject_id={{ $subject->id }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-tasks"></i> Notas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-book fa-2x mb-2"></i><br>
                            Nenhuma disciplina atribuída a esta turma
                        </p>
                    @endif
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="school-card mt-4">
                <div class="school-card-header">
                    <i class="fas fa-bolt"></i>
                    Ações Rápidas
                </div>
                <div class="school-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('teacher.attendance.class', $class->id) }}" 
                               class="btn btn-success-school w-100 h-100 py-3">
                                <i class="fas fa-clipboard-check fa-2x mb-2"></i><br>
                                Registrar Presenças
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.gradebook', $class->id) }}" 
                               class="btn btn-warning-school w-100 h-100 py-3">
                                <i class="fas fa-tasks fa-2x mb-2"></i><br>
                                Lançar Notas
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.classes.students', $class->id) }}" 
                               class="btn btn-info-school w-100 h-100 py-3">
                                <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                Ver Alunos
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.classes.index') }}" 
                               class="btn btn-secondary-school w-100 h-100 py-3">
                                <i class="fas fa-arrow-left fa-2x mb-2"></i><br>
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .class-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .user-avatar-sm {
            width: 35px;
            height: 35px;
            background: #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#3498db 0% var(--p, 0%), #f0f0f0 var(--p, 0%) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
        }

        .progress-value {
            position: relative;
            font-weight: 700;
            color: #3498db;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .stat-item {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .subject-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .subject-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .subject-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f39c12, #F57C00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto 15px;
        }

        .subject-info h6 {
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .subject-actions {
            margin-top: 15px;
        }

        .btn-success-school {
            background: linear-gradient(135deg, #27ae60, #219653);
            border: none;
            color: white;
        }

        .btn-warning-school {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border: none;
            color: white;
        }

        .btn-info-school {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
        }

        .btn-secondary-school {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            color: white;
        }

        .btn-success-school:hover,
        .btn-warning-school:hover,
        .btn-info-school:hover,
        .btn-secondary-school:hover {
            opacity: 0.9;
            color: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar círculos de progresso
            document.querySelectorAll('.progress-circle').forEach(circle => {
                const percentage = circle.getAttribute('data-percentage');
                circle.style.setProperty('--p', percentage + '%');
            });
        });
    </script>
@endsection