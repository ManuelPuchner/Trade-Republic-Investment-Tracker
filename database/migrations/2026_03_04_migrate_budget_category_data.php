
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kopiere alle BudgetCategories in Categories Tabelle
        $budgetCategories = DB::table('budget_categories')->get();
        
        foreach ($budgetCategories as $budgetCategory) {
            // Prüfe ob Kategorie mit gleichem Namen bereits existiert
            $existingCategory = DB::table('categories')
                ->where('name', $budgetCategory->name)
                ->first();
            
            if ($existingCategory) {
                // Update existing category with budget fields
                DB::table('categories')
                    ->where('id', $existingCategory->id)
                    ->update([
                        'category' => $budgetCategory->category,
                        'subcategory' => $budgetCategory->subcategory,
                    ]);
                
                // Update budgets to use the existing category_id
                DB::table('budgets')
                    ->where('budget_category_id', $budgetCategory->id)
                    ->update(['category_id' => $existingCategory->id]);
            } else {
                // Create new category
                $newCategoryId = DB::table('categories')->insertGetId([
                    'name' => $budgetCategory->name,
                    'slug' => $budgetCategory->slug,
                    'category' => $budgetCategory->category,
                    'subcategory' => $budgetCategory->subcategory,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Update budgets to use the new category_id
                DB::table('budgets')
                    ->where('budget_category_id', $budgetCategory->id)
                    ->update(['category_id' => $newCategoryId]);
            }
        }
        
        // 2. Entferne die alte budget_category_id Spalte
        if (Schema::hasColumn('budgets', 'budget_category_id')) {
            Schema::table('budgets', function ($table) {
                $table->dropForeign(['budget_category_id']);
                $table->dropColumn('budget_category_id');
            });
        }
        
        // 3. Lösche die budget_categories Tabelle
        Schema::dropIfExists('budget_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dies ist schwer rückgängig zu machen, da Daten zusammengeführt wurden
        // Manuelle Wiederherstellung aus Backup erforderlich
    }
};
