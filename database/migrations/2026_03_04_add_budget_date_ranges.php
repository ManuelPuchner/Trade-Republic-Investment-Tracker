<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Add date range fields for budget validity
            // Allows budgets to be active for specific time periods (e.g., different budgets for different life circumstances)
            $table->date('valid_from')->nullable()->after('year')->comment('Date when budget becomes active');
            $table->date('valid_until')->nullable()->after('valid_from')->comment('Date when budget expires (null = indefinitely)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['valid_from', 'valid_until']);
        });
    }
};
