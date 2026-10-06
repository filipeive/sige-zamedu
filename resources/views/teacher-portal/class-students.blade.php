@extends('layouts.app')

@section('title', 'Alunos da Turma')
@section('page-title', 'Alunos — ' . ($class->name ?? 'Turma')
@section('page-title-icon', 'fas fa-users')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}">Minhas Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>
@endsection

)
@section('content')

    <!-- Lista de Alunos -->
    <div class="school-card">
        <div class="school-card-header">
            <i class="fas fa-users"></i>
            Alunos — {{ $class->name }}
            <span class="badge bg-secondary ms-2">{{ $students->count() }} alunos</span>
        </div>
        <div class="school-card-body">
            @if($students->isEmpty())
                <p class="text-muted text-center py-4">
                    <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                    Nenhum aluno nesta turma.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Nº</th>
                                <th>Classe</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm me-2">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <small class="text-muted">{{ $student->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->student_number ?? '—' }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('teacher.attendance.class', $class->id) }}"
                                            class="btn btn-sm btn-outline-success" title="Presenças">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <a href="{{ route('teacher.gradebook', $class->id) }}?student_id={{ $student->id }}"
                                            class="btn btn-sm btn-outline-warning" title="Notas">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <style>
        .user-avatar-sm {
            width: 35px;
            height: 35px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endsection