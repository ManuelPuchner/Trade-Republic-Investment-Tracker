<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\PortfolioSetting;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PortfolioPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected ?string $heading = 'Portfolio Performance';

    protected function getStats(): array
    {
        $currentPortfolioValue = PortfolioSetting::getCurrentPortfolioValue();
        $gesamtInvestiert = $this->calculateGesamtInvestiert();
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

    protected function calculateGesamtInvestiert(): float
    {
        // gesamt_investiert = käufe - verkäufe (only past and current transactions)
        $kaeufe = Transaction::whereHas('type', function ($query) {
            $query->where('name', 'Kauf');
        })
            ->whereDate('date', '<=', today())
            ->sum('amount');

        $verkaeufe = Transaction::whereHas('type', function ($query) {
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
