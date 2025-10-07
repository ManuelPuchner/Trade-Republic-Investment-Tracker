<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsOverviewExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        // Calculate statistics
        $totalTransactions = $this->transactions->count();
        $totalAmount = $this->transactions->sum('amount');

        // Kassenbestand calculation (same as KassenbestandWidget)
        // Positive contributors (additions to cash)
        $einzahlungen = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Einzahlung';
        })->sum('amount');

        $verkaeufe = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Verkauf';
        })->sum('amount');

        $zinsen = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Zinsen';
        })->sum('amount');

        $dividenden = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Dividenden';
        })->sum('amount');

        // Negative contributors (reductions from cash)
        $kaeufe = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Kauf';
        })->sum('amount');

        $ausgaben = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Ausgabe';
        })->sum('amount');

        $savebackSteuer = $this->transactions->filter(function ($t) {
            return $t->type?->name === 'Saveback Steuer';
        })->sum('amount');

        // Calculate Kassenbestand
        $kassenbestand = $einzahlungen + $verkaeufe + $zinsen + $dividenden - $kaeufe - $ausgaben - $savebackSteuer;

        // Total income (for reference)
        $income = $einzahlungen + $verkaeufe + $zinsen + $dividenden;

        // Total expenses (for reference)
        $expenses = $kaeufe + $ausgaben + $savebackSteuer;

        // By transaction type
        $byType = $this->transactions->groupBy(fn ($t) => $t->type?->name ?? 'Unbekannt')->map(fn ($group) => [
            'count' => $group->count(),
            'sum' => $group->sum('amount'),
            'avg' => $group->avg('amount'),
        ]);

        // By account
        $byAccount = $this->transactions->groupBy(fn ($t) => $t->account?->name ?? 'Unbekannt')->map(fn ($group) => [
            'count' => $group->count(),
            'sum' => $group->sum('amount'),
        ]);

        // By category
        $byCategory = $this->transactions->filter(fn ($t) => $t->category)->groupBy(fn ($t) => $t->category->name)->map(fn ($group) => [
            'count' => $group->count(),
            'sum' => $group->sum('amount'),
        ]);

        // Build overview data
        $overviewData = collect([
            ['label' => '=== ALLGEMEINE ÜBERSICHT ===', 'value' => ''],
            ['label' => 'Gesamtanzahl Transaktionen', 'value' => $totalTransactions],
            ['label' => 'Gesamtbetrag', 'value' => $totalAmount],
            ['label' => '', 'value' => ''],
            ['label' => '=== KASSENBESTAND ===', 'value' => ''],
            ['label' => 'Kassenbestand', 'value' => $kassenbestand],
            ['label' => 'Einnahmen (gesamt)', 'value' => $income],
            ['label' => '  - Einzahlungen', 'value' => $einzahlungen],
            ['label' => '  - Verkäufe', 'value' => $verkaeufe],
            ['label' => '  - Zinsen', 'value' => $zinsen],
            ['label' => '  - Dividenden', 'value' => $dividenden],
            ['label' => 'Ausgaben (gesamt)', 'value' => $expenses],
            ['label' => '  - Käufe', 'value' => $kaeufe],
            ['label' => '  - Ausgaben', 'value' => $ausgaben],
            ['label' => '  - Saveback Steuer', 'value' => $savebackSteuer],
            ['label' => '', 'value' => ''],
            ['label' => '=== WEITERE STATISTIKEN ===', 'value' => ''],
            ['label' => 'Durchschnittsbetrag', 'value' => $this->transactions->avg('amount') ?? 0],
            ['label' => 'Höchster Betrag', 'value' => $this->transactions->max('amount') ?? 0],
            ['label' => 'Niedrigster Betrag', 'value' => $this->transactions->min('amount') ?? 0],
            ['label' => '', 'value' => ''],
            ['label' => '=== NACH TRANSAKTIONSTYP ===', 'value' => ''],
        ]);

        foreach ($byType as $type => $stats) {
            $overviewData->push(['label' => $type.' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $type.' - Summe', 'value' => $stats['sum']]);
            $overviewData->push(['label' => $type.' - Durchschnitt', 'value' => $stats['avg']]);
        }

        $overviewData->push(['label' => '', 'value' => '']);
        $overviewData->push(['label' => '=== NACH KONTO ===', 'value' => '']);

        foreach ($byAccount as $account => $stats) {
            $overviewData->push(['label' => $account.' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $account.' - Summe', 'value' => $stats['sum']]);
        }

        if ($byCategory->isNotEmpty()) {
            $overviewData->push(['label' => '', 'value' => '']);
            $overviewData->push(['label' => '=== NACH KATEGORIE ===', 'value' => '']);

            foreach ($byCategory as $category => $stats) {
                $overviewData->push(['label' => $category.' - Anzahl', 'value' => $stats['count']]);
                $overviewData->push(['label' => $category.' - Summe', 'value' => $stats['sum']]);
            }
        }

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
