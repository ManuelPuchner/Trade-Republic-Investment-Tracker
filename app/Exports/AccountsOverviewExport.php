<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountsOverviewExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $accounts;

    public function __construct(Collection $accounts)
    {
        $this->accounts = $accounts;
    }

    public function collection()
    {
        // Calculate statistics
        $totalAccounts = $this->accounts->count();
        $totalBalance = $this->accounts->sum('current_balance');
        $avgBalance = $this->accounts->avg('current_balance');
        $highestBalance = $this->accounts->max('current_balance');
        $lowestBalance = $this->accounts->min('current_balance');

        // Count by type
        $byType = $this->accounts->groupBy('account_type')->map(fn ($group) => [
            'count' => $group->count(),
            'total_balance' => $group->sum('current_balance'),
            'avg_balance' => $group->avg('current_balance'),
        ]);

        // Count by bank
        $byBank = $this->accounts->groupBy('bank_name')->map(fn ($group) => [
            'count' => $group->count(),
            'total_balance' => $group->sum('current_balance'),
        ]);

        // Trade Republic vs Others
        $tradeRepublicCount = $this->accounts->where('is_trade_republic', true)->count();
        $tradeRepublicBalance = $this->accounts->where('is_trade_republic', true)->sum('current_balance');
        $otherCount = $this->accounts->where('is_trade_republic', false)->count();
        $otherBalance = $this->accounts->where('is_trade_republic', false)->sum('current_balance');

        // Build overview data
        $overviewData = collect([
            ['label' => '=== ALLGEMEINE ÜBERSICHT ===', 'value' => ''],
            ['label' => 'Gesamtanzahl Konten', 'value' => $totalAccounts],
            ['label' => 'Gesamtsaldo', 'value' => $totalBalance],
            ['label' => 'Durchschnittssaldo', 'value' => $avgBalance],
            ['label' => 'Höchster Saldo', 'value' => $highestBalance],
            ['label' => 'Niedrigster Saldo', 'value' => $lowestBalance],
            ['label' => '', 'value' => ''],
            ['label' => '=== NACH KONTOTYP ===', 'value' => ''],
        ]);

        foreach ($byType as $type => $stats) {
            $typeName = match ($type) {
                'checking' => 'Girokonto',
                'savings' => 'Sparkonto',
                'investment' => 'Anlagekonto',
                'cash' => 'Bargeld',
                'other' => 'Sonstiges',
                default => $type,
            };

            $overviewData->push(['label' => $typeName.' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $typeName.' - Gesamtsaldo', 'value' => $stats['total_balance']]);
            $overviewData->push(['label' => $typeName.' - Durchschnitt', 'value' => $stats['avg_balance']]);
        }

        $overviewData->push(['label' => '', 'value' => '']);
        $overviewData->push(['label' => '=== NACH BANK ===', 'value' => '']);

        foreach ($byBank as $bank => $stats) {
            $overviewData->push(['label' => ($bank ?: 'Unbekannt').' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => ($bank ?: 'Unbekannt').' - Gesamtsaldo', 'value' => $stats['total_balance']]);
        }

        $overviewData->push(['label' => '', 'value' => '']);
        $overviewData->push(['label' => '=== TRADE REPUBLIC VS ANDERE ===', 'value' => '']);
        $overviewData->push(['label' => 'Trade Republic - Anzahl', 'value' => $tradeRepublicCount]);
        $overviewData->push(['label' => 'Trade Republic - Gesamtsaldo', 'value' => $tradeRepublicBalance]);
        $overviewData->push(['label' => 'Andere Konten - Anzahl', 'value' => $otherCount]);
        $overviewData->push(['label' => 'Andere Konten - Gesamtsaldo', 'value' => $otherBalance]);

        return $overviewData;
    }

    public function headings(): array
    {
        return ['Übersicht', 'Wert'];
    }

    public function map($row): array
    {
        return [
            $row['label'],
            is_numeric($row['value']) ? '€'.number_format($row['value'], 2, ',', '.') : $row['value'],
        ];
    }
}
