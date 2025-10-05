<?php

namespace App\Console\Commands;

use App\Models\PortfolioSetting;
use Illuminate\Console\Command;

class UpdatePortfolioValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:update-value {value : The new portfolio value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the current portfolio value for performance calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $value = (float) $this->argument('value');

        PortfolioSetting::setCurrentPortfolioValue($value);

        $this->info('Portfolio value updated to €'.number_format($value, 2));

        return Command::SUCCESS;
    }
}
