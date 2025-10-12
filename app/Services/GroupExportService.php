<?php

namespace App\Services;

use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class GroupExportService
{
    public function exportToPdf(Group $group): \Barryvdh\DomPDF\PDF
    {
        // Load relationships to avoid N+1 queries
        $group->load(['transactions.type', 'transactions.account', 'transactions.entity']);
        
        // Calculate statistics
        $stats = $this->calculateGroupStatistics($group);
        
        // Prepare data for PDF
        $data = [
            'group' => $group,
            'stats' => $stats,
            'transactions' => $group->transactions()->with(['type', 'account', 'entity'])->orderBy('date', 'desc')->get(),
            'generated_at' => now(),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.group-report', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }
    
    private function calculateGroupStatistics(Group $group): array
    {
        $transactions = $group->transactions()->with('type')->get();
        
        $totalTransactions = $transactions->count();
        $totalAmount = 0;
        $dividendTotal = 0;
        $netTotal = 0;
        $typeBreakdown = [];
        $monthlyBreakdown = [];
        
        foreach ($transactions as $transaction) {
            $amount = $transaction->amount;
            $typeName = $transaction->type->name;
            
            // Calculate net total based on transaction type
            switch ($typeName) {
                case 'Kauf':
                case 'Ausgabe':
                case 'Saveback Steuer':
                    $netTotal -= $amount;
                    break;
                case 'Verkauf':
                case 'Einzahlungen':
                case 'Zinsen':
                case 'Dividenden':
                    $netTotal += $amount;
                    if ($typeName === 'Dividenden') {
                        $dividendTotal += $amount;
                    }
                    break;
                default:
                    $netTotal += $amount;
                    break;
            }
            
            $totalAmount += abs($amount);
            
            // Type breakdown
            if (!isset($typeBreakdown[$typeName])) {
                $typeBreakdown[$typeName] = ['count' => 0, 'total' => 0];
            }
            $typeBreakdown[$typeName]['count']++;
            $typeBreakdown[$typeName]['total'] += $amount;
            
            // Monthly breakdown
            $month = $transaction->date->format('Y-m');
            if (!isset($monthlyBreakdown[$month])) {
                $monthlyBreakdown[$month] = ['count' => 0, 'total' => 0];
            }
            $monthlyBreakdown[$month]['count']++;
            $monthlyBreakdown[$month]['total'] += $amount;
        }
        
        return [
            'total_transactions' => $totalTransactions,
            'net_total' => $netTotal,
            'total_volume' => $totalAmount,
            'dividend_total' => $dividendTotal,
            'type_breakdown' => $typeBreakdown,
            'monthly_breakdown' => $monthlyBreakdown,
            'date_range' => [
                'start' => $transactions->min('date'),
                'end' => $transactions->max('date'),
            ],
        ];
    }
}