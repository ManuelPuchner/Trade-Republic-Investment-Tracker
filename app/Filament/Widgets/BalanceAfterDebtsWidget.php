<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Debt;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class BalanceAfterDebtsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;
    
    protected function getColumns(): int
    {
        return 1; // Single column layout for stats inside this widget
    }
    
    protected function getStats(): array
    {
        $accounts = Account::all();
        $currentBalance = $accounts->sum(fn ($account) => $account->current_balance);
        
        $unpaidDebts = Debt::where('is_paid', false)->sum('amount');
        
        $balanceAfterDebts = $currentBalance + $unpaidDebts;
        
        $difference = $balanceAfterDebts - $currentBalance;
        $percentageIncrease = $currentBalance > 0 
            ? (($difference / $currentBalance) * 100) 
            : 0;

        return [
            Stat::make('Aktueller Gesamtsaldo', '€'.number_format($currentBalance, 2, ',', '.'))
                ->description('Über alle Konten')
                ->descriptionIcon('heroicon-o-wallet')
                ->icon('heroicon-o-currency-euro')
                ->color($currentBalance >= 0 ? 'success' : 'danger'),

            Stat::make('Offene Schulden', '€'.number_format($unpaidDebts, 2, ',', '.'))
                ->description('Noch nicht zurückgezahlt')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            Stat::make('Saldo nach Schuldenrückzahlung', '€'.number_format($balanceAfterDebts, 2, ',', '.'))
                ->description('+€'.number_format($difference, 2, ',', '.').' ('.number_format($percentageIncrease, 1).'% mehr)')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->chart($this->getBalanceAfterDebtsChart()),
        ];
    }

    protected function getBalanceAfterDebtsChart(): array
    {
        // Get projected balance after debts for the last 30 days
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i));
        }

        return $dates->map(function ($date) {
            $balance = Account::all()->sum(fn ($account) => $account->balanceAtDate($date));
            $debts = Debt::where('is_paid', false)
                ->where('created_at', '<=', $date)
                ->sum('amount');
            
            return $balance + $debts;
        })->toArray();
    }
}
