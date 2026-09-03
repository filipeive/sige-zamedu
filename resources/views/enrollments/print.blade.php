<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Comprovativo de Matrícula - {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</title>
    <style>
        @page {
            margin: 8mm 10mm 8mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #047857;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header h3 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .header h4 {
            font-size: 8pt;
            margin: 1px 0;
            font-weight: normal;
            color: #475569;
            text-transform: uppercase;
        }
        .school-name {
            font-size: 12pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .doc-title {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 8px 0;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 4px;
            letter-spacing: 0.5px;
        }
        .section-box {
            border: 1px solid #cbd5e1;
            margin-bottom: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .section-header {
            background-color: #f1f5f9;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 8.5pt;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3.5px 8px;
            font-size: 8.5pt;
            border-bottom: 1px solid #f1f5f9;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 30%;
        }
        .value {
            color: #0f172a;
            font-weight: 600;
        }
        .signature-table {
            width: 100%;
            margin-top: 22px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #0f172a;
            width: 80%;
            margin: 0 auto;
            padding-top: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7.5pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho Oficial MINEDH -->
    <div class="header">
        <div style="text-align: center; margin-bottom: 4px;">
            <img src="{{ public_path('images/emblema-mocambique.png') }}" style="height: 42px; width: auto;" alt="Emblema de Moçambique">
        </div>
        <h3>REPÚBLICA DE MOÇAMBIQUE</h3>
        <h4>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h4>
        <div class="school-name">{{ strtoupper(setting('school_name', 'ZamEdu')) }}</div>
    </div>

    <!-- Título do Documento -->
    <div class="doc-title">
        COMPROVATIVO OFICIAL DE MATRÍCULA ESCOLAR
    </div>

    <!-- Dados do Aluno -->
    <div class="section-box">
        <div class="section-header">1. Identificação do Estudante</div>
        <table class="info-table">
            <tr>
                <td class="label">Nome Completo:</td>
                <td class="value">{{ $enrollment->student->full_name }}</td>
            </tr>
            <tr>
                <td class="label">Número de Estudante:</td>
                <td class="value" style="font-family: monospace;">{{ $enrollment->student->student_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Gênero:</td>
                <td class="value">{{ $enrollment->student->gender == 'male' ? 'Masculino' : 'Feminino' }}</td>
            </tr>
            <tr>
                <td class="label">Data de Nascimento / Idade:</td>
                <td class="value">
                    {{ $enrollment->student->birthdate ? $enrollment->student->birthdate->format('d/m/Y') : 'N/A' }} 
                    ({{ $enrollment->student->age ?? 'N/A' }} anos)
                </td>
            </tr>
            <tr>
                <td class="label">Encarregado de Educação:</td>
                <td class="value">{{ $enrollment->student->parent?->full_name ?? 'Não atribuído' }}</td>
            </tr>
        </table>
    </div>

    <!-- Dados da Matrícula -->
    <div class="section-box">
        <div class="section-header">2. Dados de Inscrição Escolar</div>
        <table class="info-table">
            <tr>
                <td class="label">Turma Atribuída:</td>
                <td class="value" style="color: #047857;">{{ $enrollment->class->name }}</td>
            </tr>
            <tr>
                <td class="label">Ano Lectivo:</td>
                <td class="value">{{ $enrollment->school_year }}</td>
            </tr>
            <tr>
                <td class="label">Data da Matrícula:</td>
                <td class="value">{{ $enrollment->enrollment_date?->format('d/m/Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Estado da Matrícula:</td>
                <td class="value">{{ ucfirst($enrollment->status) }}</td>
            </tr>
        </table>
    </div>

    <!-- Informações Financeiras (Apenas Escola Privada) -->
    @if(is_private_school())
        <div class="section-box">
            <div class="section-header">3. Condições Financeiras (Escola Privada)</div>
            <table class="info-table">
                <tr>
                    <td class="label">Mensalidade Fixa:</td>
                    <td class="value">{{ number_format($enrollment->monthly_fee, 2, ',', '.') }} MT</td>
                </tr>
                <tr>
                    <td class="label">Dia Limite de Pagamento:</td>
                    <td class="value">Dia {{ $enrollment->payment_day }} de cada mês</td>
                </tr>
                @if ($enrollmentPayment)
                    <tr>
                        <td class="label">Taxa de Matrícula:</td>
                        <td class="value">
                            {{ number_format($enrollmentPayment->amount, 2, ',', '.') }} MT
                            ({{ $enrollmentPayment->status == 'paid' ? 'Paga' : 'Pendente' }})
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    @if ($enrollment->observations)
        <div class="section-box">
            <div class="section-header">Observações</div>
            <div style="padding: 6px 8px; font-size: 8pt; color: #475569;">
                {{ $enrollment->observations }}
            </div>
        </div>
    @endif

    <!-- Assinaturas -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">
                    Assinatura do Encarregado de Educação
                </div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">
                    Pela Secretaria / Direcção Escolar
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Emitido em: {{ now()->format('d/m/Y H:i') }} · {{ setting('school_name', 'ZamEdu') }} · Sistema de Gestão Escolar (SIGE)
    </div>

</body>
</html>
