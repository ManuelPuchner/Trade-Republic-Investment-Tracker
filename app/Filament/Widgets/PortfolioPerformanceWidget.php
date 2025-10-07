<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\PortfolioSetting;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    protected ?string $heading = '📈 Trade Republic Portfolio Performance';

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Get Trade Republic account
        $tradeRepublicAccount = Account::where('is_trade_republic', true)->first();

        if (! $tradeRepublicAccount) {
            return [
                Stat::make('Error', 'Trade Republic account not found')
                    ->description('Please create a Trade Republic account')
                    ->color('danger'),
            ];
        }

        $currentPortfolioValue = PortfolioSetting::getCurrentPortfolioValue();
        $gesamtInvestiert = $this->calculateGesamtInvestiert($tradeRepublicAccount->id);
        $percentage = $this->calculatePercentage($currentPortfolioValue, $gesamtInvestiert);
        $absoluteGain = $currentPortfolioValue - $gesamtInvestiert;

        return [
            Stat::make('Current Portfolio Value', '€'.number_format($currentPortfolioValue, 2))
                ->description('Use dashboard header action to update')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('info'),

            Stat::make('Performance', ($percentage >= 0 ? '+' : '').number_format($percentage * 100, 2).'%')
                ->description('€'.($absoluteGain >= 0 ? '+' : '').number_format($absoluteGain, 2).' gain/loss')
                ->descriptionIcon($percentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentage >= 0 ? 'success' : 'danger'),
        ];
    }

    protected function calculateGesamtInvestiert(int $accountId): float
    {
        // gesamt_investiert = käufe - verkäufe (only Trade Republic account)
        $kaeufe = Transaction::where('account_id', $accountId)
            ->whereHas('type', function ($query) {
                $query->where('name', 'Kauf');
            })
            ->whereDate('date', '<=', today())
            ->sum('amount');

        $verkaeufe = Transaction::where('account_id', $accountId)
            ->whereHas('type', function ($query) {
                $query->where('name', 'Verkauf');
            })
            ->whereDate('date', '<=', today())
            ->sum('amount');

        return $kaeufe - $verkaeufe;
    }

    protected function calculatePercentage(float $portfolioValue, float $gesamtInvestiert): float
    {
        // percent = portfolio_value / gesamt_investiert - 1
        if ($gesamtInvestiert == 0) {
            return 0;
        }

        return ($portfolioValue / $gesamtInvestiert) - 1;
    }
}
