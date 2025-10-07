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
        Schema::table('transactions', function (Blueprint $table) {
            // Account this transaction belongs to
            $table->foreignId('account_id')->nullable()->after('entity_id')->constrained()->cascadeOnDelete();
            
            // Category for the transaction
            $table->foreignId('category_id')->nullable()->after('account_id')->constrained()->cascadeOnDelete();
            
            // For transfers: the destination account
            $table->foreignId('to_account_id')->nullable()->after('category_id')->constrained('accounts')->cascadeOnDelete();
            
            // Optional description/notes
            $table->text('notes')->nullable()->after('to_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['to_account_id']);
            $table->dropColumn(['account_id', 'category_id', 'to_account_id', 'notes']);
        });
    }
};
