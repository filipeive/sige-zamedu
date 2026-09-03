<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Boletim Escolar — {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #0f172a;
            font-size: 9.5pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-container {
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-align: center;
        }
        .header-container h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #0f172a;
        }
        .header-container h3 {
            font-size: 9pt;
            margin: 2px 0;
            font-weight: normal;
            color: #475569;
            text-transform: uppercase;
        }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .bulletin-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
            color: #047857;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 5px;
            letter-spacing: 1px;
        }
        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .student-info-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 20%;
        }
        .info-value {
            color: #0f172a;
            font-weight: 600;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        .grades-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            padding: 6px 4px;
            border: 1px solid #0f172a;
            text-align: center;
        }
        .grades-table th.left-align, .grades-table td.left-align {
            text-align: left;
            padding-left: 6px;
        }
        .grades-table td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-size: 9pt;
        }
        .grade-value {
            font-weight: bold;
        }
        .grade-positive {
            color: #166534;
        }
        .grade-negative {
            color: #991b1b;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }
        .signature-table {
            width: 100%;
            margin-top: 35px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #0f172a;
            width: 80%;
            margin: 0 auto;
            padding-top: 4px;
            font-size: 8.5pt;
            font-weight: bold;
        }
        .footer-text {
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho Oficial MINEDH Moçambique -->
    <div class="header-container">
        <div style="text-align: center; margin-bottom: 4px;">
            <img src="{{ public_path('images/emblema-mocambique.png') }}" style="height: 42px; width: auto;" alt="Emblema de Moçambique">
        </div>
        <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
        <h3>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h3>
        <div class="school-name">{{ strtoupper(setting('school_name', 'ZamEdu')) }}</div>
    </div>

    <!-- Título do Boletim -->
    <div class="bulletin-title">
        @if($term === 'annual')
            BOLETIM OFICIAL DE APROVEITAMENTO ANUAL
        @else
            BOLETIM INFORMATIVO DO {{ $term }}º TRIMESTRE
        @endif
    </div>

    <!-- Informações do Estudante -->
    <table class="student-info-table">
        <tr>
            <td class="info-label">Estudante:</td>
            <td class="info-value" colspan="3"><strong>{{ $student->full_name }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Nº Matrícula:</td>
            <td class="info-value" style="font-family: monospace;">{{ $student->student_number }}</td>
            <td class="info-label">Turma:</td>
            <td class="info-value">{{ $currentEnrollment->class->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Ano Lectivo:</td>
            <td class="info-value">{{ $currentEnrollment->school_year ?? date('Y') }}</td>
            <td class="info-label">Emissão:</td>
            <td class="info-value">{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <!-- Tabela de Notas -->
    <table class="grades-table">
        <thead>
            @if($term === 'annual')
                <tr>
                    <th class="left-align">Disciplina</th>
                    <th>1º Trim (MT1)</th>
                    <th>2º Trim (MT2)</th>
                    <th>3º Trim (MT3)</th>
                    <th>Média Final (MF)</th>
                    <th>Exame</th>
                    <th>Média Geral (MGD)</th>
                    <th>Situação</th>
                </tr>
            @else
                <tr>
                    <th class="left-align">Disciplina</th>
                    <th>ACS 1</th>
                    <th>ACS 2</th>
                    <th>ACS 3</th>
                    <th>MACS</th>
                    <th>ACP</th>
                    <th>Média Trim. (MT)</th>
                    <th>Aproveitamento</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($matrix as $subjectId => $data)
                @if($term === 'annual')
                    @php
                        $annual = $data['annual'];
                        $isPositive = $annual['mfd'] !== null && $annual['mfd'] >= 10;
                    @endphp
                    <tr>
                        <td class="left-align"><strong>{{ $data['subject']->name }}</strong></td>
                        <td>{{ $annual['mt1'] !== null ? number_format($annual['mt1'], 1) : '-' }}</td>
                        <td>{{ $annual['mt2'] !== null ? number_format($annual['mt2'], 1) : '-' }}</td>
                        <td>{{ $annual['mt3'] !== null ? number_format($annual['mt3'], 1) : '-' }}</td>
                        <td class="grade-value">{{ $annual['mf'] !== null ? number_format($annual['mf'], 1) : '-' }}</td>
                        <td>{{ $annual['exam'] !== null ? number_format($annual['exam'], 1) : '-' }}</td>
                        <td class="grade-value {{ $isPositive ? 'grade-positive' : 'grade-negative' }}">
                            {{ $annual['mfd'] !== null ? number_format($annual['mfd'], 1) : '-' }}
                        </td>
                        <td>
                            @if($annual['status'] === 'Aprovado')
                                <span class="badge badge-success">Aprovado</span>
                            @elseif($annual['status'] === 'Reprovado')
                                <span class="badge badge-danger">Reprovado</span>
                            @else
                                <span class="badge badge-warning">Em Curso</span>
                            @endif
                        </td>
                    </tr>
                @else
                    @php
                        $tData = $data['terms'][$term];
                        $isPositive = $tData['mt'] !== null && $tData['mt'] >= 10;
                    @endphp
                    <tr>
                        <td class="left-align"><strong>{{ $data['subject']->name }}</strong></td>
                        <td>{{ $tData['acs1'] !== null ? number_format($tData['acs1'], 1) : '-' }}</td>
                        <td>{{ $tData['acs2'] !== null ? number_format($tData['acs2'], 1) : '-' }}</td>
                        <td>{{ $tData['acs3'] !== null ? number_format($tData['acs3'], 1) : '-' }}</td>
                        <td><strong>{{ $tData['macs'] !== null ? number_format($tData['macs'], 1) : '-' }}</strong></td>
                        <td>{{ $tData['acp'] !== null ? number_format($tData['acp'], 1) : '-' }}</td>
                        <td class="grade-value {{ $isPositive ? 'grade-positive' : 'grade-negative' }}">
                            {{ $tData['mt'] !== null ? number_format($tData['mt'], 1) : '-' }}
                        </td>
                        <td>
                            @if($tData['mt'] !== null)
                                @if($tData['mt'] >= 10)
                                    <span class="badge badge-success">Satisfeito</span>
                                @else
                                    <span class="badge badge-danger">Insatisfeito</span>
                                @endif
                            @else
                                <span class="badge badge-warning">Pendente</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $term === 'annual' ? 8 : 8 }}" style="text-align: center; color: #64748b;">Nenhuma nota registada neste período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Assinaturas de Validação -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">
                    O Director da Turma
                </div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">
                    O Encarregado de Educação
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-text">
        Documento oficial emitido via SIGE · {{ setting('school_name', 'ZamEdu') }} · MINEDH Moçambique
    </div>

</body>
</html>
