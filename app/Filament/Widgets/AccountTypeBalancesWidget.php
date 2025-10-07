<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AccountTypeBalancesWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4; // 4 columns for the 4 account types in one row
    }
    
    protected function getStats(): array
    {
        $accounts = Account::all();

        $cashBalance = $accounts
            ->where('account_type', 'cash')
            ->sum(fn ($account) => $account->current_balance);

        $checkingBalance = $accounts
            ->where('account_type', 'checking')
            ->sum(fn ($account) => $account->current_balance);

        $savingsBalance = $accounts
            ->where('account_type', 'savings')
            ->sum(fn ($account) => $account->current_balance);

        $investmentBalance = $accounts
            ->where('account_type', 'investment')
            ->sum(fn ($account) => $account->current_balance);

        return [
            Stat::make('Bargeld', '€'.number_format($cashBalance, 2, ',', '.'))
                ->description($accounts->where('account_type', 'cash')->count().' Konto(en)')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->chart($this->getAccountTypeChart('cash')),

            Stat::make('Girokonten', '€'.number_format($checkingBalance, 2, ',', '.'))
                ->description($accounts->where('account_type', 'checking')->count().' Konto(en)')
                ->icon('heroicon-o-building-library')
                ->color('info')
                ->chart($this->getAccountTypeChart('checking')),

            Stat::make('Sparkonten', '€'.number_format($savingsBalance, 2, ',', '.'))
                ->description($accounts->where('account_type', 'savings')->count().' Konto(en)')
                ->icon('heroicon-o-wallet')
                ->color('success')
                ->chart($this->getAccountTypeChart('savings')),

            Stat::make('Anlagekonten', '€'.number_format($investmentBalance, 2, ',', '.'))
                ->description($accounts->where('account_type', 'investment')->count().' Konto(en)')
                ->icon('heroicon-o-chart-bar')
                ->color('primary')
                ->chart($this->getAccountTypeChart('investment')),
        ];
    }

    protected function getAccountTypeChart(string $accountType): array
    {
        // Get balance history for the last 30 days for specific account type
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i));
        }

        return $dates->map(function ($date) use ($accountType) {
            return Account::where('account_type', $accountType)
                ->get()
                ->sum(fn ($account) => $account->balanceAtDate($date));
        })->toArray();
    }
}
