<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pauta Oficial - {{ $class->name }}</title>
    <style>
        @page { margin: 10mm 8mm 10mm 8mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #0f172a; }
        .header { text-align: center; margin-bottom: 8px; }
        .header h2 { margin: 0; font-size: 11px; text-transform: uppercase; font-weight: bold; }
        .header h3 { margin: 2px 0; font-size: 9px; text-transform: uppercase; font-weight: normal; }
        .header p { margin: 2px 0; font-size: 8px; font-weight: bold; color: #047857; text-transform: uppercase; }
        
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8px; }
        table.meta td { padding: 3px; border: 1px solid #cbd5e1; background-color: #f8fafc; }

        table.pauta-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.pauta-table th, table.pauta-table td { border: 1px solid #000000; padding: 2px 1px; text-align: center; font-size: 7.5px; }
        table.pauta-table th { background-color: #0f172a; color: white; text-transform: uppercase; font-size: 7px; }
        table.pauta-table th.sub { background-color: #e2e8f0; color: black; font-size: 6.5px; }
        
        .text-left { text-align: left !important; padding-left: 3px !important; }
        .pos { color: #166534; font-weight: bold; }
        .neg { color: #991b1b; font-weight: bold; }
        
        .signatures { width: 100%; margin-top: 25px; font-size: 8px; }
        .signatures td { width: 33%; text-align: center; }
        .line { border-top: 1px solid black; width: 80%; margin: 0 auto; padding-top: 2px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
        <h3>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h3>
        <h3>{{ strtoupper(\App\Models\Setting::where('key', 'school_name')->first()?->value ?? 'ESCOLA SECUNDÁRIA ZAMEDU') }}</h3>
        <p>PAUTA OFICIAL DE AVALIAÇÃO PEDAGÓGICA — ANO LECTIVO {{ $year }}</p>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Nível:</strong> {{ $class->education_level_name }}</td>
            <td><strong>Turma:</strong> {{ $class->name }} ({{ $class->grade_level_name }})</td>
            <td><strong>Tipo de Pauta:</strong> {{ strtoupper($type) }} {{ $type === 'trimestral' ? "({$term}º Trimestre)" : '' }}</td>
            <td><strong>Data de Emissão:</strong> {{ date('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="pauta-table">
        <thead>
            <tr>
                <th style="width: 15px;">Nº</th>
                <th class="text-left" style="width: 120px;">Nome Completo do Aluno</th>
                @foreach($subjects as $subject)
                    <th colspan="{{ $type === 'trimestral' ? 6 : ($isExamClass ? 6 : 4) }}">{{ $subject->code ?? strtoupper(mb_substr($subject->name, 0, 4)) }}</th>
                @endforeach
                <th style="width: 25px;">MG</th>
                <th style="width: 40px;">Result.</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                @foreach($subjects as $subject)
                    @if($type === 'trimestral')
                        <th class="sub">AC1</th><th class="sub">AC2</th><th class="sub">AC3</th><th class="sub">MCS</th><th class="sub">ACP</th><th class="sub">MT</th>
                    @else
                        <th class="sub">T1</th><th class="sub">T2</th><th class="sub">T3</th><th class="sub">MF</th>
                        @if($isExamClass) <th class="sub">EX</th><th class="sub">NFD</th> @endif
                    @endif
                @endforeach
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($matrix as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left" style="white-space: nowrap; overflow: hidden;">{{ $row['student']->first_name }} {{ $row['student']->last_name }}</td>
                    @foreach($subjects as $subject)
                        @php $subData = $row['subjects'][$subject->id] ?? []; @endphp
                        @if($type === 'trimestral')
                            @php $mt = $subData['mt'] ?? '-'; @endphp
                            <td>{{ $subData['acs1'] ?? '-' }}</td>
                            <td>{{ $subData['acs2'] ?? '-' }}</td>
                            <td>{{ $subData['acs3'] ?? '-' }}</td>
                            <td><strong>{{ $subData['macs'] ?? '-' }}</strong></td>
                            <td>{{ $subData['acp'] ?? '-' }}</td>
                            <td class="{{ is_numeric($mt) ? ($mt >= 9.5 ? 'pos' : 'neg') : '' }}"><strong>{{ $mt }}</strong></td>
                        @else
                            @php $nfd = $subData['mfd'] ?? '-'; @endphp
                            <td>{{ $subData['mt1'] ?? '-' }}</td>
                            <td>{{ $subData['mt2'] ?? '-' }}</td>
                            <td>{{ $subData['mt3'] ?? '-' }}</td>
                            <td><strong>{{ $subData['mf'] ?? '-' }}</strong></td>
                            @if($isExamClass)
                                <td>{{ $subData['exam'] ?? '-' }}</td>
                                <td class="{{ is_numeric($nfd) ? ($nfd >= 10 ? 'pos' : 'neg') : '' }}"><strong>{{ $nfd }}</strong></td>
                            @endif
                        @endif
                    @endforeach
                    <td><strong>{{ $row['overall_average'] ?: '-' }}</strong></td>
                    <td class="{{ $row['final_status'] === 'Aprovado' ? 'pos' : 'neg' }}">{{ strtoupper($row['final_status']) }}</td>
                </tr>
            @endforeach
            
            <!-- Linhas de Estatística Pedagógica Obrigatórias no PDF -->
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="2" class="text-left">AVALIADOS (HM)</td>
                @foreach($subjects as $subject)
                    <td colspan="{{ $type === 'trimestral' ? 6 : ($isExamClass ? 6 : 4) }}">{{ $stats['subjects'][$subject->id]['hm_evaluated'] ?? 0 }}</td>
                @endforeach
                <td colspan="2">Total: {{ $stats['global']['total'] }}</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" class="text-left">POSITIVAS (HM)</td>
                @foreach($subjects as $subject)
                    <td colspan="{{ $type === 'trimestral' ? 6 : ($isExamClass ? 6 : 4) }}">{{ $stats['subjects'][$subject->id]['hm_positives'] ?? 0 }}</td>
                @endforeach
                <td colspan="2">Aprov: {{ $stats['global']['approved'] }}</td>
            </tr>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="2" class="text-left">% APROVEITAMENTO (HM)</td>
                @foreach($subjects as $subject)
                    <td colspan="{{ $type === 'trimestral' ? 6 : ($isExamClass ? 6 : 4) }}">{{ $stats['subjects'][$subject->id]['hm_perc'] ?? 0 }}%</td>
                @endforeach
                <td colspan="2">{{ $stats['global']['total'] > 0 ? round(($stats['global']['approved'] / $stats['global']['total']) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <table class="signatures" style="margin-top: 30px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <div style="font-weight: bold; text-transform: uppercase; font-size: 8px; margin-bottom: 25px;">O Diretor Adjunto Pedagógico (DAP)</div>
                <div class="line" style="width: 75%;"></div>
                <div style="font-size: 7px; color: #64748b; margin-top: 2px;">(Assinatura e Carimbo Oficial)</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <div style="font-weight: bold; text-transform: uppercase; font-size: 8px; margin-bottom: 2px;">O Diretor da Escola (DAE)</div>
                <div style="font-weight: 900; font-size: 8px; text-transform: uppercase; margin-bottom: 15px;">{{ strtoupper(\App\Models\Setting::where('key', 'director_name')->first()?->value ?? 'PROF. MARIA NHABINDE') }}</div>
                <div class="line" style="width: 75%;"></div>
                <div style="font-size: 7px; color: #64748b; margin-top: 2px;">(Assinatura e Carimbo Oficial)</div>
            </td>
        </tr>
    </table>
</body>
</html>
