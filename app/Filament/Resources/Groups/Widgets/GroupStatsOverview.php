<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class GroupStatsOverview extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof Group) {
            return [];
        }

        $group = $this->record;
        
        // Load transactions with their types
        $transactions = $group->transactions()->with('type')->get();
        
        $totalTransactions = $transactions->count();
        $totalAmount = 0;
        $dividendTotal = 0;
        $netTotal = 0;

        foreach ($transactions as $transaction) {
            $amount = $transaction->amount;
            $typeName = $transaction->type->name;
            
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
        }

        return [
            Stat::make('Total Transactions', $totalTransactions)
                ->description('Number of transactions in this group')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
                
            Stat::make('Net Total', '€' . number_format($netTotal, 2))
                ->description($netTotal >= 0 ? 'Positive balance' : 'Negative balance')
                ->descriptionIcon($netTotal >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netTotal >= 0 ? 'success' : 'danger'),
                
            Stat::make('Total Volume', '€' . number_format($totalAmount, 2))
                ->description('Total transaction volume')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('Dividends', $dividendTotal > 0 ? '€' . number_format($dividendTotal, 2) : 'No dividends')
                ->description('Total dividend income')
                ->descriptionIcon('heroicon-m-gift')
                ->color($dividendTotal > 0 ? 'success' : 'gray'),
        ];
    }
}
