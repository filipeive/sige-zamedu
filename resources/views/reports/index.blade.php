@extends('layouts.app')

@section('title', 'Central de Relatórios & Analytics')
@section('page-title', 'Central de Relatórios')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
    <div class="space-y-6 bg-[var(--content-bg)] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Top Header Banner - Theme Engine Parametrizado -->
        <div class="rounded-3xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6"
             style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 60%, var(--secondary-dark) 100%);">
            
            <div class="relative z-10">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-extrabold text-white shadow-xs"
                          style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                        <i class="fas fa-microchip text-amber-300"></i> ZAMEDU Analytics Engine
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold text-white shadow-xs"
                          style="background: rgba(16, 185, 129, 0.25); backdrop-filter: blur(8px); border: 1px solid rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-check-circle text-emerald-300"></i> Dados em Tempo Real
                    </span>
                </div>
                <h2 class="mt-3 text-2xl lg:text-3xl font-black font-heading text-white tracking-tight">
                    Central de Relatórios & Analytics
                </h2>
                <p class="text-xs lg:text-sm text-slate-100 max-w-2xl mt-1 leading-relaxed opacity-90">
                    Balanços financeiros, rendimento académico, estatísticas de assiduidade e relatórios exportáveis com identidade visual oficial.
                </p>
            </div>
            
            <!-- Quick Export Buttons Bar -->
            <div class="flex flex-wrap items-center gap-2 relative z-10 shrink-0">
                <a href="{{ route('reports.export.students') }}" class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold text-white border transition shadow-sm"
                   style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-file-csv text-amber-300"></i> Alunos (CSV)
                </a>
                
                @if(is_private_school())
                <a href="{{ route('reports.export.payments') }}" class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-black text-white shadow-md transition"
                   style="background: var(--secondary); border: 1px solid var(--secondary-light);">
                    <i class="fas fa-file-excel"></i> Pagamentos (CSV)
                </a>
                @endif

                <a href="{{ route('reports.export.grades') }}" class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-black text-white shadow-md transition"
                   style="background: var(--primary); border: 1px solid var(--primary-light);">
                    <i class="fas fa-graduation-cap"></i> Pauta Geral (CSV)
                </a>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-2xl bg-white text-slate-900 hover:bg-slate-100 px-4 py-2.5 text-xs font-black shadow-md transition">
                    <i class="fas fa-print text-slate-700"></i> Imprimir
                </button>
            </div>

            <!-- Background Decorative Glow -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full blur-3xl pointer-events-none opacity-20"
                 style="background: var(--accent);"></div>
        </div>

        <!-- Stat Summary Cards Grid (4 KPI Cards com Cores Parametrizadas) -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1: Alunos Ativos -->
            <div class="bg-[var(--card-bg)] rounded-2xl p-4 border border-[var(--border-color)] shadow-xs flex items-center gap-4 hover:shadow-md transition">
                <div class="w-2 h-10 rounded-full shrink-0" style="background: var(--primary);"></div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg font-bold shrink-0 shadow-xs"
                     style="background: var(--primary-soft); color: var(--primary);">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Estudantes Ativos</span>
                    <span class="text-2xl font-black text-[var(--text-primary)] mt-0.5 block">{{ number_format($stats['total_students']) }}</span>
                </div>
            </div>

            <!-- Card 2: Professores -->
            <div class="bg-[var(--card-bg)] rounded-2xl p-4 border border-[var(--border-color)] shadow-xs flex items-center gap-4 hover:shadow-md transition">
                <div class="w-2 h-10 rounded-full shrink-0" style="background: var(--secondary);"></div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg font-bold shrink-0 shadow-xs"
                     style="background: var(--secondary-soft); color: var(--secondary);">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Corpo Docente</span>
                    <span class="text-2xl font-black text-[var(--text-primary)] mt-0.5 block">{{ number_format($stats['total_teachers']) }}</span>
                </div>
            </div>

            <!-- Card 3: Turmas Ativas -->
            <div class="bg-[var(--card-bg)] rounded-2xl p-4 border border-[var(--border-color)] shadow-xs flex items-center gap-4 hover:shadow-md transition">
                <div class="w-2 h-10 rounded-full shrink-0" style="background: var(--accent);"></div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg font-bold shrink-0 shadow-xs"
                     style="background: var(--accent-light); color: var(--accent);">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Turmas em Funcionamento</span>
                    <span class="text-2xl font-black text-[var(--text-primary)] mt-0.5 block">{{ number_format($stats['total_classes']) }}</span>
                </div>
            </div>

            <!-- Card 4: Receita ou Modalidade -->
            <div class="bg-[var(--card-bg)] rounded-2xl p-4 border border-[var(--border-color)] shadow-xs flex items-center gap-4 hover:shadow-md transition">
                <div class="w-2 h-10 rounded-full shrink-0" style="background: var(--success);"></div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg font-bold shrink-0 shadow-xs"
                     style="background: var(--success-soft); color: var(--success);">
                    <i class="fas {{ is_public_school() ? 'fa-landmark' : 'fa-wallet' }}"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">{{ is_public_school() ? 'Modalidade' : 'Arrecadação do Mês' }}</span>
                    <span class="text-2xl font-black text-[var(--text-primary)] mt-0.5 block">
                        @if(is_public_school())
                            <span class="text-base font-extrabold" style="color: var(--primary);">Ensino Público</span>
                        @else
                            {{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') }} <small class="text-xs text-[var(--text-muted)] font-normal">MT</small>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Interactive Analytics Charts Grid -->
        <div class="grid gap-6 lg:grid-cols-2">
            
            <!-- Chart 1: Arrecadação Financeira / Mês -->
            @if(is_private_school())
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-sm border border-[var(--border-color)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
                    <div>
                        <h3 class="text-base font-black text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-chart-line" style="color: var(--primary);"></i> Evolução Financeira de Receitas
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Montante arrecadado nos últimos 6 meses</p>
                    </div>
                    <span class="badge px-3 py-1 rounded-full text-xs font-extrabold"
                          style="background: var(--primary-soft); color: var(--primary); border: 1px solid var(--primary-light);">
                        Financeiro
                    </span>
                </div>
                <div class="relative w-full h-[260px]">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
            @else
            <!-- Fallback Public School Academic Overview Chart -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-sm border border-[var(--border-color)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
                    <div>
                        <h3 class="text-base font-black text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-landmark" style="color: var(--primary);"></i> Gestão de Vagas por Nível
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Indicador de ocupação do recinto escolar</p>
                    </div>
                    <span class="badge px-3 py-1 rounded-full text-xs font-extrabold"
                          style="background: var(--primary-soft); color: var(--primary); border: 1px solid var(--primary-light);">
                        Público
                    </span>
                </div>
                <div class="flex flex-col justify-center items-center h-[260px] text-center p-6 rounded-2xl border border-dashed"
                     style="background: var(--surface-bg); border-color: var(--border-color);">
                    <i class="fas fa-building-columns text-4xl mb-3" style="color: var(--primary);"></i>
                    <h4 class="font-extrabold text-[var(--text-primary)]">Instituição Isenta de Propinas</h4>
                    <p class="text-xs text-[var(--text-muted)] max-w-sm mt-1">
                        Os dados operacionais de matrículas gratuitas e inscrições públicas são consolidados no módulo académico.
                    </p>
                </div>
            </div>
            @endif

            <!-- Chart 2: Demografia por Género -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-sm border border-[var(--border-color)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
                    <div>
                        <h3 class="text-base font-black text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-users" style="color: var(--secondary);"></i> Distribuição por Género
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Proporção de estudantes masculinos e femininos</p>
                    </div>
                    <span class="badge px-3 py-1 rounded-full text-xs font-extrabold"
                          style="background: var(--secondary-soft); color: var(--secondary); border: 1px solid var(--secondary-light);">
                        Demografia
                    </span>
                </div>
                <div class="relative w-full h-[260px] flex items-center justify-center">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Distribuição de Turmas por Nível -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-sm border border-[var(--border-color)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
                    <div>
                        <h3 class="text-base font-black text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-layer-group" style="color: var(--accent);"></i> Turmas por Nível Escolar
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Quantidade de turmas ativas da 1ª à 12ª classe</p>
                    </div>
                    <span class="badge px-3 py-1 rounded-full text-xs font-extrabold"
                          style="background: var(--accent-light); color: var(--accent); border: 1px solid var(--accent);">
                        Académico
                    </span>
                </div>
                <div class="relative w-full h-[260px]">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Assiduidade Global -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-sm border border-[var(--border-color)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[var(--border-color)]">
                    <div>
                        <h3 class="text-base font-black text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-calendar-check" style="color: var(--success);"></i> Taxa Global de Assiduidade
                        </h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Distribuição de presenças, ausências e atrasos</p>
                    </div>
                    <span class="badge px-3 py-1 rounded-full text-xs font-extrabold"
                          style="background: var(--success-soft); color: var(--success); border: 1px solid var(--success-dark);">
                        Frequência
                    </span>
                </div>
                <div class="relative w-full h-[260px] flex items-center justify-center">
                    <canvas id="globalAttendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Categorized Detailed Reports Cards Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            
            <!-- Category 1: Relatórios Académicos -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[var(--border-color)]">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xs"
                             style="background: var(--primary-soft); color: var(--primary);">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] font-heading">Relatórios Académicos</h3>
                            <p class="text-xs text-[var(--text-muted)]">Rendimento escolar, turmas, pautas e assiduidade</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('reports.academic') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--primary);">
                                    <i class="fas fa-list-ul text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Visão Geral Académica</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Resumo de turmas, lotação e médias</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('reports.academic.performance') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--secondary);">
                                    <i class="fas fa-chart-line text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Desempenho por Disciplina & Alunos</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Filtro de notas por trimestre e turma</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('reports.pedagogical-map') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs text-emerald-600">
                                    <i class="fas fa-table text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block flex items-center gap-1.5">
                                        Mapa de Aproveitamento Pedagógico (MINEDH)
                                        <span class="badge bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.5 rounded">HM / M</span>
                                    </span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Aproveitamento estatístico por sexo para Inspeção Escolar</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('reports.academic.attendance') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--accent);">
                                    <i class="fas fa-calendar-check text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Relatório de Assiduidade</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Histórico de chamadas e faltas justificadas</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category 2: Relatórios Financeiros -->
            @if(is_private_school())
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[var(--border-color)]">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xs"
                             style="background: var(--secondary-soft); color: var(--secondary);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] font-heading">Relatórios Financeiros</h3>
                            <p class="text-xs text-[var(--text-muted)]">Balanço de propinas, despesas e mensalidades em atraso</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('reports.financial') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--secondary);">
                                    <i class="fas fa-receipt text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Visão Geral Financeira</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Balanço de receitas vs despesas e resultado</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('reports.financial.revenue') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--primary);">
                                    <i class="fas fa-money-bill-wave text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Receitas & Liquidações</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Transações efetuadas por período e método</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('reports.financial.defaulters') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white text-rose-600 flex items-center justify-center shadow-xs">
                                    <i class="fas fa-exclamation-triangle text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Inadimplência & Devedores</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Lista de alunos com mensalidades pendentes</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @else
            <!-- Public School Quick Documents Panel -->
            <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[var(--border-color)]">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xs"
                             style="background: var(--primary-soft); color: var(--primary);">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] font-heading">Documentos & Certificados</h3>
                            <p class="text-xs text-[var(--text-muted)]">Emissão oficial de documentos e pautas públicas</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('certificates.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--primary);">
                                    <i class="fas fa-award text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Emitir Certidão & Certificados</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Certidões de matrícula com Emblema Nacional</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>

                        <a href="{{ route('students.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl transition group border"
                           style="background: var(--surface-bg); border-color: var(--border-color);">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-xs" style="color: var(--secondary);">
                                    <i class="fas fa-users text-xs"></i>
                                </span>
                                <div>
                                    <span class="text-sm font-extrabold text-[var(--text-primary)] group-hover:opacity-80 block">Listagem Geral de Alunos</span>
                                    <span class="text-[11px] text-[var(--text-muted)] block">Fichas individuais e cartões de estudante</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-[var(--text-muted)]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js não foi carregado.');
        return;
    }

    // Leitura Dinâmica dos Tokens de Cores das Definições do Sistema
    const getCssVar = (name, fallback) => {
        const val = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return val ? val : fallback;
    };

    const primaryColor = getCssVar('--primary', '#1e40af');
    const secondaryColor = getCssVar('--secondary', '#059669');
    const accentColor = getCssVar('--accent', '#f59e0b');
    const successColor = getCssVar('--success', '#10b981');
    const dangerColor = getCssVar('--danger', '#ef4444');
    const infoColor = getCssVar('--info', '#06b6d4');

    // 1. Gráfico de Evolução Financeira (Com Guard e Cores de Settings)
    const revenueEl = document.getElementById('revenueTrendChart');
    if (revenueEl) {
        new Chart(revenueEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($monthsLabels),
                datasets: [{
                    label: 'Receita (MT)',
                    data: @json($monthlyRevenueTrend),
                    borderColor: primaryColor,
                    backgroundColor: primaryColor + '20',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 5,
                    pointBackgroundColor: primaryColor,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Receita: ' + context.parsed.y.toLocaleString('pt-MZ') + ' MT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Género (Com Cores do Theme Engine: Primary + Secondary)
    const genderEl = document.getElementById('genderChart');
    if (genderEl) {
        const maleCount = {{ $genderStats['male'] ?? 0 }};
        const femaleCount = {{ $genderStats['female'] ?? 0 }};
        const hasData = (maleCount + femaleCount) > 0;

        new Chart(genderEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: hasData ? ['Masculino', 'Feminino'] : ['Sem Alunos'],
                datasets: [{
                    data: hasData ? [maleCount, femaleCount] : [1],
                    backgroundColor: hasData ? [primaryColor, secondaryColor] : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12, weight: 'bold' } } }
                }
            }
        });
    }

    // 3. Gráfico de Distribuição por Nível de Ensino (Com Cor Accent de Settings)
    const gradeEl = document.getElementById('gradeDistributionChart');
    if (gradeEl) {
        const gradeData = @json($gradeDistribution);
        const gradeLabels = Object.keys(gradeData);
        const gradeValues = Object.values(gradeData);
        const hasGrades = gradeLabels.length > 0;

        new Chart(gradeEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hasGrades ? gradeLabels : ['Sem Turmas'],
                datasets: [{
                    label: 'Turmas Ativas',
                    data: hasGrades ? gradeValues : [0],
                    backgroundColor: accentColor,
                    borderRadius: 8,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 4. Gráfico de Assiduidade Global (Com Cores de Sucesso, Perigo, Alerta e Info)
    const attEl = document.getElementById('globalAttendanceChart');
    if (attEl) {
        const attPresent = {{ $attendanceStats['present'] ?? 0 }};
        const attAbsent = {{ $attendanceStats['absent'] ?? 0 }};
        const attLate = {{ $attendanceStats['late'] ?? 0 }};
        const attExcused = {{ $attendanceStats['excused'] ?? 0 }};
        const attTotal = attPresent + attAbsent + attLate + attExcused;

        new Chart(attEl.getContext('2d'), {
            type: 'pie',
            data: {
                labels: attTotal > 0 ? ['Presente', 'Ausente', 'Atrasado', 'Justificado'] : ['Sem Registos'],
                datasets: [{
                    data: attTotal > 0 ? [attPresent, attAbsent, attLate, attExcused] : [1],
                    backgroundColor: attTotal > 0 ? [successColor, dangerColor, warningColor, infoColor] : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11, weight: 'bold' } } }
                }
            }
        });
    }
});
</script>
@endpush