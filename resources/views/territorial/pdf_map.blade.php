<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Mapa de Aproveitamento Pedagógico MINEDH</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 12px;
            font-weight: bold;
            color: #475569;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
            color: #059669;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            text-align: center;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .text-left {
            text-align: left !important;
            padding-left: 8px;
        }
        .bg-subtotal {
            background-color: #d1fae5;
            font-weight: bold;
        }
        .bg-total {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer td {
            border: none;
            text-align: center;
            font-size: 10px;
            vertical-align: bottom;
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>REPÚBLICA DE MOÇAMBIQUE</h2>
        <h3>MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h3>
        <p>MAPA DE APROVEITAMENTO PEDAGÓGICO — ENSINO SECUNDÁRIO GERAL ({{ $term }}º TRIMESTRE / {{ $year }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-left">CLASSE</th>
                <th colspan="2">Nº ENTRADAS</th>
                <th colspan="2">DESISTENTES</th>
                <th colspan="2">TRANSFERIDOS</th>
                <th colspan="2">AVALIADOS</th>
                <th colspan="2">APROVADOS</th>
                <th colspan="2">% APROVEITAMENTO</th>
                <th rowspan="2">% DESIST.</th>
            </tr>
            <tr>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
                <th>HM</th><th>M</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['8', '9', '10'] as $lvl)
                @if(isset($mapData['by_level'][$lvl]))
                    @php $row = $mapData['by_level'][$lvl]; @endphp
                    <tr>
                        <td class="text-left">{{ $row['label'] }}</td>
                        <td>{{ $row['entries_hm'] }}</td><td>{{ $row['entries_m'] }}</td>
                        <td>{{ $row['dropouts_hm'] }}</td><td>{{ $row['dropouts_m'] }}</td>
                        <td>{{ $row['transfers_hm'] }}</td><td>{{ $row['transfers_m'] }}</td>
                        <td>{{ $row['evaluated_hm'] }}</td><td>{{ $row['evaluated_m'] }}</td>
                        <td>{{ $row['passed_hm'] }}</td><td>{{ $row['passed_m'] }}</td>
                        <td>{{ $row['approval_rate_hm'] }}%</td><td>{{ $row['approval_rate_m'] }}%</td>
                        <td>{{ $row['dropout_rate_hm'] }}%</td>
                    </tr>
                @endif
            @endforeach

            <tr class="bg-subtotal">
                <td class="text-left">SUB-TOTAL 1º CICLO</td>
                <td>{{ $mapData['ciclo1']['entries_hm'] }}</td><td>{{ $mapData['ciclo1']['entries_m'] }}</td>
                <td>{{ $mapData['ciclo1']['dropouts_hm'] }}</td><td>{{ $mapData['ciclo1']['dropouts_m'] }}</td>
                <td>{{ $mapData['ciclo1']['transfers_hm'] }}</td><td>{{ $mapData['ciclo1']['transfers_m'] }}</td>
                <td>{{ $mapData['ciclo1']['evaluated_hm'] }}</td><td>{{ $mapData['ciclo1']['evaluated_m'] }}</td>
                <td>{{ $mapData['ciclo1']['passed_hm'] }}</td><td>{{ $mapData['ciclo1']['passed_m'] }}</td>
                <td>{{ $mapData['ciclo1']['approval_rate_hm'] }}%</td><td>{{ $mapData['ciclo1']['approval_rate_m'] }}%</td>
                <td>{{ $mapData['ciclo1']['dropout_rate_hm'] }}%</td>
            </tr>

            @foreach(['11', '12'] as $lvl)
                @if(isset($mapData['by_level'][$lvl]))
                    @php $row = $mapData['by_level'][$lvl]; @endphp
                    <tr>
                        <td class="text-left">{{ $row['label'] }}</td>
                        <td>{{ $row['entries_hm'] }}</td><td>{{ $row['entries_m'] }}</td>
                        <td>{{ $row['dropouts_hm'] }}</td><td>{{ $row['dropouts_m'] }}</td>
                        <td>{{ $row['transfers_hm'] }}</td><td>{{ $row['transfers_m'] }}</td>
                        <td>{{ $row['evaluated_hm'] }}</td><td>{{ $row['evaluated_m'] }}</td>
                        <td>{{ $row['passed_hm'] }}</td><td>{{ $row['passed_m'] }}</td>
                        <td>{{ $row['approval_rate_hm'] }}%</td><td>{{ $row['approval_rate_m'] }}%</td>
                        <td>{{ $row['dropout_rate_hm'] }}%</td>
                    </tr>
                @endif
            @endforeach

            <tr class="bg-subtotal">
                <td class="text-left">SUB-TOTAL 2º CICLO</td>
                <td>{{ $mapData['ciclo2']['entries_hm'] }}</td><td>{{ $mapData['ciclo2']['entries_m'] }}</td>
                <td>{{ $mapData['ciclo2']['dropouts_hm'] }}</td><td>{{ $mapData['ciclo2']['dropouts_m'] }}</td>
                <td>{{ $mapData['ciclo2']['transfers_hm'] }}</td><td>{{ $mapData['ciclo2']['transfers_m'] }}</td>
                <td>{{ $mapData['ciclo2']['evaluated_hm'] }}</td><td>{{ $mapData['ciclo2']['evaluated_m'] }}</td>
                <td>{{ $mapData['ciclo2']['passed_hm'] }}</td><td>{{ $mapData['ciclo2']['passed_m'] }}</td>
                <td>{{ $mapData['ciclo2']['approval_rate_hm'] }}%</td><td>{{ $mapData['ciclo2']['approval_rate_m'] }}%</td>
                <td>{{ $mapData['ciclo2']['dropout_rate_hm'] }}%</td>
            </tr>

            <tr class="bg-total">
                <td class="text-left">TOTAL GERAL TERRITÓRIO</td>
                <td>{{ $mapData['totals']['entries_hm'] }}</td><td>{{ $mapData['totals']['entries_m'] }}</td>
                <td>{{ $mapData['totals']['dropouts_hm'] }}</td><td>{{ $mapData['totals']['dropouts_m'] }}</td>
                <td>{{ $mapData['totals']['transfers_hm'] }}</td><td>{{ $mapData['totals']['transfers_m'] }}</td>
                <td>{{ $mapData['totals']['evaluated_hm'] }}</td><td>{{ $mapData['totals']['evaluated_m'] }}</td>
                <td>{{ $mapData['totals']['passed_hm'] }}</td><td>{{ $mapData['totals']['passed_m'] }}</td>
                <td>{{ $mapData['totals']['approval_rate_hm'] }}%</td><td>{{ $mapData['totals']['approval_rate_m'] }}%</td>
                <td>{{ $mapData['totals']['dropout_rate_hm'] }}%</td>
            </tr>
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td style="width: 33%;">O Técnico de Estatística<br><br>__________________________</td>
            <td style="width: 33%;">O Chefe do DAE<br><br>__________________________</td>
            <td style="width: 33%;">O Director Provincial/Distrital<br><br>__________________________</td>
        </tr>
    </table>

</body>
</html>
