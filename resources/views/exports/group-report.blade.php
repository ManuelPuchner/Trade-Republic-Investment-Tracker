<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gruppen-Report: {{ $group->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e293b;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .group-info {
            background: #f8fafc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid {{ $group->color ?? '#64748b' }};
        }
        
        .group-info h2 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 16px;
        }
        
        .group-info .description {
            color: #64748b;
            font-style: italic;
            margin-bottom: 10px;
        }
        
        .stats-container {
            width: 100%;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats-row {
            width: 100%;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
            margin: 0 5px;
            box-sizing: border-box;
        }
        
        .stats-3 .stat-card {
            width: 180px;
        }
        
        .stats-4 .stat-card {
            width: 135px;
        }
        
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
        }
        
        .stat-card.positive .value {
            color: #059669;
        }
        
        .stat-card.negative .value {
            color: #dc2626;
        }
        
        .transactions-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .transactions-table th {
            background: #f1f5f9;
            font-weight: 700;
            color: #475569;
        }
        
        .transactions-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .amount-positive {
            color: #059669;
            font-weight: 700;
        }
        
        .amount-negative {
            color: #dc2626;
            font-weight: 700;
        }
        
        .type-badge {
            background: #e2e8f0;
            color: #475569;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
        
        .color-badge {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .breakdown-section {
            margin-top: 20px;
        }
        
        .breakdown-container {
            width: 100%;
            border-collapse: separate;
            border-spacing: 20px 0;
        }
        
        .breakdown-container td {
            width: 50%;
            vertical-align: top;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        
        .breakdown-table th {
            background: #f1f5f9;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 10px;
        }
        
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color: {{ $group->color ?? '#1e293b' }};">Gruppen-Report</h1>
        <div class="subtitle">{{ $group->name }}</div>
        <div class="subtitle">Erstellt am {{ $generated_at->format('d.m.Y H:i') }}</div>
    </div>


    <div class="stats-container">
        <div class="stats-row {{ $stats['dividend_total'] > 0 ? 'stats-4' : 'stats-3' }}">
            <div class="stat-card">
                <div class="value">{{ $stats['total_transactions'] }}</div>
                <div class="label">Transaktionen</div>
            </div>
            
            <div class="stat-card {{ $stats['net_total'] >= 0 ? 'positive' : 'negative' }}">
                <div class="value">€ {{ number_format($stats['net_total'], 2) }}</div>
                <div class="label">Netto Total</div>
            </div>
            
            <div class="stat-card">
                <div class="value">€ {{ number_format($stats['total_volume'], 2) }}</div>
                <div class="label">Gesamtvolumen</div>
            </div>
            
            @if($stats['dividend_total'] > 0)
            <div class="stat-card positive">
                <div class="value">€ {{ number_format($stats['dividend_total'], 2) }}</div>
                <div class="label">Dividenden</div>
            </div>
            @endif
        </div>
    </div>

    <div class="transactions-section">
        <div class="section-title">Transaktionen ({{ $transactions->count() }} insgesamt)</div>
        
        @if($transactions->count() > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Betrag</th>
                        <th>Typ</th>
                        <th>Konto</th>
                        <th>Beschreibung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d.m.Y') }}</td>
                            <td class="{{ $transaction->amount >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                € {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>
                                <span class="type-badge">{{ $transaction->type->name }}</span>
                            </td>
                            <td>{{ $transaction->account->name ?? '-' }}</td>
                            <td>{{ $transaction->entity->name ?? $transaction->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Keine Transaktionen in dieser Gruppe gefunden.</p>
        @endif
    </div>

    <div class="breakdown-section">
        <table class="breakdown-container">
            <tr>
                <td>
                    <div class="section-title">Aufschlüsselung nach Typ</div>
                    <table class="breakdown-table">
                        <thead>
                            <tr>
                                <th>Typ</th>
                                <th>Anzahl</th>
                                <th>Gesamt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['type_breakdown'] as $type => $data)
                                <tr>
                                    <td>{{ $type }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td class="{{ $data['total'] >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                        € {{ number_format($data['total'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>

                <td>
                    <div class="section-title">Aufschlüsselung nach Monat</div>
                    <table class="breakdown-table">
                        <thead>
                            <tr>
                                <th>Monat</th>
                                <th>Anzahl</th>
                                <th>Gesamt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($stats['monthly_breakdown'], -12, 12, true) as $month => $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('m/Y') }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td class="{{ $data['total'] >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                        € {{ number_format($data['total'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Trade Republic Investment Tracker - Gruppen-Report</p>
        <p>Zeitraum: 
            @if($stats['date_range']['start'] && $stats['date_range']['end'])
                {{ $stats['date_range']['start']->format('d.m.Y') }} bis {{ $stats['date_range']['end']->format('d.m.Y') }}
            @else
                Keine Daten
            @endif
        </p>
    </div>
</body>
</html>