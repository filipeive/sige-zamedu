<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 18px;
            background: #ffffff;
            color: #0f172a;
        }
        .border-container {
            border: 6px double #1e3a8a;
            padding: 18px 25px;
            box-sizing: border-box;
            background: #fff;
        }
        .header {
            text-align: center;
        }
        .header h3 {
            font-size: 11pt;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1e3a8a;
            font-weight: bold;
        }
        .header h4 {
            font-size: 8.5pt;
            margin: 2px 0;
            font-weight: normal;
            color: #475569;
            text-transform: uppercase;
        }
        .school-header-title {
            font-size: 12pt;
            font-weight: bold;
            color: #047857;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title {
            text-align: center;
            margin: 12px 0 6px 0;
        }
        .title h1 {
            font-size: 20pt;
            margin: 0;
            color: #991b1b;
            letter-spacing: 2px;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            text-transform: uppercase;
        }
        .content {
            font-size: 11pt;
            line-height: 1.6;
            text-align: center;
            margin: 10px 20px;
            color: #1e293b;
        }
        .student-name {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 2px solid #cbd5e1;
            display: inline-block;
            padding: 0 15px;
            margin: 3px 0;
            letter-spacing: 1px;
        }
        .footer-table {
            width: 100%;
            margin-top: 20px;
            text-align: center;
            font-size: 9.5pt;
        }
        .signature-line {
            border-top: 1px solid #334155;
            width: 75%;
            margin: 0 auto;
            padding-top: 4px;
            font-weight: bold;
        }
        .reg-number {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #64748b;
            font-family: monospace;
            text-align: left;
        }
        .stamp-circle-small {
            width: 50px;
            height: 50px;
            border: 1px dashed #94a3b8;
            border-radius: 50%;
            margin: 0 auto;
            font-size: 6pt;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="border-container">
    <!-- Cabeçalho Oficial MINEDH -->
    <div class="header">
        <div style="text-align: center; margin-bottom: 4px;">
            <img src="{{ public_path('images/emblema-mocambique.png') }}" style="height: 48px; width: auto;" alt="Emblema de Moçambique">
        </div>
        <h3>REPÚBLICA DE MOÇAMBIQUE</h3>
        <h4>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h4>
        <h4>GOVERNO DA PROVÍNCIA DE {{ strtoupper($province) }}</h4>
        <div class="school-header-title">{{ strtoupper($schoolName) }}</div>
    </div>

    <!-- Título Principal do Diploma -->
    <div class="title">
        <h1>CERTIFICADO DE CONCLUSÃO</h1>
    </div>

    <!-- Texto Oficial de Conclusão -->
    <div class="content">
        Certifica-se que o(a) estudante<br>
        <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span><br>
        concluiu com aproveitamento no presente estabelecimento de ensino o nível de<br>
        <strong style="color: #047857;">{{ strtoupper($educationLevelName) }} ({{ $className }})</strong><br>
        nos termos do Regulamento Geral do Sistema Nacional de Educação da República de Moçambique.
    </div>

    <!-- Tabela de Assinaturas & Data -->
    <table class="footer-table">
        <tr>
            <td style="width: 35%; text-align: center; vertical-align: bottom;">
                <strong>{{ $district }}</strong>, {{ date('d') }} de {{ \Carbon\Carbon::now()->locale('pt')->monthName }} de {{ date('Y') }}
            </td>
            <td style="width: 30%; text-align: center; vertical-align: bottom;">
                <div class="stamp-circle-small">Selo Branco</div>
            </td>
            <td style="width: 35%; text-align: center; vertical-align: bottom;">
                <div class="signature-line">
                    O Director da Escola<br>
                    <strong>{{ ($directorName && !str_starts_with($directorName, 'O Director')) ? $directorName : '' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="reg-number">
        Documento Oficial MINEDH · Registo N.º MINEDH-{{ $student->student_number }}-{{ date('Y') }}
    </div>
</div>

</body>
</html>
