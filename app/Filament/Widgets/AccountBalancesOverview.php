<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Debt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountBalancesOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2; // Two columns for stats inside this widget
    }

    protected function getStats(): array
    {
        $accounts = Account::all();

        $totalBalance = $accounts->sum(fn ($account) => $account->current_balance);

        $unpaidDebts = Debt::where('is_paid', false)->sum('amount');
        $balanceAfterDebts = $totalBalance + $unpaidDebts;
        $percentageIncrease = $totalBalance > 0
            ? (($unpaidDebts / $totalBalance) * 100)
            : 0;

        return [
            Stat::make('Gesamtsaldo', '€'.number_format($totalBalance, 2, ',', '.'))
                ->description('Über alle Konten')
                ->icon('heroicon-o-currency-euro')
                ->color($totalBalance >= 0 ? 'success' : 'danger')
                ->chart($this->getBalanceChart()),

            Stat::make('Saldo nach Schuldenrückzahlung', '€'.number_format($balanceAfterDebts, 2, ',', '.'))
                ->description('+€'.number_format($unpaidDebts, 2, ',', '.').' ('.number_format($percentageIncrease, 1).'% mehr)')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->chart($this->getBalanceAfterDebtsChart()),
        ];
    }

    protected function getBalanceChart(): array
    {
        // Get balance history for the last 30 days
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i));
        }

        return $dates->map(function ($date) {
            return Account::all()->sum(fn ($account) => $account->balanceAtDate($date));
        })->toArray();
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
