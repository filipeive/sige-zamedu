@extends('layouts.app')

@section('title', 'Cartão de Estudante — ' . $student->full_name)
@section('page-title', 'Cartão de Identificação Escolar')
@section('page-title-icon', 'fas fa-id-card')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline">Início</a></li>
    <li class="breadcrumb-item"><a href="{{ route('gatekeeper.index') }}" class="no-underline">Portaria Digital</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Cartão — {{ $student->first_name }}</li>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap');

    /* ── Print ───────────────────────────────────────────── */
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; top: 0; left: 0; width: 100%; }
        .no-print { display: none !important; }
        .card-wrapper { break-inside: avoid; page-break-inside: avoid; }
    }

    /* ── Page Container ──────────────────────────────────── */
    .card-page-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem 0;
    }
    .card-display-row {
        display: flex;
        flex-wrap: wrap;
        gap: 2.5rem;
        justify-content: center;
        align-items: stretch;
        padding: 1rem 0;
    }

    /* ── ID Card Base ────────────────────────────────────── */
    .id-card {
        width: 360px;
        height: 560px;
        border-radius: 22px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.15),
            0 12px 24px -8px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255,255,255,0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .id-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow:
            0 35px 60px -15px rgba(0, 0, 0, 0.2),
            0 15px 30px -10px rgba(0, 0, 0, 0.12),
            inset 0 1px 0 rgba(255,255,255,0.2);
    }

    /* ═══════════════════════════════════════════════════════
       FRONT CARD
       ═══════════════════════════════════════════════════════ */
    .id-card-front {
        background: linear-gradient(175deg, #ffffff 0%, #f8fafc 100%);
    }

    /* Header with official Mozambique Emblem */
    .front-header {
        position: relative;
        background: linear-gradient(135deg, #0c4128 0%, #166534 40%, #15803d 75%, #22c55e 100%);
        padding: 1.25rem 1.25rem 2.25rem;
        text-align: center;
        color: white;
        overflow: hidden;
    }
    .front-header::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: -5%;
        width: 110%;
        height: 35px;
        background: #ffffff;
        border-radius: 50% 50% 0 0;
    }
    .front-header .header-ornament {
        position: absolute;
        top: -20px; right: -20px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .front-header .header-ornament-2 {
        position: absolute;
        bottom: 20px; left: -30px;
        width: 80px; height: 80px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .front-header .republic-text {
        font-size: 0.58rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #fef08a;
        margin-top: 2px;
    }
    .front-header .school-name {
        font-size: 0.88rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        margin-top: 1px;
    }
    .front-header .card-type-label {
        font-size: 0.52rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.9;
        margin-top: 2px;
        position: relative;
        z-index: 1;
    }

    /* Photo */
    .front-photo-section {
        text-align: center;
        margin-top: -1.25rem;
        position: relative;
        z-index: 2;
    }
    .photo-ring {
        display: inline-block;
        padding: 4px;
        background: linear-gradient(135deg, #166534, #22c55e, #166534);
        border-radius: 50%;
        box-shadow: 0 8px 25px rgba(22, 101, 52, 0.3);
    }
    .student-photo {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        display: block;
    }
    .student-photo-placeholder {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 900;
        border: 3px solid #fff;
    }

    /* Student Info */
    .front-student-info {
        text-align: center;
        padding: 0.5rem 1.5rem 0;
    }
    .student-full-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 0.35rem;
    }
    .student-number-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        color: #166534;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.3rem 0.9rem;
        border-radius: 8px;
        letter-spacing: 1.5px;
        border: 1px solid #bbf7d0;
    }
    .student-number-chip i {
        font-size: 0.7rem;
        opacity: 0.6;
    }

    /* Details Grid */
    .front-details {
        margin: 0.75rem 1.25rem 0;
        padding: 0.7rem 0.85rem;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem 1rem;
    }
    .detail-label {
        font-size: 0.55rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 1px;
    }
    .detail-value {
        font-size: 0.78rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Front Footer */
    .front-footer {
        margin-top: auto;
        background: linear-gradient(90deg, #0c4128, #166534);
        color: rgba(255,255,255,0.9);
        text-align: center;
        padding: 0.5rem 1rem;
        font-size: 0.58rem;
        font-weight: 600;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .front-footer .hologram {
        width: 20px; height: 20px;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3), rgba(255,255,255,0.1));
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* ═══════════════════════════════════════════════════════
       BACK CARD
       ═══════════════════════════════════════════════════════ */
    .id-card-back {
        background: linear-gradient(175deg, #fafffe 0%, #f0fdf4 100%);
    }

    .back-header {
        background: linear-gradient(135deg, #0c4128 0%, #166534 100%);
        padding: 1rem 1.25rem;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .back-header .back-title {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        position: relative;
        z-index: 1;
    }
    .back-header .back-ornament {
        position: absolute;
        top: -15px; left: -15px;
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    /* QR Section */
    .qr-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.25rem 1rem 0.5rem;
        flex: 1;
    }
    .qr-outer-frame {
        padding: 10px;
        background: white;
        border-radius: 16px;
        border: 2px solid #bbf7d0;
        box-shadow:
            0 8px 25px rgba(22, 101, 52, 0.1),
            inset 0 1px 0 rgba(255,255,255,0.8);
        position: relative;
    }
    .qr-outer-frame::before {
        content: '';
        position: absolute;
        top: -1px; left: -1px; right: -1px; bottom: -1px;
        border-radius: 17px;
        background: linear-gradient(135deg, #22c55e, #166534, #22c55e);
        z-index: -1;
        opacity: 0.3;
    }
    .qr-outer-frame img {
        width: 160px;
        height: 160px;
        display: block;
        border-radius: 8px;
    }
    .qr-no-code {
        width: 160px; height: 160px;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .qr-label {
        margin-top: 0.6rem;
        font-size: 0.58rem;
        font-weight: 700;
        color: #166534;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .qr-sublabel {
        font-size: 0.52rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* Emergency Info */
    .back-emergency {
        margin: 0.5rem 1rem;
        padding: 0.65rem 0.85rem;
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .back-emergency .emergency-title {
        font-size: 0.55rem;
        font-weight: 800;
        color: #dc2626;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .emergency-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.2rem 0;
        border-bottom: 1px dotted #f1f5f9;
    }
    .emergency-row:last-child { border-bottom: none; }
    .emergency-row .e-label {
        font-size: 0.62rem;
        font-weight: 600;
        color: #64748b;
    }
    .emergency-row .e-value {
        font-size: 0.65rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Disclaimer */
    .back-disclaimer {
        padding: 0.35rem 1.25rem;
        font-size: 0.52rem;
        color: #94a3b8;
        text-align: center;
        line-height: 1.35;
    }

    /* Back Footer */
    .back-footer {
        margin-top: auto;
        background: linear-gradient(90deg, #0c4128, #166534);
        color: rgba(255,255,255,0.85);
        text-align: center;
        padding: 0.45rem 1rem;
        font-size: 0.55rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="max-w-full mx-auto space-y-4">

    <!-- Student Summary Top Bar -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 rounded-xl border border-emerald-200 p-4 shadow-2xs no-print flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-700 shadow-2xs" alt="Foto">
            @else
                <div class="w-12 h-12 rounded-full bg-emerald-800 text-white font-black flex items-center justify-center text-base border-2 border-emerald-700 shadow-2xs">
                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                </div>
            @endif
            <div>
                <h3 class="text-base font-black text-slate-800 leading-tight">{{ $student->full_name }}</h3>
                <p class="text-xs text-slate-500 font-medium">
                    <code class="text-emerald-700 font-bold bg-emerald-100/70 px-1.5 py-0.5 rounded border border-emerald-200">{{ $student->student_number }}</code> •
                    {{ $student->currentEnrollment->class->name ?? 'Sem turma' }} •
                    Ano Lectivo {{ setting('academic_year', date('Y')) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('gatekeeper.index', ['search' => $student->student_number]) }}" class="no-underline px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs">
                <i class="fas fa-arrow-left"></i> Portaria
            </a>
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-print"></i> Imprimir Cartão
            </button>
            <a href="{{ route('students.show', $student->id) }}" class="no-underline px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs">
                <i class="fas fa-eye text-slate-500"></i> Perfil Aluno
            </a>
        </div>
    </div>

    <!-- Cards Printable Area -->
    <div class="print-area">
        <div class="card-display-row">

            <!-- ════ FRENTE DO CARTÃO ════ -->
            <div class="card-wrapper">
                <div class="text-center text-slate-500 text-[11px] mb-2 font-black uppercase tracking-wider no-print flex items-center justify-center gap-1">
                    <i class="fas fa-id-card text-emerald-600"></i> Frente do Cartão
                </div>
                <div class="id-card id-card-front">
                    <!-- Frente Header com Emblema Oficial de Moçambique -->
                    <div class="front-header">
                        <div class="header-ornament"></div>
                        <div class="header-ornament-2"></div>
                        <div style="text-align: center; margin-bottom: 2px;">
                            <img src="{{ asset('images/emblema-mocambique.png') }}" style="height: 32px; width: auto; display: inline-block;" alt="Emblema de Moçambique">
                        </div>
                        <div class="republic-text">REPÚBLICA DE MOÇAMBIQUE</div>
                        <div class="school-name">{{ setting('school_name', config('app.name', 'ZamEdu SIGE')) }}</div>
                        <div class="card-type-label">Cartão de Identificação Escolar</div>
                    </div>

                    <div class="front-photo-section">
                        <div class="photo-ring">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" class="student-photo" alt="Foto">
                            @else
                                <div class="student-photo-placeholder">
                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="front-student-info">
                        <div class="student-full-name">{{ $student->full_name }}</div>
                        <div class="student-number-chip">
                            <i class="fas fa-fingerprint"></i>
                            {{ $student->student_number }}
                        </div>
                    </div>

                    <div class="front-details">
                        <div class="detail-item">
                            <div class="detail-label">Turma / Nível</div>
                            <div class="detail-value">{{ $student->currentEnrollment->class->name ?? 'Sem Turma' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Ano Lectivo</div>
                            <div class="detail-value">{{ setting('academic_year', date('Y')) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Data de Nasc.</div>
                            <div class="detail-value">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : 'N/D' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Gênero</div>
                            <div class="detail-value">{{ $student->gender === 'M' ? 'Masculino' : ($student->gender === 'F' ? 'Feminino' : 'N/D') }}</div>
                        </div>
                    </div>

                    <div class="front-footer">
                        <div class="hologram"></div>
                        <span>Validade: 31/12/{{ setting('academic_year', date('Y')) }}</span>
                        <span>{{ setting('school_short_name', 'ZamEdu') }}</span>
                    </div>
                </div>
            </div>

            <!-- ════ VERSO DO CARTÃO ════ -->
            <div class="card-wrapper">
                <div class="text-center text-slate-500 text-[11px] mb-2 font-black uppercase tracking-wider no-print flex items-center justify-center gap-1">
                    <i class="fas fa-qrcode text-emerald-600"></i> Verso do Cartão
                </div>
                <div class="id-card id-card-back">
                    <div class="back-header">
                        <div class="back-ornament"></div>
                        <div class="back-title">
                            <i class="fas fa-shield-halved me-1"></i> Verificação de Segurança
                        </div>
                    </div>

                    <!-- QR Section -->
                    <div class="qr-section">
                        <div class="qr-outer-frame">
                            @if($qrBase64)
                                <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
                            @else
                                <div class="qr-no-code">
                                    <div class="text-center">
                                        <i class="fas fa-qrcode fa-2x mb-2 d-block text-slate-300"></i>
                                        QR Indisponível
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="qr-label">Acesso Portaria</div>
                        <div class="qr-sublabel">Aponte a câmera ou use o leitor de código</div>
                    </div>

                    <!-- Emergency Info -->
                    <div class="back-emergency">
                        <div class="emergency-title">
                            <i class="fas fa-phone-volume"></i> Contactos de Emergência
                        </div>
                        <div class="emergency-row">
                            <span class="e-label">Encarregado</span>
                            <span class="e-value">{{ $student->parent->full_name ?? $student->parent->name ?? 'N/D' }}</span>
                        </div>
                        <div class="emergency-row">
                            <span class="e-label">Tel. Emergência</span>
                            <span class="e-value">{{ $student->parent->phone ?? $student->emergency_phone ?? 'N/D' }}</span>
                        </div>
                        <div class="emergency-row">
                            <span class="e-label">Tel. Escola</span>
                            <span class="e-value">{{ setting('school_phone', '+258 84 000 0000') }}</span>
                        </div>
                    </div>

                    <div class="back-disclaimer">
                        Este cartão é pessoal e intransmissível. Em caso de perda, favor entregar na Secretaria da Escola.
                    </div>

                    <div class="back-footer">
                        Assinatura Autorizada da Direção
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
