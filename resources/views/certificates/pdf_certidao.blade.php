<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Certidão de Habilitações Literárias - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', 'Times New Roman', serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .outer-border {
            border: 2px double #0f172a;
            padding: 12px 16px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .header h3 {
            font-size: 9pt;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
            color: #334155;
        }
        .header h4 {
            font-size: 8.5pt;
            margin: 1px 0;
            font-weight: normal;
            color: #475569;
        }
        .school-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 6px 0 2px 0;
            text-transform: uppercase;
            color: #064e3b;
            letter-spacing: 0.5px;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 8px 0;
        }
        .doc-title h1 {
            font-size: 13pt;
            font-weight: font-black;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0f172a;
            margin: 0;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .cert-body {
            text-align: justify;
            margin-bottom: 10px;
            font-size: 9.5pt;
            line-height: 1.4;
        }
        .table-grades {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .table-grades th, .table-grades td {
            border: 1px solid #334155;
            padding: 4px 6px;
            font-size: 8.5pt;
        }
        .table-grades th {
            background-color: #f1f5f9;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #0f172a;
            width: 80%;
            margin: 0 auto;
            padding-top: 3px;
            font-size: 8.5pt;
        }
        .stamp-circle {
            width: 55px;
            height: 55px;
            border: 1px dashed #94a3b8;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6.5pt;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .doc-footer {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

<div class="outer-border">
    <!-- Header Oficial MINEDH Moçambique com Emblema -->
    <div class="header">
        <div style="text-align: center; margin-bottom: 4px;">
            <img src="{{ public_path('images/emblema-mocambique.png') }}" style="height: 46px; width: auto;" alt="Emblema de Moçambique">
        </div>
        <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
        <h3>GOVERNO DA PROVÍNCIA DE {{ strtoupper($province) }}</h3>
        <h4>DIRECÇÃO PROVINCIAL DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h4>
        <div class="school-title">{{ strtoupper($schoolName) }}</div>
    </div>

    <!-- Título do Documento -->
    <div class="doc-title">
        <h1>CERTIDÃO DE HABILITAÇÕES LITERÁRIAS</h1>
    </div>

    <!-- Corpo do Texto Institucional -->
    <div class="cert-body">
        <p style="margin-bottom: 6px;">
            <strong>CERTIFICO</strong>, nos termos da lei e para os devidos efeitos, que <strong>{{ strtoupper($student->first_name . ' ' . $student->last_name) }}</strong>, 
            filho(a) de {{ $student->parent?->full_name ?? 'N/A' }}, portador(a) do documento de identificação N.º <strong>{{ $student->bi_number ?? $student->student_number }}</strong>, 
            concluiu e obteve no presente estabelecimento de ensino as seguintes classificações finais por disciplina:
        </p>

        <table class="table-grades">
            <thead>
                <tr>
                    <th style="width: 50%;">Disciplina / Componente Curricular</th>
                    <th style="width: 20%; text-align: center;">Nota (0 a 20)</th>
                    <th style="width: 30%;">Classificação por Extenso</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjectsData as $sub)
                    <tr>
                        <td><strong>{{ $sub['name'] }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($sub['grade'], 1) }}</td>
                        <td>{{ $sub['grade_words'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">Nenhuma disciplina registada no sistema.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td style="text-transform: uppercase;">MÉDIA GERAL FINAL</td>
                    <td style="text-align: center; color: #064e3b; font-size: 9.5pt;">{{ number_format($finalAverage, 1) }}</td>
                    <td style="color: #064e3b;">{{ $finalAverageWords }}</td>
                </tr>
            </tfoot>
        </table>

        <p style="margin-top: 6px; margin-bottom: 4px;">
            E, por ser verdade e me ter sido solicitado, mandei passar a presente certidão que vai por mim assinada e autenticada com o carimbo a óleo em uso nesta instituição de ensino.
        </p>

        <p style="text-align: right; margin-top: 12px; font-weight: bold; font-size: 9pt;">
            {{ $district }}, {{ date('d') }} de {{ \Carbon\Carbon::now()->locale('pt')->monthName }} de {{ date('Y') }}.
        </p>
    </div>

    <!-- Assinaturas Oficiais -->
    <table class="signature-table">
        <tr>
            <td style="width: 40%; vertical-align: bottom;">
                <div class="signature-line">
                    O Chefe da Secretaria<br>
                    <strong>{{ ($secretaryName && !str_starts_with($secretaryName, 'O Chefe')) ? $secretaryName : '' }}</strong>
                </div>
            </td>
            <td style="width: 20%; text-align: center;">
                <div class="stamp-circle">
                    Selo Branco /<br>Carimbo
                </div>
            </td>
            <td style="width: 40%; vertical-align: bottom;">
                <div class="signature-line">
                    O Director da Escola<br>
                    <strong>{{ ($directorName && !str_starts_with($directorName, 'O Director')) ? $directorName : '' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Documento oficial emitido via Sistema Integrado de Gestão Escolar (SIGE) · MINEDH Moçambique · Registo N.º {{ $student->student_number }}/{{ date('Y') }}
    </div>
</div>

</body>
</html>
