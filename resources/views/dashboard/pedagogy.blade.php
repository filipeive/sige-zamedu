@extends('layouts.app')

@section('title', 'Dashboard Pedagógico')
@section('page-title', 'Dashboard Pedagógico')
@section('page-title-icon', 'fas fa-graduation-cap')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <button type="button"
        class="inline-flex items-center gap-2 rounded-full bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-800 transition"
        onclick="window.location.reload()">
        <i class="fas fa-sync-alt"></i>
        Atualizar
    </button>
@endsection

@push('styles')
<style>
    .dash-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .dash-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }
    .dash-card-flat {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: none;
    }
    .dash-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dash-section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .dash-section-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }
    .dash-kpi-value {
        font-size: 1.6rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
    }
    .dash-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 9999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .dash-quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .dash-quick-action:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .dash-quick-action i {
        font-size: 1.1rem;
        color: #059669;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <!-- Header Contextual -->
    <div class="dash-card-flat">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <p class="dash-kpi-label" style="margin-bottom:0.25rem;">{{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}</p>
                <h1 class="dash-kpi-value" style="font-size:1.4rem;">{{ $greetingData['greeting'] }}</h1>
            </div>
            <div class="text-end">
                <p class="dash-kpi-label">{{ $greetingData['current_date'] }}</p>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <section class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Total Alunos</p>
                        <p class="dash-kpi-value">{{ number_format($stats['total_students']) }}</p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">
                            <i class="fas fa-circle" style="font-size:0.45rem; color:#10b981;"></i> {{ $stats['total_classes'] }} turmas
                        </span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#e0e7ff; color:#4f46e5;"><i class="fas fa-user-graduate"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Professores</p>
                        <p class="dash-kpi-value">{{ number_format($stats['total_teachers']) }}</p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">{{ $stats['total_subjects'] }} disciplinas</span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#dbeafe; color:#2563eb;"><i class="fas fa-chalkboard-user"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Presença Média</p>
                        <p class="dash-kpi-value">{{ $stats['average_attendance'] }}%</p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">Este mês</span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#ccfbf1; color:#0f766e;"><i class="fas fa-clipboard-check"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Média Global</p>
                        <p class="dash-kpi-value">{{ $stats['class_performance_avg'] }} <span style="font-size:0.75rem; font-weight:600; color:#64748b;">/ 20</span></p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">Desempenho</span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#f3e8ff; color:#7c3aed;"><i class="fas fa-award"></i></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Desempenho por Turma -->
    <section>
        <div class="dash-card-flat">
            <div class="dash-section">
                <div>
                    <p class="dash-section-title">Desempenho por Turma</p>
                    <p class="dash-section-subtitle">Média geral e progresso</p>
                </div>
            </div>
            <div class="dash-collapse-content">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Professor</th>
                                <th>Alunos</th>
                                <th>Média</th>
                                <th style="width:140px;">Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classPerformance as $class)
                                @php
                                    $cName = $class['name'] ?? $class['class_name'] ?? 'Turma';
                                    $tName = $class['teacher_name'] ?? 'N/A';
                                    $sCount = $class['student_count'] ?? $class['total_students'] ?? 0;
                                    $avgGrade = $class['avg_grade'] ?? $class['average_grade'] ?? 0;
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $cName }}</td>
                                    <td class="text-muted">{{ $tName }}</td>
                                    <td>{{ $sCount }}</td>
                                    <td>
                                        <span class="dash-badge" style="background:#dcfce7; color:#166534;">{{ number_format($avgGrade, 1) }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height:8px; background:#f1f5f9; border-radius:9999px;">
                                            <div class="progress-bar bg-success" style="width: {{ min(100, ($avgGrade / 20) * 100) }}%; border-radius:9999px;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Próximos Exames / Eventos -->
    <section class="row g-3">
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-section">
                    <div>
                        <p class="dash-section-title">Próximos Exames</p>
                        <p class="dash-section-subtitle">Avaliações agendadas</p>
                    </div>
                </div>
                <div class="dash-collapse-content">
                    @forelse($upcomingExams as $exam)
                        <div class="d-flex align-items-center justify-content-between rounded-xl p-3 mb-2" style="background:#f8fafc; border:1px solid #f1f5f9;">
                            <div>
                                <p class="fw-bold mb-0" style="font-size:0.85rem;">{{ $exam->title }}</p>
                                <p class="mb-0" style="font-size:0.75rem; color:#64748b;">
                                    <i class="far fa-clock me-1"></i> {{ $exam->event_date->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="dash-badge" style="background:#fef3c7; color:#92400e;">Exame</span>
                        </div>
                    @empty
                        <p class="text-center py-4 mb-0" style="font-size:0.85rem; color:#94a3b8;">Nenhum exame agendado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            @include('partials.calendar')
        </div>
    </section>

</div>
@endsection
