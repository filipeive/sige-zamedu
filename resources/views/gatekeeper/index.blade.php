@extends('layouts.app')

@section('title', 'Portaria Digital — Controlo de Acesso')
@section('page-title', 'Portaria Digital')
@section('page-title-icon', 'fas fa-id-card-clip')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Portaria Digital</li>
@endsection

@push('styles')
<style>
    #qr-reader {
        width: 100%;
        max-width: 360px;
        margin: 0 auto;
        border-radius: 1rem;
        overflow: hidden;
        border: 2px solid #059669;
    }
    #qr-reader video {
        border-radius: 0.75rem;
    }
    .scan-success-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(4px);
        z-index: 999999;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        color: white;
    }
    .scan-success-overlay.show { display: flex; }
    .live-indicator {
        display: inline-block;
        width: 8px; height: 8px;
        background: #10b981;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulse-live 1.5s infinite;
    }
    @keyframes pulse-live {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
        50% { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
    }

    /* Trail Timeline */
    .trail-timeline {
        position: relative;
        padding-left: 1.75rem;
    }
    .trail-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 4px;
        bottom: 4px;
        width: 2px;
        background: #e2e8f0;
    }
    .trail-item {
        position: relative;
        padding-bottom: 1rem;
    }
    .trail-item:last-child { padding-bottom: 0; }
    .trail-dot {
        position: absolute;
        left: -1.75rem;
        top: 2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        color: white;
        border: 2px solid white;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .trail-dot.entry { background: #10b981; }
    .trail-dot.exit { background: #ef4444; }
    .trail-date-separator {
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #047857;
        letter-spacing: 0.05em;
        padding: 0.5rem 0 0.25rem 0;
        border-bottom: 1px dashed #cbd5e1;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-full mx-auto space-y-4">

    <!-- 1. Stat KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Passagens Hoje</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="text-xl font-black text-slate-800 mt-1">{{ number_format($stats['total']) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Movimentações registadas</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Entradas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-right-to-bracket"></i>
                </div>
            </div>
            <div class="text-xl font-black text-emerald-700 mt-1">{{ number_format($stats['entries']) }}</div>
            <span class="text-[10px] text-emerald-600/80 font-medium">Entradas validadas</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Saídas</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
            </div>
            <div class="text-xl font-black text-rose-700 mt-1">{{ number_format($stats['exits']) }}</div>
            <span class="text-[10px] text-rose-600/80 font-medium">Saídas registadas</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status do Leitor</span>
                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
            <div class="text-xs font-extrabold text-emerald-700 mt-2 flex items-center">
                <span class="live-indicator"></span> SISTEMA OPERACIONAL
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Portaria conectada</span>
        </div>
    </div>

    <!-- 2. Main Layout Grid (Left: Scanner/Search & Verification - Right: History Table) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- Left Column: Scanner & Search Card -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Identification Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-qrcode text-emerald-600"></i> Identificação do Estudante
                    </h3>
                    <div class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200" id="liveClock">--:--:--</div>
                </div>
                
                <div class="p-4 space-y-4">
                    <!-- Mode Selector Buttons -->
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-lg">
                        <button type="button" class="py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-2xs" id="btnModeBarcode" onclick="switchMode('barcode')">
                            <i class="fas fa-barcode"></i> Leitor USB / Manual
                        </button>
                        <button type="button" class="py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800" id="btnModeCamera" onclick="switchMode('camera')">
                            <i class="fas fa-camera"></i> Câmera QR Code
                        </button>
                    </div>

                    <!-- Barcode / Manual Search Form -->
                    <div id="barcodeMode">
                        <form action="{{ route('gatekeeper.index') }}" method="GET" id="searchForm" class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Ler Cartão ou Digitar Nome / Nº *</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-slate-400 text-xs"><i class="fas fa-barcode"></i></span>
                                    <input type="text"
                                           name="search"
                                           id="barcodeInput"
                                           class="w-full rounded-lg border border-slate-200 pl-9 pr-3 py-2 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500"
                                           placeholder="Nº de estudante ou nome completo..."
                                           value="{{ request('search') }}"
                                           autofocus
                                           autocomplete="off">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-search"></i> Verificar Estudante
                            </button>
                        </form>
                        <p class="text-[10px] text-slate-400 text-center mt-2 flex items-center justify-center gap-1">
                            <i class="fas fa-info-circle"></i> Leitor USB ativo — leitura de código submete automaticamente.
                        </p>
                    </div>

                    <!-- Camera QR Mode -->
                    <div id="cameraMode" style="display:none;" class="space-y-3">
                        <div id="qr-reader"></div>
                        <div class="flex justify-center gap-2">
                            <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer" id="startCameraBtn" onclick="startCamera()">
                                <i class="fas fa-play"></i> Iniciar Câmera
                            </button>
                            <button class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer" id="stopCameraBtn" onclick="stopCamera()" style="display:none;">
                                <i class="fas fa-stop"></i> Parar Câmera
                            </button>
                        </div>
                        <form id="qrScanForm" action="{{ route('gatekeeper.scan') }}" method="POST" style="display:none;">
                            @csrf
                            <input type="hidden" name="qr_data" id="qrDataInput">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Student Validation Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-check text-emerald-600"></i> Dados da Validação
                    </h3>
                </div>
                
                <div class="p-4">
                    @if($searchedStudent)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                            <div class="flex items-center gap-3">
                                @if($searchedStudent->photo)
                                    <img src="{{ asset('storage/' . $searchedStudent->photo) }}" class="w-14 h-14 rounded-full object-cover border-2 border-emerald-600 shadow-2xs" alt="Foto">
                                @else
                                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-800 font-black flex items-center justify-center text-sm border-2 border-emerald-600 shadow-2xs">
                                        {{ strtoupper(substr($searchedStudent->first_name, 0, 1) . substr($searchedStudent->last_name ?? '', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $searchedStudent->full_name }}</h4>
                                    <code class="text-xs text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $studentNumber ?? $searchedStudent->student_number }}</code>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-200 pt-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Turma</span>
                                    <span class="font-bold text-slate-800">{{ $searchedStudent->currentEnrollment->class->name ?? 'Sem turma' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Encarregado</span>
                                    <span class="font-bold text-slate-800">{{ $searchedStudent->parent->full_name ?? $searchedStudent->parent->name ?? 'N/D' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Contacto Encarregado</span>
                                    <span class="font-bold text-slate-800 font-mono">{{ $searchedStudent->parent->phone ?? $searchedStudent->emergency_phone ?? 'N/D' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">Estado Matrícula</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $searchedStudent->currentEnrollment ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        {{ $searchedStudent->currentEnrollment ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Entry / Exit Actions -->
                            <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-200">
                                <form action="{{ route('gatekeeper.log', $searchedStudent) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="entry">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                                        <i class="fas fa-right-to-bracket"></i> Registar Entrada
                                    </button>
                                </form>
                                <form action="{{ route('gatekeeper.log', $searchedStudent) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="exit">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                                        <i class="fas fa-right-from-bracket"></i> Registar Saída
                                    </button>
                                </form>
                                <a href="{{ route('gatekeeper.card', $searchedStudent) }}" class="no-underline px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1">
                                    <i class="fas fa-id-card text-emerald-600"></i> Cartão
                                </a>
                                @if (auth()->user()?->can('view_students'))
                                    <a href="{{ route('students.show', $searchedStudent->id) }}" class="no-underline px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1">
                                        <i class="fas fa-eye text-slate-500"></i> Perfil
                                    </a>
                                @endif
                            </div>

                            <!-- Full Trail Modal Button -->
                            <button type="button" class="w-full py-2 px-3 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer mt-2"
                                    onclick="loadTrail({{ $searchedStudent->id }})">
                                <i class="fas fa-route"></i> Ver Rastreio Completo (Trail)
                            </button>
                        </div>

                        <!-- Mini Recent Movements -->
                        @if($studentTrail && $studentTrail->count() > 0)
                            <div class="mt-4 space-y-2">
                                <h6 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-timeline text-emerald-600"></i> Movimentações Recentes
                                </h6>
                                <div class="trail-timeline">
                                    @foreach($studentTrail->take(6) as $trail)
                                        <div class="trail-item">
                                            <div class="trail-dot {{ $trail->action }}">
                                                <i class="fas fa-{{ $trail->isEntry() ? 'arrow-right' : 'arrow-left' }}"></i>
                                            </div>
                                            <div class="flex items-center justify-between text-xs">
                                                <div>
                                                    <span class="font-bold {{ $trail->isEntry() ? 'text-emerald-700' : 'text-rose-700' }}">{{ $trail->action_label }}</span>
                                                    <span class="text-[10px] text-slate-400 ml-1">{{ $trail->method_label }}</span>
                                                </div>
                                                <span class="font-mono text-[10px] text-slate-500 font-bold">
                                                    {{ $trail->logged_at->format('d/m H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-slate-400 space-y-2">
                            <i class="fas fa-shield-halved text-4xl text-slate-300"></i>
                            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Aguardando Identificação</h5>
                            <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Pesquise por nome, nº de estudante ou faça a leitura automática no leitor óptico USB.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Today's Gate Logs Table -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Date Filter Box -->
            <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-2xs">
                <form action="{{ route('gatekeeper.index') }}" method="GET" class="flex items-center gap-3 flex-wrap">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 text-nowrap">
                        <i class="fas fa-calendar-day text-emerald-600"></i> Data:
                    </label>
                    <input type="date" name="date" class="rounded-lg border border-slate-200 text-xs py-1.5 px-2.5 font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500"
                        value="{{ $filterDate }}" onchange="this.form.submit()">
                    @if($filterDate !== now()->toDateString())
                        <a href="{{ route('gatekeeper.index', request('search') ? ['search' => request('search')] : []) }}" class="no-underline px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold transition flex items-center gap-1">
                            <i class="fas fa-calendar-check"></i> Hoje
                        </a>
                    @endif
                </form>
            </div>

            <!-- Gate Logs Table Container -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-history text-emerald-600"></i> Registo de Acessos
                        @if($filterDate === now()->toDateString())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="live-indicator"></span> Hoje
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ \Carbon\Carbon::parse($filterDate)->format('d/m/Y') }}
                            </span>
                        @endif
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-700 text-white">
                        {{ number_format($todayLogs->total()) }} registos
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                                <th class="py-2.5 px-4">Hora</th>
                                <th class="py-2.5 px-3">Estudante</th>
                                <th class="py-2.5 px-3">Turma</th>
                                <th class="py-2.5 px-3 text-center">Tipo</th>
                                <th class="py-2.5 px-3 text-center">Método</th>
                                <th class="py-2.5 px-3">Operador</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($todayLogs as $log)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2.5 px-4 font-mono font-bold text-emerald-700">
                                        {{ $log->logged_at->format('H:i') }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-[10px] {{ $log->isEntry() ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ strtoupper(substr($log->student->first_name ?? 'N', 0, 1) . substr($log->student->last_name ?? 'A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800 block text-xs">{{ $log->student->full_name ?? 'N/A' }}</span>
                                                <code class="text-[10px] text-slate-400 block">{{ $log->student->student_number ?? '' }}</code>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $log->class->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if($log->isEntry())
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <i class="fas fa-arrow-right-to-bracket"></i> Entrada
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                                <i class="fas fa-arrow-right-from-bracket"></i> Saída
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if($log->method === 'qr')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                <i class="fas fa-qrcode"></i> QR
                                            </span>
                                        @elseif($log->method === 'barcode' || $log->method === 'usb')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fas fa-barcode"></i> USB
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                <i class="fas fa-keyboard"></i> Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-500 font-medium">
                                        {{ $log->loggedBy->name ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <i class="fas fa-history text-3xl text-slate-300 mb-2"></i>
                                        <p class="text-xs font-semibold text-slate-600">Nenhum registo de passagem nesta data.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($todayLogs->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                        {{ $todayLogs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Overlay Feedback QR -->
<div class="scan-success-overlay" id="scanOverlay">
    <div class="text-5xl text-emerald-400 mb-3"><i class="fas fa-check-circle"></i></div>
    <h3 class="text-lg font-black text-white mb-1">QR Code Lido com Sucesso!</h3>
    <p class="text-xs text-slate-300" id="scanOverlayText">A processar registo...</p>
</div>

<!-- Trail Modal (Rastreio Completo - Native JS Modal) -->
<div id="trailModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100 relative" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fas fa-route text-emerald-600"></i> Rastreio Completo — <span id="trailStudentName">Estudante</span>
            </h3>
            <button type="button" onclick="closeTrailModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        <div class="p-2 max-h-96 overflow-y-auto" id="trailModalBody">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
                <p class="text-xs font-semibold">A carregar registos de rastreio...</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <div class="flex items-center gap-1">
                <button type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 trail-days-btn" data-days="7" onclick="reloadTrail(7)">7 dias</button>
                <button type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 trail-days-btn" data-days="15" onclick="reloadTrail(15)">15 dias</button>
                <button type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 trail-days-btn" data-days="30" onclick="reloadTrail(30)">30 dias</button>
            </div>
            <button type="button" onclick="closeTrailModal()" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">Fechar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const clockEl = document.getElementById('liveClock');
        if (clockEl) clockEl.textContent = time;
    }
    updateClock();
    setInterval(updateClock, 1000);

    const barcodeInput = document.getElementById('barcodeInput');
    if (barcodeInput) {
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value.trim().length > 0) {
                    document.getElementById('searchForm').submit();
                }
            }
        });
        barcodeInput.focus();
    }
});

let html5QrCode = null;
let cameraRunning = false;
let currentTrailStudentId = null;

function switchMode(mode) {
    const btnBarcode = document.getElementById('btnModeBarcode');
    const btnCamera = document.getElementById('btnModeCamera');

    if (mode === 'barcode') {
        btnBarcode.className = 'py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-2xs';
        btnCamera.className = 'py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800';
    } else {
        btnCamera.className = 'py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-2xs';
        btnBarcode.className = 'py-2 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800';
    }

    document.getElementById('barcodeMode').style.display = mode === 'barcode' ? 'block' : 'none';
    document.getElementById('cameraMode').style.display = mode === 'camera' ? 'block' : 'none';

    if (mode === 'barcode') {
        stopCamera();
        document.getElementById('barcodeInput')?.focus();
    }
}

function startCamera() {
    const qrReaderEl = document.getElementById('qr-reader');
    if (!qrReaderEl) return;

    document.getElementById('startCameraBtn').style.display = 'none';
    document.getElementById('stopCameraBtn').style.display = 'inline-flex';

    html5QrCode = new Html5Qrcode('qr-reader');
    cameraRunning = true;

    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
        (decodedText) => {
            stopCamera();
            showScanOverlay(decodedText);
            document.getElementById('qrDataInput').value = decodedText;
            document.getElementById('qrScanForm').submit();
        },
        (errorMessage) => {}
    ).catch(err => {
        console.error('Camera error:', err);
        alert('Não foi possível aceder à câmera.');
        document.getElementById('startCameraBtn').style.display = 'inline-flex';
        document.getElementById('stopCameraBtn').style.display = 'none';
    });
}

function stopCamera() {
    if (html5QrCode && cameraRunning) {
        html5QrCode.stop().then(() => {
            cameraRunning = false;
            document.getElementById('startCameraBtn').style.display = 'inline-flex';
            document.getElementById('stopCameraBtn').style.display = 'none';
        }).catch(err => console.warn(err));
    }
}

function showScanOverlay(data) {
    const overlay = document.getElementById('scanOverlay');
    const text = document.getElementById('scanOverlayText');
    if (overlay) {
        text.textContent = 'Código lido: ' + data.substring(0, 40) + '...';
        overlay.classList.add('show');
        setTimeout(() => overlay.classList.remove('show'), 2500);
    }
}

// ── Trail Modal Native Logic ──────────────────────────────────────
function openTrailModal() {
    const modal = document.getElementById('trailModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeTrailModal() {
    const modal = document.getElementById('trailModal');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

function loadTrail(studentId, days = 7) {
    currentTrailStudentId = studentId;
    openTrailModal();
    const body = document.getElementById('trailModalBody');
    body.innerHTML = '<div class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i><p class="text-xs font-semibold">A carregar registos de rastreio...</p></div>';

    fetch(`/gatekeeper/${studentId}/history?days=${days}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('trailStudentName').textContent = data.student.full_name + ' (' + data.student.student_number + ')';
            renderTrail(data.logs);
        })
        .catch(err => {
            body.innerHTML = '<div class="text-center py-4 text-rose-600"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-xs font-bold">Erro ao carregar histórico de acessos.</p></div>';
        });
}

function reloadTrail(days) {
    if (currentTrailStudentId) {
        document.querySelectorAll('.trail-days-btn').forEach(b => b.classList.remove('bg-emerald-700', 'text-white'));
        document.querySelector(`.trail-days-btn[data-days="${days}"]`)?.classList.add('bg-emerald-700', 'text-white');
        loadTrail(currentTrailStudentId, days);
    }
}

function renderTrail(logs) {
    const body = document.getElementById('trailModalBody');

    if (!logs || logs.length === 0) {
        body.innerHTML = '<div class="text-center py-8 text-slate-400"><i class="fas fa-route text-3xl text-slate-300 mb-2"></i><h5 class="text-xs font-bold text-slate-700 uppercase">Sem movimentações</h5><p class="text-[11px]">Nenhum registo de entrada ou saída encontrado para o período seleccionado.</p></div>';
        return;
    }

    let html = '<div class="trail-timeline space-y-3">';
    let lastDate = '';

    logs.forEach(log => {
        if (log.date !== lastDate) {
            html += `<div class="trail-date-separator"><i class="fas fa-calendar-day"></i> ${log.date}</div>`;
            lastDate = log.date;
        }

        const isEntry = log.action === 'entry';
        const dotClass = isEntry ? 'entry' : 'exit';
        const icon = isEntry ? 'fa-arrow-right' : 'fa-arrow-left';
        const color = isEntry ? 'text-emerald-700' : 'text-rose-700';
        const badgeClass = isEntry
            ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
            : 'bg-rose-100 text-rose-800 border border-rose-200';

        html += `
            <div class="trail-item">
                <div class="trail-dot ${dotClass}"><i class="fas ${icon}"></i></div>
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold ${badgeClass}">${log.label}</span>
                        <span class="text-[10px] text-slate-400 ml-1">${log.method}</span>
                        <span class="text-[10px] text-slate-500 font-semibold ml-2">• Turma: ${log.class}</span>
                        <span class="text-[10px] text-slate-400 ml-2">• Por: ${log.logged_by}</span>
                    </div>
                    <span class="font-mono font-bold ${color}">${log.time}</span>
                </div>
            </div>
        `;
    });

    html += '</div>';

    const entries = logs.filter(l => l.action === 'entry').length;
    const exits = logs.filter(l => l.action === 'exit').length;
    html += `
        <div class="mt-4 p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-center text-xs font-semibold text-slate-600">
            <strong class="text-emerald-700">${entries}</strong> entradas •
            <strong class="text-rose-700">${exits}</strong> saídas •
            <strong class="text-slate-800">${logs.length}</strong> movimentações totais
        </div>
    `;

    body.innerHTML = html;
}

// Close modals on overlay click or Escape key
document.getElementById('trailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTrailModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTrailModal();
});
</script>
@endpush
