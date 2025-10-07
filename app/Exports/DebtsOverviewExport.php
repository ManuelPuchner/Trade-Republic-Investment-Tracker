<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class DebtsOverviewExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $debts;

    public function __construct(Collection $debts)
    {
        $this->debts = $debts;
    }

    public function collection()
    {
        // Calculate statistics
        $totalDebts = $this->debts->count();
        $totalAmount = $this->debts->sum('amount');
        $avgAmount = $this->debts->avg('amount');
        $highestDebt = $this->debts->max('amount');
        $lowestDebt = $this->debts->min('amount');
        
        // Paid vs Unpaid
        $paidDebts = $this->debts->where('is_paid', true);
        $unpaidDebts = $this->debts->where('is_paid', false);
        
        $paidCount = $paidDebts->count();
        $paidAmount = $paidDebts->sum('amount');
        $unpaidCount = $unpaidDebts->count();
        $unpaidAmount = $unpaidDebts->sum('amount');
        
        // By debtor
        $byDebtor = $this->debts->groupBy(fn ($d) => $d->debtor?->name ?? 'Unbekannt')->map(fn ($group) => [
            'count' => $group->count(),
            'total_amount' => $group->sum('amount'),
            'paid_count' => $group->where('is_paid', true)->count(),
            'unpaid_count' => $group->where('is_paid', false)->count(),
        ]);
        
        // By payment method
        $byPaymentMethod = $this->debts->filter(fn ($d) => $d->payment_method)->groupBy('payment_method')->map(fn ($group) => [
            'count' => $group->count(),
            'total_amount' => $group->sum('amount'),
        ]);
        
        // Build overview data
        $overviewData = collect([
            ['label' => '=== ALLGEMEINE ÜBERSICHT ===', 'value' => ''],
            ['label' => 'Gesamtanzahl Schulden', 'value' => $totalDebts],
            ['label' => 'Gesamtbetrag', 'value' => $totalAmount],
            ['label' => 'Durchschnittsbetrag', 'value' => $avgAmount],
            ['label' => 'Höchste Schuld', 'value' => $highestDebt],
            ['label' => 'Niedrigste Schuld', 'value' => $lowestDebt],
            ['label' => '', 'value' => ''],
            ['label' => '=== STATUS ===', 'value' => ''],
            ['label' => 'Bezahlt - Anzahl', 'value' => $paidCount],
            ['label' => 'Bezahlt - Betrag', 'value' => $paidAmount],
            ['label' => 'Offen - Anzahl', 'value' => $unpaidCount],
            ['label' => 'Offen - Betrag', 'value' => $unpaidAmount],
            ['label' => 'Bezahlungsrate', 'value' => $totalDebts > 0 ? round(($paidCount / $totalDebts) * 100, 1) . '%' : '0%'],
            ['label' => '', 'value' => ''],
            ['label' => '=== NACH SCHULDNER ===', 'value' => ''],
        ]);
        
        foreach ($byDebtor as $debtor => $stats) {
            $overviewData->push(['label' => $debtor . ' - Anzahl', 'value' => $stats['count']]);
            $overviewData->push(['label' => $debtor . ' - Gesamtbetrag', 'value' => $stats['total_amount']]);
            $overviewData->push(['label' => $debtor . ' - Bezahlt', 'value' => $stats['paid_count']]);
            $overviewData->push(['label' => $debtor . ' - Offen', 'value' => $stats['unpaid_count']]);
        }
        
        if ($byPaymentMethod->isNotEmpty()) {
            $overviewData->push(['label' => '', 'value' => '']);
            $overviewData->push(['label' => '=== NACH ZAHLUNGSART ===', 'value' => '']);
            
            foreach ($byPaymentMethod as $method => $stats) {
                $methodName = match($method) {
                    'cash' => 'Bargeld',
                    'bank_transfer' => 'Überweisung',
                    'paypal' => 'PayPal',
                    'other' => 'Sonstiges',
                    default => $method,
                };
                
                $overviewData->push(['label' => $methodName . ' - Anzahl', 'value' => $stats['count']]);
                $overviewData->push(['label' => $methodName . ' - Betrag', 'value' => $stats['total_amount']]);
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
