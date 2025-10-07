<?php

namespace App\Filament\Widgets;

use App\Models\Debt;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TotalDebtsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3; // 3 columns for the stats inside this widget
    }

    protected function getStats(): array
    {
        $unpaidDebts = Debt::where('is_paid', false)->sum('amount');
        $paidDebts = Debt::where('is_paid', true)->sum('amount');
        $totalDebts = Debt::sum('amount');

        $unpaidCount = Debt::where('is_paid', false)->count();
        $paidCount = Debt::where('is_paid', true)->count();

        return [
            Stat::make('Offene Schulden', '€'.number_format($unpaidDebts, 2, ',', '.'))
                ->description($unpaidCount.' unbezahlte Schuld(en)')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->icon('heroicon-o-currency-euro')
                ->color('danger')
                ->chart($this->getDebtsChart()),

            Stat::make('Bezahlte Schulden', '€'.number_format($paidDebts, 2, ',', '.'))
                ->description($paidCount.' bezahlte Schuld(en)')
                ->descriptionIcon('heroicon-o-check-circle')
                ->icon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Gesamtschulden', '€'.number_format($totalDebts, 2, ',', '.'))
                ->description(($unpaidCount + $paidCount).' Schulden insgesamt')
                ->icon('heroicon-o-document-text')
                ->color('gray'),
        ];
    }

    protected function getDebtsChart(): array
    {
        // Get unpaid debts trend for the last 30 days
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i));
        }

        return $dates->map(function ($date) {
            return Debt::where('is_paid', false)
                ->where('created_at', '<=', $date)
                ->sum('amount');
        })->toArray();
    }
}
