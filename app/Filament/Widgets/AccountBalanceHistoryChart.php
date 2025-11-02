<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Account;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountBalanceHistoryChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'accountBalanceHistoryChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Account Balance History (Daily - Last 2 Weeks)';

    /**
     * Widget column span
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Current page for pagination
     */
    public int $currentPage = 0;

    /**
     * Days per page
     */
    public int $daysPerPage = 14;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $accounts = Account::all();
        $series = [];

        // Generate daily dates for the current page
        $dates = collect();
        $startDate = Carbon::now()->subDays($this->daysPerPage + ($this->currentPage * $this->daysPerPage));
        $endDate = Carbon::now()->subDays($this->currentPage * $this->daysPerPage);

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        foreach ($accounts as $account) {
            $data = [];

            foreach ($dates as $dateString) {
                $date = Carbon::createFromFormat('Y-m-d', $dateString);

                // Calculate balance up to end of day
                $balance = $this->calculateBalanceUpToDate($account, $date);
                $data[] = (float) $balance;
            }

            $series[] = [
                'name' => $account->name,
                'data' => $data,
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 500,
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
                'categories' => $dates->map(fn ($date) => Carbon::createFromFormat('Y-m-d', $date)->format('d.m.Y'))->toArray(),
                'title' => [
                    'text' => 'Date',
                ],
                'labels' => [
                    'rotate' => -45,
                    'maxHeight' => 120,
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Balance (€)',
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'markers' => [
                'size' => 0,
                'hover' => [
                    'size' => 4,
                ],
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
                    text: 'Balance (€)'
                },
                labels: {
                    formatter: function (val) {
                        return '€' + val.toFixed(2).replace('.', ',')
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '€' + val.toFixed(2).replace('.', ',')
                    }
                }
            },
            chart: {
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: true,
                        customIcons: [
                            {
                                icon: '<span>←</span>',
                                title: 'Previous 2 Weeks',
                                class: 'custom-icon',
                                click: function(chart, options, e) {
                                    window.Livewire.find(chart.el.closest('[wire\\:id]').getAttribute('wire:id')).call('previousPage');
                                }
                            },
                            {
                                icon: '<span>→</span>',
                                title: 'Next 2 Weeks',
                                class: 'custom-icon',
                                click: function(chart, options, e) {
                                    window.Livewire.find(chart.el.closest('[wire\\:id]').getAttribute('wire:id')).call('nextPage');
                                }
                            },
                            {
                                icon: '<span>📅</span>',
                                title: 'Current Period',
                                class: 'custom-icon',
                                click: function(chart, options, e) {
                                    window.Livewire.find(chart.el.closest('[wire\\:id]').getAttribute('wire:id')).call('currentPeriod');
                                }
                            }
                        ]
                    }
                }
            }
        }
        JS);
    }

    public function previousPage()
    {
        $this->currentPage++;
    }

    public function nextPage()
    {
        $this->currentPage = max(0, $this->currentPage - 1);
    }

    public function currentPeriod()
    {
        $this->currentPage = 0;
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
        $ausschuettungssteuer = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Steuer (Ausschüttung/Ausschüttungsgleicher Ertrag)'))->sum('amount');
        
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
            - $ausschuettungssteuer
            - $outgoingTransfers;

        // For non-Trade Republic accounts, include initial_balance (matching Account model logic)
        // Trade Republic calculates from transactions only
        if (! $account->is_trade_republic) {
            return ($account->initial_balance ?? 0) + $transactionsBalance;
        }

        return $transactionsBalance;
    }
}
