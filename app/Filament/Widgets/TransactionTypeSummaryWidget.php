<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionTypeSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = '📊 Trade Republic Transaction Summary by Type';
    
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Get Trade Republic account
        $tradeRepublicAccount = Account::where('is_trade_republic', true)->first();
        
        if (!$tradeRepublicAccount) {
            return [
                Stat::make('Error', 'Trade Republic account not found')
                    ->description('Please create a Trade Republic account')
                    ->color('danger')
            ];
        }

        // Get sum of transactions by type (only Trade Republic account)
        $transactionTypeSums = Transaction::select('transaction_type_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as transaction_count')
            ->with('type')
            ->where('account_id', $tradeRepublicAccount->id)
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

        // Add overall summary (only Trade Republic account)
        $overallTotal = Transaction::where('account_id', $tradeRepublicAccount->id)
            ->whereDate('date', '<=', today())
            ->sum('amount');
        $totalTransactions = Transaction::where('account_id', $tradeRepublicAccount->id)
            ->whereDate('date', '<=', today())
            ->count();

        $stats[] = Stat::make('Total (All Types)', '€'.number_format($overallTotal, 2))
            ->description($totalTransactions.' total transaction'.($totalTransactions !== 1 ? 's' : ''))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color($overallTotal >= 0 ? 'success' : 'danger');

        return $stats;
    }
}
