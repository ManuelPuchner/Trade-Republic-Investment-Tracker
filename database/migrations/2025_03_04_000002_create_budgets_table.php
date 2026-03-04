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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_category_id')->constrained('budget_categories')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('period', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('month')->nullable(); // 1-12 for monthly/quarterly budgets
            $table->integer('year')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['budget_category_id', 'period', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
