<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to use raw SQL to modify enum
        DB::statement("ALTER TABLE debts DROP CONSTRAINT IF EXISTS debts_payment_method_check");
        DB::statement("ALTER TABLE debts ADD CONSTRAINT debts_payment_method_check CHECK (payment_method IS NULL OR payment_method IN ('cash', 'bank_transfer', 'trade_republic', 'other'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE debts DROP CONSTRAINT IF EXISTS debts_payment_method_check");
        DB::statement("ALTER TABLE debts ADD CONSTRAINT debts_payment_method_check CHECK (payment_method IS NULL OR payment_method IN ('cash', 'bank_transfer', 'trade_republic'))");
    }
};
