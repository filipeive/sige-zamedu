<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pauta Oficial - {{ $class->name }} (Ano Lectivo {{ $year }})</title>
    @php
        $subjectCount = count($subjects);
        $colsPerSubject = $type === 'trimestral' ? 6 : ($type === 'anual' ? 4 : 3);
        $totalCols = ($subjectCount * $colsPerSubject) + 4;
        $paperSize = $paper ?? 'a4';

        if ($paperSize === 'a3') {
            $bodyFont = $totalCols > 40 ? 7 : ($totalCols > 30 ? 7.5 : 8);
            $cellPad = $totalCols > 40 ? '2px 1px' : '3px 2px';
            $cellFont = $totalCols > 40 ? 6.5 : ($totalCols > 30 ? 7 : 7.5);
            $headerFont = $totalCols > 40 ? 6 : ($totalCols > 30 ? 6.5 : 7);
            $nameWidth = $totalCols > 40 ? 130 : 160;
        } else {
            $bodyFont = $totalCols > 30 ? 6 : ($totalCols > 20 ? 7 : 8);
            $cellPad = $totalCols > 30 ? '1px 1px' : ($totalCols > 20 ? '2px 1px' : '3px 2px');
            $cellFont = $totalCols > 30 ? 5.5 : ($totalCols > 20 ? 6.5 : 7.5);
            $headerFont = $totalCols > 30 ? 5 : ($totalCols > 20 ? 6 : 7);
            $nameWidth = $totalCols > 30 ? 100 : ($totalCols > 20 ? 130 : 160);
        }
    @endphp
    <style>
        @page {
            margin: 8mm 6mm 8mm 6mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ $bodyFont }}px;
            margin: 0;
            padding: 0;
            color: #0f172a;
        }
        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
        }
        .header h2 { margin: 0; font-size: 11px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .header h3 { margin: 1px 0; font-size: 9px; font-weight: normal; color: #334155; text-transform: uppercase; }
        .header p  { margin: 2px 0; font-size: 8.5px; font-weight: bold; color: #047857; text-transform: uppercase; }

        .meta-info { width: 100%; margin-bottom: 6px; font-size: {{ $bodyFont }}px; border-collapse: collapse; }
        .meta-info td { padding: 3px 5px; border: 1px solid #cbd5e1; background-color: #f8fafc; }

        table.pauta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }
        table.pauta th, table.pauta td {
            border: 1px solid #334155;
            padding: {{ $cellPad }};
            text-align: center;
            font-size: {{ $cellFont }}px;
            word-wrap: break-word;
            overflow: hidden;
        }
        table.pauta th {
            background-color: #0f172a;
            color: #ffffff;
            text-transform: uppercase;
            font-size: {{ $headerFont }}px;
            font-weight: bold;
        }
        table.pauta th.sub-header {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: {{ $headerFont }}px;
        }
        table.pauta td.student-name {
            text-align: left;
            padding-left: 3px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        table.pauta td.avg-col {
            font-weight: bold;
            background-color: #f0fdf4;
            color: #166534;
        }

        .text-left { text-align: left !important; padding-left: 3px !important; }
        .approved { color: #166534; font-weight: bold; background-color: #dcfce7; padding: 1px 3px; border-radius: 2px; font-size: {{ $cellFont - 0.5 }}px; }
        .failed   { color: #991b1b; font-weight: bold; background-color: #fee2e2; padding: 1px 3px; border-radius: 2px; font-size: {{ $cellFont - 0.5 }}px; }
        .pending  { color: #854d0e; font-weight: bold; background-color: #fef9c3; padding: 1px 3px; border-radius: 2px; font-size: {{ $cellFont - 0.5 }}px; }

        .footer-signatures { width: 100%; margin-top: 15px; text-align: center; }
        .footer-signatures td { width: 33%; padding-top: 20px; font-size: 8px; }
        .signature-line { border-top: 1px solid #0f172a; width: 80%; margin: 0 auto; padding-top: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <!-- Header Oficial MINEDH -->
    <div class="header">
        <div style="text-align: center; margin-bottom: 4px;">
            <img src="{{ public_path('images/emblema-mocambique.png') }}" style="height: 40px; width: auto;" alt="Emblema de Moçambique">
        </div>
        <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
        <h3>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h3>
        <h3>{{ strtoupper(\App\Models\Setting::get('school_name', 'ESCOLA SECUNDÁRIA ZAMEDU')) }}</h3>
        <p>PAUTA OFICIAL DE FREQUÊNCIA E AVALIAÇÃO — ANO LECTIVO {{ $year }}</p>
    </div>

    <table class="meta-info">
        <tr>
            <td><strong>Nível:</strong> {{ $class->education_level_name }}</td>
            <td><strong>Turma:</strong> {{ $class->name }} ({{ $class->grade_level_name }})</td>
            <td><strong>Tipo:</strong> {{ strtoupper($type) }} {{ $type === 'trimestral' ? "({$term}º Trimestre)" : '' }}</td>
            <td><strong>Emissão:</strong> {{ date('d/m/Y') }}</td>
            <td><strong>Formato:</strong> {{ strtoupper($paperSize) }}</td>
        </tr>
    </table>

    <table class="pauta">
        <thead>
            <tr>
                <th rowspan="2" style="width: 18px;">N.º</th>
                <th rowspan="2" class="text-left" style="width: {{ $nameWidth }}px;">Nome do Aluno</th>
                @foreach($subjects as $subject)
                    <th colspan="{{ $colsPerSubject }}">{{ $subject->code ?? strtoupper(mb_substr($subject->name, 0, 5)) }}</th>
                @endforeach
                <th rowspan="2" style="width: 30px;">MG</th>
                <th rowspan="2" style="width: 50px;">Result.</th>
            </tr>
            <tr>
                @foreach($subjects as $subject)
                    @if($type === 'trimestral')
                        <th class="sub-header">AC1</th>
                        <th class="sub-header">AC2</th>
                        <th class="sub-header">AC3</th>
                        <th class="sub-header">MCS</th>
                        <th class="sub-header">ACP</th>
                        <th class="sub-header">MT</th>
                    @elseif($type === 'anual')
                        <th class="sub-header">T1</th>
                        <th class="sub-header">T2</th>
                        <th class="sub-header">T3</th>
                        <th class="sub-header">MF</th>
                    @else
                        <th class="sub-header">MF</th>
                        <th class="sub-header">EX</th>
                        <th class="sub-header">MFD</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($matrix as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="student-name">{{ $item['student']->first_name }} {{ $item['student']->last_name }}</td>
                    @foreach($subjects as $subject)
                        @php $subData = $item['subjects'][$subject->id] ?? []; @endphp
                        @if($type === 'trimestral')
                            <td>{{ isset($subData['acs1']) && is_numeric($subData['acs1']) ? number_format($subData['acs1'], 1) : '-' }}</td>
                            <td>{{ isset($subData['acs2']) && is_numeric($subData['acs2']) ? number_format($subData['acs2'], 1) : '-' }}</td>
                            <td>{{ isset($subData['acs3']) && is_numeric($subData['acs3']) ? number_format($subData['acs3'], 1) : '-' }}</td>
                            <td><strong>{{ isset($subData['macs']) && is_numeric($subData['macs']) ? number_format($subData['macs'], 1) : '-' }}</strong></td>
                            <td>{{ isset($subData['acp']) && is_numeric($subData['acp']) ? number_format($subData['acp'], 1) : '-' }}</td>
                            <td><strong>{{ isset($subData['mt']) && is_numeric($subData['mt']) ? number_format($subData['mt'], 1) : '-' }}</strong></td>
                        @elseif($type === 'anual')
                            <td>{{ isset($subData['mt1']) && is_numeric($subData['mt1']) ? number_format($subData['mt1'], 1) : '-' }}</td>
                            <td>{{ isset($subData['mt2']) && is_numeric($subData['mt2']) ? number_format($subData['mt2'], 1) : '-' }}</td>
                            <td>{{ isset($subData['mt3']) && is_numeric($subData['mt3']) ? number_format($subData['mt3'], 1) : '-' }}</td>
                            <td><strong>{{ isset($subData['mf']) && is_numeric($subData['mf']) ? number_format($subData['mf'], 1) : '-' }}</strong></td>
                        @else
                            <td>{{ isset($subData['mf']) && is_numeric($subData['mf']) ? number_format($subData['mf'], 1) : '-' }}</td>
                            <td>{{ isset($subData['exam']) && is_numeric($subData['exam']) ? number_format($subData['exam'], 1) : '-' }}</td>
                            <td><strong>{{ isset($subData['mfd']) && is_numeric($subData['mfd']) ? number_format($subData['mfd'], 1) : '-' }}</strong></td>
                        @endif
                    @endforeach
                    <td class="avg-col">
                        {{ $item['overall_average'] > 0 ? number_format($item['overall_average'], 1) : '-' }}
                    </td>
                    <td>
                        @if($item['final_status'] === 'Aprovado')
                            <span class="approved">APROV.</span>
                        @elseif($item['final_status'] === 'Retido')
                            <span class="failed">RETIDO</span>
                        @else
                            <span class="pending">CURSO</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td>
                <div class="signature-line">
                    O Director da Turma<br>
                    <strong>{{ ($class->teacher && !str_starts_with($class->teacher->first_name, 'O Director')) ? ($class->teacher->first_name . ' ' . $class->teacher->last_name) : '' }}</strong>
                </div>
            </td>
            <td>
                <div class="signature-line">
                    O Director Pedagógico<br>
                    @php $pedName = \App\Models\Setting::get('pedagogical_director_name', ''); @endphp
                    <strong>{{ ($pedName && !str_starts_with($pedName, 'O Director')) ? $pedName : '' }}</strong>
                </div>
            </td>
            <td>
                <div class="signature-line">
                    O Director da Escola<br>
                    @php $dirName = \App\Models\Setting::get('director_name', ''); @endphp
                    <strong>{{ ($dirName && !str_starts_with($dirName, 'O Director')) ? $dirName : '' }}</strong>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
