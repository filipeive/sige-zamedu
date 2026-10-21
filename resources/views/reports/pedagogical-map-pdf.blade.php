<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Mapa de Aproveitamento Pedagógico MINEDH</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7.5pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            height: 55px;
            width: auto;
            margin-bottom: 3px;
        }
        .header h1 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 7.5pt;
            color: #475569;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }
        .header h3 {
            font-size: 9.5pt;
            color: #047857;
            font-weight: bold;
            margin: 3px 0 2px 0;
            text-transform: uppercase;
        }
        .badge-title {
            display: inline-block;
            padding: 3px 10px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            font-size: 8pt;
            margin-top: 3px;
            text-transform: uppercase;
        }
        .table-minedh {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 12px;
        }
        .table-minedh th, .table-minedh td {
            border: 1px solid #94a3b8;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }
        .table-minedh th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 6pt;
            text-transform: uppercase;
        }
        .bg-subhead {
            background-color: #e2e8f0;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left !important;
            padding-left: 6px !important;
        }
        .bg-ciclo {
            background-color: #cbd5e1;
            font-weight: bold;
        }
        .bg-hm { background-color: #f1f5f9; }
        .bg-m { background-color: #faf5ff; color: #7e22ce; }
        .bg-pos { background-color: #ecfdf5; color: #047857; font-weight: bold; }
        .bg-rate { background-color: #d1fae5; color: #047857; font-weight: bold; }
        .bg-desist { color: #b91c1c; font-weight: bold; }

        .signatures {
            margin-top: 20px;
            width: 100%;
        }
        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            border: none;
            padding: 0 15px;
        }
        .sig-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        .sig-line {
            border-top: 1px solid #000000;
            width: 75%;
            margin: 0 auto 3px auto;
        }
        .sig-name {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .sig-sub {
            font-size: 6.5pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>

    @php
        $isPrivateInstitution = ($selectedSchool && ($selectedSchool->isPrivate() || $selectedSchool->type === 'private' || str_contains(strtolower($selectedSchool->name), 'visionários')));
    @endphp

    @if($isPrivateInstitution)
    <!-- CABEÇALHO ESCOLA PRIVADA -->
    <div class="header">
        @if(file_exists(public_path('images/logo_visionarios.png')))
            <img src="{{ public_path('images/logo_visionarios.png') }}" alt="{{ $selectedSchool->name }}" style="height: 50px;">
        @endif
        <h1 style="font-size: 13pt; color: #047857; margin-top: 4px;">{{ strtoupper($selectedSchool?->name ?? 'ESCOLA DOS VISIONÁRIOS') }}</h1>
        <h2>UNIDADE: {{ strtoupper($selectedSchool?->city ?? $selectedSchool?->district?->name ?? 'QUELIMANE') }} &bull; ENSINO PRIVADO</h2>
        <div class="badge-title" style="margin-top: 6px;">
            MAPA DE APROVEITAMENTO PEDAGÓGICO — {{ $term == 4 ? 'FINAL DO ANO' : $term . 'º TRIMESTRE' }} DE {{ $year }}
        </div>
    </div>
    @else
    <!-- MINEDH OFFICIAL HEADER -->
    <div class="header">
        @if(file_exists(public_path('image.png')))
            <img src="{{ public_path('image.png') }}" alt="Emblema da República de Moçambique">
        @endif
        <h1>REPÚBLICA DE MOÇAMBIQUE</h1>
        <h2>GOVERNO DA PROVÍNCIA DE {{ strtoupper($selectedSchool?->province?->name ?? 'PROVÍNCIA') }}</h2>
        <h2>SERVIÇO DISTRITAL DE EDUCAÇÃO, JUVENTUDE E TECNOLOGIA (SDEJT {{ strtoupper($selectedSchool?->district?->name ?? 'DISTRITO') }})</h2>
        @if($selectedSchool && $selectedSchool->zip)
            <h2>ZONA DE INFLUÊNCIA PEDAGÓGICA: {{ strtoupper($selectedSchool->zip->name) }} {{ $selectedSchool->zip->headquarters ? '('.strtoupper($selectedSchool->zip->headquarters).')' : '' }}</h2>
        @endif
        <h3>{{ strtoupper($selectedSchool?->name ?? 'ESCOLA NÃO SELECIONADA') }}</h3>
        <div style="font-size: 7pt; color: #475569; margin-top: 2px;">
            SUB-SISTEMA: <strong>{{ strtoupper($schoolTeachingType ?? 'ENSINO GERAL') }}</strong> &bull; NATUREZA: <strong>{{ strtoupper($selectedSchool?->type ?? 'Pública') }}</strong>
        </div>

        <div class="badge-title">
            MAPA DE APROVEITAMENTO PEDAGÓGICO — {{ $term == 4 ? 'FINAL DO ANO' : $term . 'º TRIMESTRE' }} DE {{ $year }}
        </div>
    </div>
    @endif

    <!-- QUADRO 1: RENDIMENTO ESCOLAR COMPLETO MINEDH -->
    <table class="table-minedh">
        <thead>
            <tr>
                <th rowspan="2" style="width: 18%; text-align: left; padding-left: 6px;">Classe</th>
                <th colspan="2">N/ Entradas (Matrícula)</th>
                <th colspan="2">Desistentes</th>
                <th colspan="2">Transf.</th>
                <th colspan="2">Anulou Matrícula</th>
                <th colspan="2">PPF / PDF</th>
                <th colspan="2">Avaliados</th>
                <th colspan="2">Transitados / Aprovados</th>
                <th colspan="2" style="background-color: #047857;">% TRANS/ APROVADOS</th>
                <th colspan="2" style="background-color: #991b1b;">% Desistentes</th>
            </tr>
            <tr style="font-size: 5.5pt; background-color: #1e293b;">
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th style="background-color: #065f46;">HM %</th><th style="background-color: #065f46;">M %</th>
                <th style="background-color: #7f1d1d;">HM %</th><th style="background-color: #7f1d1d;">M %</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($preschoolRows) && count($preschoolRows) > 0)
                <tr>
                    <td colspan="19" class="bg-subhead">PRÉ-ESCOLAR / PRÉ-INFANTIL</td>
                </tr>
                @foreach($preschoolRows as $cm)
                    <tr>
                        <td style="text-align: left; padding-left: 6px; font-weight: bold;">{{ $cm['grade_level_name'] }}</td>
                        <td class="bg-hm">{{ number_format($cm['total_hm']) }}</td>
                        <td class="bg-m">{{ number_format($cm['total_m']) }}</td>
                        <td>{{ number_format($cm['desist_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['desist_m']) }}</td>
                        <td>{{ number_format($cm['transf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['transf_m']) }}</td>
                        <td>{{ number_format($cm['anulou_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['anulou_m']) }}</td>
                        <td>{{ number_format($cm['ppf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['ppf_m']) }}</td>
                        <td style="font-weight: bold;">{{ number_format($cm['eval_hm']) }}</td>
                        <td style="font-weight: bold; color: #7e22ce;">{{ number_format($cm['eval_m']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_hm']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_m']) }}</td>
                        <td class="bg-rate">{{ $cm['rate_hm'] }}%</td>
                        <td class="bg-rate">{{ $cm['rate_m'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_hm'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_m'] }}%</td>
                    </tr>
                @endforeach
            @endif

            @if(!empty($ciclo1Rows) && count($ciclo1Rows) > 0)
                <tr>
                    <td colspan="19" class="bg-subhead">{{ $ciclo1Data['title'] }}</td>
                </tr>
                @foreach($ciclo1Rows as $cm)
                    <tr>
                        <td style="text-align: left; padding-left: 6px; font-weight: bold;">{{ $cm['grade_level_name'] }}</td>
                        <td class="bg-hm">{{ number_format($cm['total_hm']) }}</td>
                        <td class="bg-m">{{ number_format($cm['total_m']) }}</td>
                        <td>{{ number_format($cm['desist_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['desist_m']) }}</td>
                        <td>{{ number_format($cm['transf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['transf_m']) }}</td>
                        <td>{{ number_format($cm['anulou_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['anulou_m']) }}</td>
                        <td>{{ number_format($cm['ppf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['ppf_m']) }}</td>
                        <td style="font-weight: bold;">{{ number_format($cm['eval_hm']) }}</td>
                        <td style="font-weight: bold; color: #7e22ce;">{{ number_format($cm['eval_m']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_hm']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_m']) }}</td>
                        <td class="bg-rate">{{ $cm['rate_hm'] }}%</td>
                        <td class="bg-rate">{{ $cm['rate_m'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_hm'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_m'] }}%</td>
                    </tr>
                @endforeach
                <tr class="bg-ciclo">
                    <td style="text-align: left; padding-left: 6px; font-weight: bold;">TOTAL 1º CICLO</td>
                    <td>{{ number_format($ciclo1Data['total_hm']) }}</td><td>{{ number_format($ciclo1Data['total_m']) }}</td>
                    <td>{{ number_format($ciclo1Data['desist_hm']) }}</td><td>{{ number_format($ciclo1Data['desist_m']) }}</td>
                    <td>{{ number_format($ciclo1Data['transf_hm']) }}</td><td>{{ number_format($ciclo1Data['transf_m']) }}</td>
                    <td>{{ number_format($ciclo1Data['anulou_hm']) }}</td><td>{{ number_format($ciclo1Data['anulou_m']) }}</td>
                    <td>{{ number_format($ciclo1Data['ppf_hm']) }}</td><td>{{ number_format($ciclo1Data['ppf_m']) }}</td>
                    <td>{{ number_format($ciclo1Data['eval_hm']) }}</td><td>{{ number_format($ciclo1Data['eval_m']) }}</td>
                    <td style="color: #047857; font-weight: bold;">{{ number_format($ciclo1Data['passed_hm']) }}</td><td style="color: #047857; font-weight: bold;">{{ number_format($ciclo1Data['passed_m']) }}</td>
                    <td style="color: #047857; font-weight: bold;">{{ $ciclo1Data['rate_hm'] }}%</td><td style="color: #7e22ce; font-weight: bold;">{{ $ciclo1Data['rate_m'] }}%</td>
                    <td style="color: #b91c1c; font-weight: bold;">{{ $ciclo1Data['desist_rate_hm'] }}%</td><td style="color: #b91c1c; font-weight: bold;">{{ $ciclo1Data['desist_rate_m'] }}%</td>
                </tr>
            @endif

            @if(!empty($ciclo2Rows) && count($ciclo2Rows) > 0)
                <tr>
                    <td colspan="19" class="bg-subhead">{{ $ciclo2Data['title'] }}</td>
                </tr>
                @foreach($ciclo2Rows as $cm)
                    <tr>
                        <td style="text-align: left; padding-left: 6px; font-weight: bold;">{{ $cm['grade_level_name'] }}</td>
                        <td class="bg-hm">{{ number_format($cm['total_hm']) }}</td>
                        <td class="bg-m">{{ number_format($cm['total_m']) }}</td>
                        <td>{{ number_format($cm['desist_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['desist_m']) }}</td>
                        <td>{{ number_format($cm['transf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['transf_m']) }}</td>
                        <td>{{ number_format($cm['anulou_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['anulou_m']) }}</td>
                        <td>{{ number_format($cm['ppf_hm']) }}</td>
                        <td style="color: #7e22ce;">{{ number_format($cm['ppf_m']) }}</td>
                        <td style="font-weight: bold;">{{ number_format($cm['eval_hm']) }}</td>
                        <td style="font-weight: bold; color: #7e22ce;">{{ number_format($cm['eval_m']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_hm']) }}</td>
                        <td class="bg-pos">{{ number_format($cm['passed_m']) }}</td>
                        <td class="bg-rate">{{ $cm['rate_hm'] }}%</td>
                        <td class="bg-rate">{{ $cm['rate_m'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_hm'] }}%</td>
                        <td class="bg-desist">{{ $cm['desist_rate_m'] }}%</td>
                    </tr>
                @endforeach
                <tr class="bg-ciclo">
                    <td style="text-align: left; padding-left: 6px; font-weight: bold;">TOTAL 2º CICLO</td>
                    <td>{{ number_format($ciclo2Data['total_hm']) }}</td><td>{{ number_format($ciclo2Data['total_m']) }}</td>
                    <td>{{ number_format($ciclo2Data['desist_hm']) }}</td><td>{{ number_format($ciclo2Data['desist_m']) }}</td>
                    <td>{{ number_format($ciclo2Data['transf_hm']) }}</td><td>{{ number_format($ciclo2Data['transf_m']) }}</td>
                    <td>{{ number_format($ciclo2Data['anulou_hm']) }}</td><td>{{ number_format($ciclo2Data['anulou_m']) }}</td>
                    <td>{{ number_format($ciclo2Data['ppf_hm']) }}</td><td>{{ number_format($ciclo2Data['ppf_m']) }}</td>
                    <td>{{ number_format($ciclo2Data['eval_hm']) }}</td><td>{{ number_format($ciclo2Data['eval_m']) }}</td>
                    <td style="color: #047857; font-weight: bold;">{{ number_format($ciclo2Data['passed_hm']) }}</td><td style="color: #047857; font-weight: bold;">{{ number_format($ciclo2Data['passed_m']) }}</td>
                    <td style="color: #047857; font-weight: bold;">{{ $ciclo2Data['rate_hm'] }}%</td><td style="color: #7e22ce; font-weight: bold;">{{ $ciclo2Data['rate_m'] }}%</td>
                    <td style="color: #b91c1c; font-weight: bold;">{{ $ciclo2Data['desist_rate_hm'] }}%</td><td style="color: #b91c1c; font-weight: bold;">{{ $ciclo2Data['desist_rate_m'] }}%</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background-color: #cbd5e1; font-weight: bold; font-size: 7.5pt;">
                <td style="text-align: left; padding-left: 6px;">TOTAL GERAL DA ESCOLA</td>
                <td>{{ number_format($totalGeralData['total_hm']) }}</td><td>{{ number_format($totalGeralData['total_m']) }}</td>
                <td>{{ number_format($totalGeralData['desist_hm']) }}</td><td>{{ number_format($totalGeralData['desist_m']) }}</td>
                <td>{{ number_format($totalGeralData['transf_hm']) }}</td><td>{{ number_format($totalGeralData['transf_m']) }}</td>
                <td>{{ number_format($totalGeralData['anulou_hm']) }}</td><td>{{ number_format($totalGeralData['anulou_m']) }}</td>
                <td>{{ number_format($totalGeralData['ppf_hm']) }}</td><td>{{ number_format($totalGeralData['ppf_m']) }}</td>
                <td>{{ number_format($totalGeralData['eval_hm']) }}</td><td>{{ number_format($totalGeralData['eval_m']) }}</td>
                <td style="color: #047857;">{{ number_format($totalGeralData['passed_hm']) }}</td><td style="color: #047857;">{{ number_format($totalGeralData['passed_m']) }}</td>
                <td style="color: #047857; font-weight: bold;">{{ $totalGeralData['rate_hm'] }}%</td><td style="color: #7e22ce; font-weight: bold;">{{ $totalGeralData['rate_m'] }}%</td>
                <td style="color: #b91c1c; font-weight: bold;">{{ $totalGeralData['desist_rate_hm'] }}%</td><td style="color: #b91c1c; font-weight: bold;">{{ $totalGeralData['desist_rate_m'] }}%</td>
            </tr>
        </tfoot>
    </table>

    <!-- OFFICIAL SIGNATURES BLOCK -->
    <div class="signatures">
        <table>
            <tr>
                <td>
                    <div class="sig-title">O Diretor Adjunto Pedagógico (DAP)</div>
                    <div class="sig-line"></div>
                    <div class="sig-sub">(Assinatura e Carimbo Oficial)</div>
                </td>
                <td>
                    <div class="sig-title">O Diretor da Escola (DAE)</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $selectedSchool?->director_name ?? 'PROF. MARIA NHABINDE' }}</div>
                    <div class="sig-sub">(Assinatura e Carimbo Oficial)</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
