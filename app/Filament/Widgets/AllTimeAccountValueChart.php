<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AllTimeAccountValueChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'allTimeAccountValueChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'All Time Account Values & Total Development';

    /**
     * Widget column span - half page
     */
    protected int|string|array $columnSpan = 1;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Get all transactions with dates, ordered by date
        $allTransactions = Transaction::with(['account', 'type'])
            ->orderBy('date')
            ->get();

        if ($allTransactions->isEmpty()) {
            return $this->getEmptyChartOptions();
        }

        // Get date range from first transaction to now
        $firstDate = $allTransactions->first()->date;
        $lastDate = Carbon::now();

        // Generate monthly data points
        $dates = collect();
        $currentDate = Carbon::parse($firstDate)->startOfMonth();

        while ($currentDate <= $lastDate) {
            $dates->push($currentDate->copy());
            $currentDate->addMonth();
        }

        $accounts = Account::all();
        $series = [];
        $categories = [];

        // Prepare categories
        foreach ($dates as $date) {
            $categories[] = $date->format('M Y');
        }

        // Create a series for each account
        foreach ($accounts as $account) {
            $accountData = [];

            foreach ($dates as $date) {
                $balance = $this->calculateBalanceUpToDate($account, $date->endOfMonth());
                $accountData[] = (float) $balance;
            }

            $series[] = [
                'name' => $account->name,
                'data' => $accountData,
            ];
        }

        // Create overall total series
        $totalData = [];
        foreach ($dates as $date) {
            $totalValue = 0;

            // Calculate total value across all accounts at this date
            foreach ($accounts as $account) {
                $balance = $this->calculateBalanceUpToDate($account, $date->endOfMonth());
                $totalValue += $balance;
            }

            $totalData[] = (float) $totalValue;
        }

        // Add total series with different styling
        $series[] = [
            'name' => 'Total Value',
            'data' => $totalData,
        ];

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                        'selection' => false,
                        'zoom' => false,
                        'zoomin' => false,
                        'zoomout' => false,
                        'pan' => false,
                        'reset' => true,
                    ],
                ],
                'animations' => [
                    'enabled' => false,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $categories,
                'title' => [
                    'text' => 'Month',
                ],
                'labels' => [
                    'rotate' => -45,
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Value (€)',
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => [2, 2, 2, 4], // Make total line thicker
            ],
            'markers' => [
                'size' => 0,
                'strokeWidth' => 0,
                'hover' => [
                    'size' => 6,
                    'strokeWidth' => 2,
                ],
            ],
            'colors' => ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'], // Different colors for each line
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'position' => 'top',
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: {
                title: {
                    text: 'Total Value (€)'
                },
                labels: {
                    formatter: function (val) {
                        return '€' + val.toFixed(0)
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '€' + val.toFixed(2).replace('.', ',')
                    }
                }
            }
        }
        JS);
    }

    private function getEmptyChartOptions(): array
    {
        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'No Data',
                    'data' => [0],
                ],
            ],
            'xaxis' => [
                'categories' => ['No Data'],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Value (€)',
                ],
            ],
        ];
    }

    private function calculateBalanceUpToDate(Account $account, Carbon $date): float
    {
        // Get all transactions up to the specified date
        $transactions = $account->transactions()->whereDate('date', '<=', $date);

        // Calculate balance using the same logic as Account model
        $einzahlungen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Einzahlung'))->sum('amount');
        $verkaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Verkauf'))->sum('amount');
        $zinsen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Zinsen'))->sum('amount');
        $dividenden = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Dividenden'))->sum('amount');

        $kaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Kauf'))->sum('amount');
        $ausgaben = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Ausgabe'))->sum('amount');
        $savebackSteuer = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Saveback Steuer'))->sum('amount');

        $incomingTransfers = (clone $transactions)
            ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
            ->whereNull('to_account_id')
            ->sum('amount');

        $outgoingTransfers = (clone $transactions)
            ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
            ->whereNotNull('to_account_id')
            ->sum('amount');

        $transactionsBalance = $einzahlungen
            + $verkaeufe
            + $zinsen
            + $dividenden
            + $incomingTransfers
            - $kaeufe
            - $ausgaben
            - $savebackSteuer
            - $outgoingTransfers;

        // For non-Trade Republic accounts, include initial_balance
        if (! $account->is_trade_republic) {
            return ($account->initial_balance ?? 0) + $transactionsBalance;
        }

        return $transactionsBalance;
    }
}
