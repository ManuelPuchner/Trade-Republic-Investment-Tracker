<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::create([
            'name' => 'Trade Republic',
            'account_number' => null,
            'bank_name' => 'Trade Republic Bank GmbH',
            'account_type' => 'investment',
            'is_trade_republic' => true,
        ]);

        Account::create([
            'name' => 'Sparkasse (Hauptkonto)',
            'account_number' => '',
            'bank_name' => 'Sparkasse Oberösterreich',
            'account_type' => 'checking',
            'is_trade_republic' => false,
        ]);
    }
}
