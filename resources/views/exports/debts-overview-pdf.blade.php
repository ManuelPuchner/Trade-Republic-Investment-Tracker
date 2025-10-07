<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schulden Übersicht - {{ date('d.m.Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #0f172a;
            background: #ffffff;
            padding: 30px;
            line-height: 1.5;
        }
        h1 {
            font-family: Arial, Helvetica, sans-serif;
            color: #ef4444;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 3px solid #ef4444;
            letter-spacing: -0.5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 9px;
            margin-bottom: 30px;
            font-weight: 400;
        }
        .section {
            margin-bottom: 25px;
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .section-title {
            background: #ef4444;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 11px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            background: #ffffff;
        }
        th, td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
        }
        th {
            background: #f8fafc;
            font-weight: 700;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #e2e8f0;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .value {
            text-align: right;
            font-weight: 700;
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .highlight {
            background: #fecaca;
            font-weight: 700;
            font-size: 11px;
            border-left: 4px solid #ef4444;
        }
        .highlight td {
            padding: 12px 14px;
        }
        .negative {
            color: #ef4444;
            font-weight: 700;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <h1>Schulden Übersicht</h1>
    <div class="subtitle">Exportiert am {{ now()->format('d.m.Y H:i') }} Uhr</div>

    <div class="section">
        <div class="section-title">Allgemeine Übersicht</div>
        <table>
            <tr>
                <td>Gesamtanzahl Schulden</td>
                <td class="value">{{ $totalDebts }}</td>
            </tr>
            <tr class="highlight">
                <td><strong>Gesamtbetrag Schulden</strong></td>
                <td class="value negative">€ {{ number_format($totalAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Offene Schulden</td>
                <td class="value">{{ $openDebtsCount }}</td>
            </tr>
            <tr class="highlight">
                <td><strong>Offene Schulden Betrag</strong></td>
                <td class="value negative">€ {{ number_format($openDebtsAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bezahlte Schulden</td>
                <td class="value">{{ $paidDebtsCount }}</td>
            </tr>
            <tr>
                <td>Bezahlte Schulden Betrag</td>
                <td class="value positive">€ {{ number_format($paidDebtsAmount, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Statistiken</div>
        <table>
            <tr>
                <td>Durchschnittsbetrag</td>
                <td class="value">€ {{ number_format($avgAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Höchste Schuld</td>
                <td class="value negative">€ {{ number_format($maxAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Niedrigste Schuld</td>
                <td class="value">€ {{ number_format($minAmount, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    @if(!empty($byDebtor))
    <div class="section">
        <div class="section-title">Nach Schuldner</div>
        <table>
            <thead>
                <tr>
                    <th>Schuldner</th>
                    <th style="text-align: right;">Anzahl</th>
                    <th style="text-align: right;">Gesamtbetrag</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byDebtor as $debtor => $stats)
                <tr>
                    <td>{{ $debtor }}</td>
                    <td class="value">{{ $stats['count'] }}</td>
                    <td class="value negative">€ {{ number_format($stats['sum'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($byType))
    <div class="section">
        <div class="section-title">Nach Typ</div>
        <table>
            <thead>
                <tr>
                    <th>Typ</th>
                    <th style="text-align: right;">Anzahl</th>
                    <th style="text-align: right;">Gesamtbetrag</th>
                    <th style="text-align: right;">Durchschnitt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byType as $type => $stats)
                <tr>
                    <td>{{ $type }}</td>
                    <td class="value">{{ $stats['count'] }}</td>
                    <td class="value">€ {{ number_format($stats['sum'], 2, ',', '.') }}</td>
                    <td class="value">€ {{ number_format($stats['avg'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Trade Republic Investment Tracker - Generiert am {{ now()->format('d.m.Y H:i:s') }}
    </div>
</body>
</html>
