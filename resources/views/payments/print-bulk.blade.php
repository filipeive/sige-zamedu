<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referências de Pagamento em Massa</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .reference-container {
            border: 1px solid #333;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
        }
        .reference-number {
            font-size: 14px;
            font-weight: bold;
            background: #f8f9fa;
            padding: 5px;
            text-align: center;
            margin: 8px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            font-size: 9px;
            color: #666;
        }
        .info-value {
            font-size: 10px;
            margin-bottom: 3px;
        }
        .amount {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
            margin: 10px 0;
            padding: 5px;
            background: #f8f9fa;
        }
        .barcode {
            text-align: center;
            margin: 8px 0;
            padding: 5px;
            border: 1px dashed #ccc;
            font-size: 9px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 5px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c5aa0; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print me-1"></i> Imprimir Todas as Referências
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times me-1"></i> Fechar
        </button>
    </div>

    @foreach($references as $index => $payment)
        <div class="reference-container">
            <div class="header">
                <div class="school-name">{{ strtoupper(setting('school_name', 'ZamEdu')) }}</div>
                <div>REFERÊNCIA DE PAGAMENTO</div>
            </div>

            <div class="reference-number">
                {{ $payment->reference_number }}
            </div>

            <div class="info-grid">
                <div>
                    <div class="info-label">BENEFICIÁRIO</div>
                    <div class="info-value">{{ strtoupper(setting('school_name', 'ZamEdu')) }}</div>

                    <div class="info-label">CONTA</div>
                    <div class="info-value">1234-5678-9012-3456 (BIM)</div>
                </div>
                <div>
                    <div class="info-label">ALUNO</div>
                    <div class="info-value">{{ $payment->student->full_name }}</div>

                    <div class="info-label">TURMA</div>
                    <div class="info-value">{{ $payment->enrollment?->class?->name ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="info-grid">
                <div>
                    <div class="info-label">TIPO</div>
                    <div class="info-value">{{ ucfirst($payment->type) }}</div>
                </div>
                <div>
                    <div class="info-label">VENCIMENTO</div>
                    <div class="info-value">{{ $payment->due_date->format('d/m/Y') }}</div>
                </div>
            </div>

            <div class="amount">
                {{ number_format($payment->total_amount, 2, ',', '.') }} MT
            </div>

            <div class="barcode">
                PAGUE COM ESTE CÓDIGO: {{ $payment->reference_number }}
            </div>
        </div>

        {{-- Quebra de página a cada 4 referências --}}
        @if(($index + 1) % 4 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <script>
        window.onload = function() {
            // Auto-print opcional
            // window.print();
        };
    </script>
</body>
</html>