<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            if (Schema::hasColumn('debts', 'account_id')) {
                $table->dropForeign(['account_id']);
                $table->dropColumn('account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('set null');
        });
    }
};
