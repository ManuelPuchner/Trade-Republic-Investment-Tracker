<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionTypeSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 3; // This will appear after performance widget

    protected ?string $heading = 'Transaction Summary by Type';

    protected function getStats(): array
    {
        // Get sum of transactions by type (only past and current transactions)
        $transactionTypeSums = Transaction::select('transaction_type_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as transaction_count')
            ->with('type')
            ->whereDate('date', '<=', today())
            ->groupBy('transaction_type_id')
            ->get();

        $stats = [];

        foreach ($transactionTypeSums as $typeSum) {
            $typeName = $typeSum->type->name ?? 'Unknown Type';
            $totalAmount = $typeSum->total_amount;
            $transactionCount = $typeSum->transaction_count;

            $stats[] = Stat::make($typeName, '€'.number_format($totalAmount, 2))
                ->description($transactionCount.' transaction'.($transactionCount !== 1 ? 's' : ''))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($totalAmount >= 0 ? 'success' : 'danger');
        }

        // Add overall summary (only past and current transactions)
        $overallTotal = Transaction::whereDate('date', '<=', today())->sum('amount');
        $totalTransactions = Transaction::whereDate('date', '<=', today())->count();

        $stats[] = Stat::make('Total (All Types)', '€'.number_format($overallTotal, 2))
            ->description($totalTransactions.' total transaction'.($totalTransactions !== 1 ? 's' : ''))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color($overallTotal >= 0 ? 'success' : 'danger');

        return $stats;
    }
}
