<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class AccountChartsPage extends Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $title = 'Account Charts';

    protected static ?string $navigationLabel = 'Account Charts';

    protected static ?int $navigationSort = null;

    protected static string $routePath = '/account-charts';

    public function getColumns(): int
    {
        return 2; // 2 column grid layout
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\AccountChart::class,
            \App\Filament\Widgets\AllTimeAccountValueChart::class,
            \App\Filament\Widgets\AccountBalanceHistoryChart::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add header actions here
        ];
    }
}
