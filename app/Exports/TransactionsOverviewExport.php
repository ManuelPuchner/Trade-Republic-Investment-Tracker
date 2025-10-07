<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

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
        
        // Income (positive transactions)
        $income = $this->transactions->filter(function ($t) {
            return in_array($t->type?->name, ['Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back']);
        })->sum('amount');
        
        // Expenses (negative transactions)
        $expenses = $this->transactions->filter(function ($t) {
            return in_array($t->type?->name, ['Kauf', 'Ausgabe', 'Saveback Steuer']);
        })->sum('amount');
        
        // Net balance
        $netBalance = $income - $expenses;
        
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
            ['label' => 'Einnahmen (gesamt)', 'value' => $income],
            ['label' => 'Ausgaben (gesamt)', 'value' => $expenses],
            ['label' => 'Netto Bilanz', 'value' => $netBalance],
            ['label' => 'Durchschnittsbetrag', 'value' => $this->transactions->avg('amount') ?? 0],
            ['label' => 'Höchster Betrag', 'value' => $this->transactions->max('amount') ?? 0],
            ['label' => 'Niedrigster Betrag', 'value' => $this->transactions->min('amount') ?? 0],
            ['label' => '', 'value' => ''],
            ['label' => '=== NACH TRANSAKTIONSTYP ===', 'value' => ''],
        ]);
        
        foreach ($byType as $type => $stats) {
            $overviewData->push(['label' => $type . ' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $type . ' - Summe', 'value' => $stats['sum']]);
            $overviewData->push(['label' => $type . ' - Durchschnitt', 'value' => $stats['avg']]);
        }
        
        $overviewData->push(['label' => '', 'value' => '']);
        $overviewData->push(['label' => '=== NACH KONTO ===', 'value' => '']);
        
        foreach ($byAccount as $account => $stats) {
            $overviewData->push(['label' => $account . ' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $account . ' - Summe', 'value' => $stats['sum']]);
        }
        
        if ($byCategory->isNotEmpty()) {
            $overviewData->push(['label' => '', 'value' => '']);
            $overviewData->push(['label' => '=== NACH KATEGORIE ===', 'value' => '']);
            
            foreach ($byCategory as $category => $stats) {
                $overviewData->push(['label' => $category . ' - Anzahl', 'value' => $stats['count']]);
                $overviewData->push(['label' => $category . ' - Summe', 'value' => $stats['sum']]);
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
            is_numeric($row['value']) ? '€' . number_format($row['value'], 2, ',', '.') : $row['value']
        ];
    }
}
