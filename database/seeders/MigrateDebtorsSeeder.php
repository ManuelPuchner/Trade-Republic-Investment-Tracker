<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\Debtor;
use Illuminate\Database\Seeder;

class MigrateDebtorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Finde alle existierenden Schulden mit debtor_name aber ohne debtor_id
        $debts = Debt::whereNotNull('debtor_name')->whereNull('debtor_id')->get();
        
        foreach ($debts as $debt) {
            // Suche oder erstelle Debtor basierend auf dem Namen
            $debtor = Debtor::firstOrCreate(
                ['name' => $debt->debtor_name],
                ['name' => $debt->debtor_name]
            );
            
            // Verknüpfe die Schuld mit dem Debtor
            $debt->update(['debtor_id' => $debtor->id]);
        }
        
        echo "Migriert " . $debts->count() . " Schulden zu Debtor-Entities.\n";
    }
}