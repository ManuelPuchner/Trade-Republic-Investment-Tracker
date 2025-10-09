<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'accountChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Account Balances Overview';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Get all accounts with their current balances
        $accounts = Account::all();

        $labels = [];
        $data = [];

        foreach ($accounts as $account) {
            $labels[] = $account->name;
            // Use the Account model's current_balance attribute which correctly handles initial_balance
            $data[] = (float) $account->current_balance;
        }

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $data,
            'labels' => $labels,
            'legend' => [
                'position' => 'bottom',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '65%',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '14px',
                    'fontWeight' => 'bold',
                ],
                'dropShadow' => [
                    'enabled' => false,
                ],
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '€' + val.toFixed(2).replace('.', ',')
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    return val.toFixed(1) + '%'
                },
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold'
                },
                dropShadow: {
                    enabled: false
                }
            }
        }
        JS);
    }
}
