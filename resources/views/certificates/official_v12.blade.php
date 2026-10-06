<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Certificado Oficial MINEDH - {{ $student->full_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #0f172a;
            font-size: 11pt;
            line-height: 1.5;
        }
        .outer-border {
            border: 4px double #1e3a8a;
            padding: 15px;
            box-sizing: border-box;
            min-height: 95%;
        }
        .inner-border {
            border: 1px solid #94a3b8;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 12pt;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0f172a;
            font-weight: bold;
        }
        .header h3 {
            font-size: 10pt;
            margin: 3px 0;
            text-transform: uppercase;
            color: #334155;
            font-weight: normal;
        }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-title {
            text-align: center;
            margin: 20px 0 15px 0;
        }
        .doc-title h1 {
            font-size: 18pt;
            margin: 0;
            color: #991b1b;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .cert-text {
            text-align: justify;
            margin: 15px 10px;
            font-size: 11pt;
            line-height: 1.8;
            text-indent: 30px;
        }
        .student-highlight {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10pt;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #475569;
            padding: 6px 10px;
        }
        .grades-table th {
            background-color: #f1f5f9;
            text-transform: uppercase;
            font-size: 9pt;
            color: #1e293b;
        }
        .grades-table td.num-grade {
            text-align: center;
            font-weight: bold;
            width: 70px;
        }
        .final-summary {
            margin: 15px 0;
            padding: 10px 15px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-size: 11pt;
            font-weight: bold;
        }
        .signatures {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .signatures td {
            width: 50%;
            vertical-align: bottom;
            padding: 0 15px;
        }
        .sig-line {
            border-top: 1px solid #0f172a;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
        }
        .seal-box {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-footer {
            margin-top: 25px;
            font-size: 8pt;
            color: #64748b;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="outer-border">
        <div class="inner-border">
            <!-- Cabeçalho Oficial da República de Moçambique -->
            <div class="header">
                <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
                <h3>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h3>
                <h3>GOVERNO DA PROVÍNCIA DE {{ strtoupper($province) }}</h3>
                <small style="color: #64748b; text-transform: uppercase;">Distrito de {{ $district }}</small>
                <div class="school-name">{{ $schoolName }}</div>
            </div>

            <div class="doc-title">
                <h1>CERTIFICADO OFICIAL</h1>
            </div>

            <!-- Texto Oficial de Certificação MINEDH -->
            <div class="cert-text">
                Certifica-se, em cumprimento do Regulamento Geral de Avaliação e da Lei 18/2018 do Sistema Nacional de Educação (SNE), que o(a) estudante <span class="student-highlight">{{ $student->full_name }}</span>, filho(a) de {{ $student->parent?->full_name ?? $student->father_name ?? '_____' }} e de {{ $student->mother_name ?? '_____' }}, natural de {{ $student->birth_place ?? $district }}, Província de {{ $province }}, portador(a) do Documento de Identificação/BI Nº <strong>{{ $student->id_number ?? $student->student_number }}</strong>, concluiu no Ano Lectivo de <strong>{{ date('Y') }}</strong> nesta instituição de ensino a <strong>{{ $className }}</strong> do <strong>{{ $educationLevelName }}</strong>, tendo obtido as seguintes classificações por disciplina:
            </div>

            <!-- Tabela de Disciplinas e Notas por Extenso -->
            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Disciplina</th>
                        <th style="width: 80px; text-align: center;">Nota (0-20)</th>
                        <th style="text-align: left;">Classificação por Extenso</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjectsData as $sub)
                        <tr>
                            <td style="font-weight: bold; color: #1e293b;">{{ $sub['name'] }}</td>
                            <td class="num-grade">{{ number_format($sub['grade'], 1, ',', '.') }}</td>
                            <td style="font-style: italic;">{{ $sub['grade_words'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Média Final de Conclusão -->
            <div class="final-summary">
                Média Final de Conclusão do Nível: 
                <span style="color: #991b1b; font-size: 12pt;">{{ number_format($finalAverage, 1, ',', '.') }}</span> 
                (<span style="font-style: italic; color: #1e3a8a;">{{ $finalAverageWords }}</span>)
            </div>

            <div style="margin-top: 15px; font-size: 10pt;">
                E, para constar, se passou o presente Certificado Oficial que vai assinado pelas autoridades competentes e autenticado com o carimbo a óleo em uso nesta instituição escolar.
            </div>

            <div style="text-align: right; margin-top: 20px; font-size: 10pt;">
                {{ $district }}, aos {{ date('d') }} de {{ \Carbon\Carbon::now()->locale('pt')->monthName }} de {{ date('Y') }}.
            </div>

            <!-- Bloco de Assinaturas das Autoridades Escolares -->
            <table class="signatures">
                <tr>
                    <td>
                        <div class="sig-line">{{ $secretaryName }}</div>
                        <small style="color: #475569;">Chefe da Secretaria / Serviços de Registo</small>
                    </td>
                    <td>
                        <div class="sig-line">{{ $directorName }}</div>
                        <small style="color: #475569;">O Director da Escola</small>
                    </td>
                </tr>
            </table>

            <div class="seal-box">
                [ Espaço Reservado ao Selo / Carimbo Branco a Óleo do MINEDH ]
            </div>

            <div class="meta-footer">
                Documento de Emissão Oficial Nº MINEDH-CERT/{{ date('Y') }}/{{ $student->id }} | Antigravity SNE Digital Verification
            </div>
        </div>
    </div>
</body>
</html>
