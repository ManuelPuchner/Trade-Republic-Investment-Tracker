<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaktionen Detailliert - {{ date('d.m.Y') }}</title>
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
            color: #f59e0b;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 3px solid #f59e0b;
            letter-spacing: -0.5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 9px;
            margin-bottom: 30px;
            font-weight: 400;
        }
        .date-range {
            background: #fef3c7;
            padding: 10px 14px;
            margin-bottom: 20px;
            border-left: 4px solid #f59e0b;
            font-size: 10px;
            font-weight: 700;
        }
        .section {
            margin-bottom: 25px;
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .section-title {
            background: #f59e0b;
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
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        th {
            background: #f8fafc;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            color: #475569;
            letter-spacing: 0.3px;
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
        .indent {
            padding-left: 30px;
            color: #64748b;
            font-size: 9px;
        }
        .indent .label {
            font-weight: 400;
            font-family: Arial, Helvetica, sans-serif;
        }
        .indent .value {
            color: #64748b;
            font-weight: 400;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .highlight {
            background: #fef3c7;
            font-weight: 700;
            font-size: 11px;
            border-left: 4px solid #f59e0b;
        }
        .highlight td {
            padding: 12px 14px;
        }
        .positive {
            color: #10b981;
        }
        .negative {
            color: #ef4444;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-weight: 400;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            background: #f1f5f9;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            color: #475569;
        }
        .page-break {
            page-break-after: always;
        }
        .transactions-table td {
            font-size: 8px;
            padding: 6px 8px;
        }
        .transactions-table th {
            font-size: 8px;
            padding: 6px 8px;
        }
    </style>
</head>
<body>
    <h1>Transaktionen Detailliert</h1>
    <div class="subtitle">Exportiert am {{ now()->format('d.m.Y H:i') }} Uhr</div>

    @if($dateFrom || $dateTo)
    <div class="date-range">
        Zeitraum: 
        @if($dateFrom)
            {{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }}
        @else
            Beginn
        @endif
        bis
        @if($dateTo)
            {{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}
        @else
            Heute
        @endif
    </div>
    @endif

    <div class="section">
        <div class="section-title">Allgemeine Übersicht</div>
        <table>
            <tr>
                <td>Gesamtanzahl Transaktionen</td>
                <td class="value">{{ $totalTransactions }}</td>
            </tr>
            <tr class="highlight">
                <td><strong>Kassenbestand</strong></td>
                <td class="value {{ $kassenbestand >= 0 ? 'positive' : 'negative' }}">
                    € {{ number_format($kassenbestand, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Gesamte Einnahmen</td>
                <td class="value positive">€ {{ number_format($income, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Einzahlungen</td>
                <td class="value">€ {{ number_format($einzahlungen, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Verkäufe</td>
                <td class="value">€ {{ number_format($verkaeufe, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Zinsen</td>
                <td class="value">€ {{ number_format($zinsen, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Dividenden</td>
                <td class="value">€ {{ number_format($dividenden, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Gesamte Ausgaben</td>
                <td class="value negative">€ {{ number_format($expenses, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Käufe</td>
                <td class="value">€ {{ number_format($kaeufe, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Ausgaben</td>
                <td class="value">€ {{ number_format($ausgaben, 2, ',', '.') }}</td>
            </tr>
            <tr class="indent">
                <td class="label">Saveback Steuer</td>
                <td class="value">€ {{ number_format($savebackSteuer, 2, ',', '.') }}</td>
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
                <td>Höchster Betrag</td>
                <td class="value positive">€ {{ number_format($maxAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Niedrigster Betrag</td>
                <td class="value {{ $minAmount < 0 ? 'negative' : '' }}">€ {{ number_format($minAmount, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    @if(!empty($byType))
    <div class="section">
        <div class="section-title">Nach Transaktionstyp</div>
        <table>
            <thead>
                <tr>
                    <th>Typ</th>
                    <th style="text-align: right;">Anzahl</th>
                    <th style="text-align: right;">Summe</th>
                    <th style="text-align: right;">Ø Betrag</th>
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

    @if(!empty($byAccount))
    <div class="section">
        <div class="section-title">Nach Konto</div>
        <table>
            <thead>
                <tr>
                    <th>Konto</th>
                    <th style="text-align: right;">Anzahl</th>
                    <th style="text-align: right;">Summe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byAccount as $account => $stats)
                <tr>
                    <td>{{ $account }}</td>
                    <td class="value">{{ $stats['count'] }}</td>
                    <td class="value">€ {{ number_format($stats['sum'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($byCategory))
    <div class="section">
        <div class="section-title">Nach Kategorie</div>
        <table>
            <thead>
                <tr>
                    <th>Kategorie</th>
                    <th style="text-align: right;">Anzahl</th>
                    <th style="text-align: right;">Summe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $category => $stats)
                <tr>
                    <td>{{ $category }}</td>
                    <td class="value">{{ $stats['count'] }}</td>
                    <td class="value">€ {{ number_format($stats['sum'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($includeTable && $transactions->isNotEmpty())
    <div class="page-break"></div>
    <div class="section">
        <div class="section-title">Alle Transaktionen</div>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Konto</th>
                    <th>Typ</th>
                    <th>Beschreibung</th>
                    <th>Kategorie</th>
                    <th style="text-align: right;">Betrag</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                @php
                    // Determine the sign based on transaction type
                    $typeName = $transaction->type->name ?? '';
                    $positiveTypes = ['Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden'];
                    $negativeTypes = ['Kauf', 'Ausgabe', 'Saveback Steuer'];
                    
                    if (in_array($typeName, $positiveTypes)) {
                        $sign = '+';
                        $colorClass = 'positive';
                    } elseif (in_array($typeName, $negativeTypes)) {
                        $sign = '-';
                        $colorClass = 'negative';
                    } elseif ($typeName === 'Transfer') {
                        // Transfer: positive if no to_account_id (incoming), negative if has to_account_id (outgoing)
                        $sign = $transaction->to_account_id ? '-' : '+';
                        $colorClass = $transaction->to_account_id ? 'negative' : 'positive';
                    } else {
                        // Fallback to amount sign
                        $sign = $transaction->amount >= 0 ? '+' : '-';
                        $colorClass = $transaction->amount >= 0 ? 'positive' : 'negative';
                    }
                    $absAmount = abs($transaction->amount);
                @endphp
                <tr>
                    <td>{{ $transaction->date->format('d.m.Y') }}</td>
                    <td>{{ $transaction->account->name ?? '—' }}</td>
                    <td>{{ $transaction->type->name ?? '—' }}</td>
                    <td>
                        {{ $transaction->entity?->name ?? $transaction->toAccount?->name ?? '—' }}
                    </td>
                    <td>{{ $transaction->category?->name ?? '—' }}</td>
                    <td class="value {{ $colorClass }}">
                        {{ $sign }} € {{ number_format($absAmount, 2, ',', '.') }}
                    </td>
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
