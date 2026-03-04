
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
        // Add budget-related columns to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('subcategory')->nullable()->after('category');
            $table->boolean('is_income_category')->default(false)->after('subcategory');
        });

        // Migrate data from budget_categories to categories
        // This should be done manually or with a seeder based on your data
        
        // Update budgets table to use category_id instead of budget_category_id
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Copy budget_category_id to category_id (do this in a separate data migration)
        // DB::statement('UPDATE budgets SET category_id = budget_category_id');

        // After data migration, remove old column
        // Schema::table('budgets', function (Blueprint $table) {
        //     $table->dropForeign(['budget_category_id']);
        //     $table->dropColumn('budget_category_id');
        // });

        // Drop budget_categories table (after data migration)
        // Schema::dropIfExists('budget_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['category', 'subcategory', 'is_income_category']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
