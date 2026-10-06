@extends('layouts.portal')

@section('title', 'Quadro Oficial de Vagas Escolares — Ano Lectivo 2027 — MINEDH | ZAMEDU')

@section('content')
<div style="max-width: 1440px; margin: 0 auto; padding: 36px 32px;">

    <!-- Banner Institucional MINEDH -->
    <div style="background: linear-gradient(135deg, #02381B 0%, #007A3D 100%); border-radius: 20px; padding: 32px 36px; color: white; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0, 122, 61, 0.15); border-bottom: 4px solid #FCD116; position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; position: relative; z-index: 2;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="{{ asset('images/emblem.png') }}" alt="Emblema República de Moçambique" style="height: 64px; width: auto; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 6px; background: rgba(252, 209, 22, 0.2); border: 1px solid #FCD116; color: #FDE047; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <i class="fas fa-landmark"></i> REPÚBLICA DE MOÇAMBIQUE · MINEDH
                    </div>
                    <h1 style="font-size: 26px; font-weight: 900; margin: 0 0 6px 0; color: white; line-height: 1.2;">
                        Quadro Oficial de Vagas Escolares — Ano Lectivo {{ $academicYear ?? '2027' }}
                    </h1>
                    <p style="font-size: 14px; margin: 0; color: #D1FAE5; opacity: 0.95; max-width: 760px;">
                        Consulte a disponibilidade em tempo real de vagas escolares por estabelecimento de ensino, província e distrito no Sistema Nacional de Educação.
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 10px; flex-shrink: 0;">
                <a href="{{ route('public.allocations') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; background: rgba(255,255,255,0.12); color: white; border: 1px solid rgba(255,255,255,0.3); font-weight: 700; font-size: 13px; text-decoration: none; transition: background 0.2s;">
                    <i class="fas fa-search-location"></i> Consultar Afetações 6ª &rarr; 7ª
                </a>
                <a href="{{ route('public.pre-enrollment') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; background: #FCD116; color: #0F172A; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: background 0.2s;">
                    <i class="fas fa-user-plus"></i> Pré-Inscrição 2027
                </a>
            </div>
        </div>
    </div>

    <!-- Painel de Filtros de Vagas -->
    <div style="background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 15px rgba(0,0,0,0.04); padding: 24px; margin-bottom: 28px;">
        <form method="GET" action="{{ route('public.vacancies') }}">
            <div style="display: grid; grid-template-columns: 1.2fr 1.2fr 1.2fr 1fr auto; gap: 16px; align-items: flex-end;">
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fas fa-map-marker-alt text-emerald-700" style="margin-right: 4px;"></i> Província:
                    </label>
                    <select name="province_id" style="width: 100%; height: 44px; padding: 8px 14px; border-radius: 10px; border: 1.5px solid #CBD5E1; font-size: 14px; outline: none; background: white;">
                        <option value="">Todas as Províncias</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p->id }}" {{ $provinceId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fas fa-landmark text-emerald-700" style="margin-right: 4px;"></i> Distrito:
                    </label>
                    <select name="district_id" style="width: 100%; height: 44px; padding: 8px 14px; border-radius: 10px; border: 1.5px solid #CBD5E1; font-size: 14px; outline: none; background: white;">
                        <option value="">Todos os Distritos</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ $districtId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fas fa-graduation-cap text-emerald-700" style="margin-right: 4px;"></i> Nível de Ensino:
                    </label>
                    <select name="level" style="width: 100%; height: 44px; padding: 8px 14px; border-radius: 10px; border: 1.5px solid #CBD5E1; font-size: 14px; outline: none; background: white;">
                        <option value="all" {{ $level === 'all' ? 'selected' : '' }}>Todos os Níveis</option>
                        <option value="primary" {{ $level === 'primary' ? 'selected' : '' }}>Ensino Primário (1ª à 6ª)</option>
                        <option value="secondary" {{ $level === 'secondary' ? 'selected' : '' }}>Ensino Secundário (7ª à 12ª)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fas fa-building text-emerald-700" style="margin-right: 4px;"></i> Regime:
                    </label>
                    <select name="type" style="width: 100%; height: 44px; padding: 8px 14px; border-radius: 10px; border: 1.5px solid #CBD5E1; font-size: 14px; outline: none; background: white;">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="public" {{ $type === 'public' ? 'selected' : '' }}>Pública</option>
                        <option value="private" {{ $type === 'private' ? 'selected' : '' }}>Privada</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="height: 44px; padding: 0 22px; border-radius: 10px; background: #007A3D; color: white; font-weight: 700; font-size: 14px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; box-shadow: 0 2px 8px rgba(0, 122, 61, 0.25);">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    @if(!empty($provinceId) || !empty($districtId) || $level !== 'all' || $type !== 'all')
                        <a href="{{ route('public.vacancies') }}" style="height: 44px; padding: 0 16px; border-radius: 10px; background: #F1F5F9; color: #475569; font-weight: 700; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #CBD5E1;" title="Limpar Filtros">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Estabelecimentos e Vagas -->
    @if($schools->count() > 0)
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <div style="font-size: 14px; font-weight: 700; color: #1E293B;">
                <i class="fas fa-school text-emerald-700" style="margin-right: 6px;"></i>
                Estabelecimentos Listados: <span style="background: #E2E8F0; padding: 2px 8px; border-radius: 6px;">{{ $schools->total() }}</span>
            </div>
            <span style="font-size: 12px; font-weight: 700; color: #047857; background: #DCFCE7; border: 1px solid #BBF7D0; padding: 4px 12px; border-radius: 20px;">
                <i class="fas fa-check-circle" style="margin-right: 4px;"></i> Ano Lectivo {{ $academicYear ?? '2027' }} — Candidaturas Abertas
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 24px; margin-bottom: 36px;">
            @foreach($schools as $school)
                <div style="background: white; border-radius: 16px; border: 1.5px solid #E2E8F0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                    <!-- Top Bar do Card -->
                    <div style="padding: 14px 18px; background: #F8FAFC; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 800; text-transform: uppercase; padding: 3px 9px; border-radius: 6px; background: {{ $school->type === 'public' ? '#E0F2FE' : '#F3E8FF' }}; color: {{ $school->type === 'public' ? '#0369A1' : '#7E22CE' }};">
                            <i class="fas {{ $school->type === 'public' ? 'fa-landmark' : 'fa-building' }}"></i>
                            {{ $school->type_label }}
                        </span>
                        <span style="font-family: monospace; font-size: 11px; color: #64748B;">
                            Cód: {{ $school->code ?? ('ESC-' . str_pad($school->id, 3, '0', STR_PAD_LEFT)) }}
                        </span>
                    </div>

                    <!-- Conteúdo do Card -->
                    <div style="padding: 20px; flex-grow: 1;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.3;">
                            {{ $school->name }}
                        </h3>
                        <div style="font-size: 12px; color: #64748B; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-map-pin text-emerald-700"></i>
                            <span>{{ $school->district->name ?? 'Distrito' }}, {{ $school->province->name ?? 'Província' }}</span>
                            @if($school->zip)
                                <span>&bull; ZIP: {{ $school->zip->name }}</span>
                            @endif
                        </div>

                        <!-- Indicadores de Vagas (HM/Total) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 16px; text-align: center;">
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 10px 6px;">
                                <span style="font-size: 10.5px; font-weight: 700; color: #64748B; text-transform: uppercase; display: block;">Capacidade</span>
                                <strong style="font-size: 17px; color: #1E293B; display: block; margin-top: 2px;">{{ $school->total_capacity }}</strong>
                            </div>

                            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 10px 6px;">
                                <span style="font-size: 10.5px; font-weight: 700; color: #B45309; text-transform: uppercase; display: block;">Afetadas</span>
                                <strong style="font-size: 17px; color: #B45309; display: block; margin-top: 2px;">{{ $school->allocated_vacancies }}</strong>
                            </div>

                            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 10px 6px;">
                                <span style="font-size: 10.5px; font-weight: 800; color: #047857; text-transform: uppercase; display: block;">Disponíveis</span>
                                <strong style="font-size: 17px; color: #047857; display: block; margin-top: 2px;">{{ $school->available_vacancies }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Ação -->
                    <div style="padding: 14px 20px; background: white; border-top: 1px solid #F1F5F9;">
                        <a href="{{ route('public.pre-enrollment', ['school_id' => $school->id]) }}" style="width: 100%; height: 42px; display: flex; align-items: center; justify-content: center; gap: 8px; background: #007A3D; color: white; border-radius: 10px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 10px rgba(0, 122, 61, 0.15); transition: background 0.2s;">
                            <i class="fas fa-user-plus"></i> Pré-Inscrever nesta Escola
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display: flex; justify-content: center; margin-top: 20px;">
            {{ $schools->links() }}
        </div>
    @else
        <div style="background: white; border-radius: 20px; border: 1px solid #E2E8F0; padding: 60px 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px auto; color: #94A3B8; font-size: 28px;">
                <i class="fas fa-school-circle-xmark"></i>
            </div>
            <h3 style="font-size: 19px; font-weight: 800; color: #1E293B; margin-bottom: 8px;">Nenhuma escola localizada com os filtros aplicados</h3>
            <p style="font-size: 13.5px; color: #64748B; max-width: 520px; margin: 0 auto 24px auto;">
                Tente selecionar outra província, distrito ou alterar o nível de ensino para verificar a disponibilidade de vagas.
            </p>
            <a href="{{ route('public.vacancies') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 10px; background: #F1F5F9; color: #334155; font-weight: 700; font-size: 13px; text-decoration: none; border: 1px solid #CBD5E1;">
                <i class="fas fa-rotate-left"></i> Limpar Filtros
            </a>
        </div>
    @endif

</div>
@endsection
