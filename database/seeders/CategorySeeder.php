<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Salary',
                'slug' => 'salary',
                'color' => '#10b981', // green
                'icon' => 'heroicon-o-currency-dollar',
                'description' => 'Monthly salary and wages',
            ],
            [
                'name' => 'Groceries',
                'slug' => 'groceries',
                'color' => '#f59e0b', // amber
                'icon' => 'heroicon-o-shopping-cart',
                'description' => 'Food and household items',
            ],
            [
                'name' => 'Rent',
                'slug' => 'rent',
                'color' => '#ef4444', // red
                'icon' => 'heroicon-o-home',
                'description' => 'Monthly rent payments',
            ],
            [
                'name' => 'Utilities',
                'slug' => 'utilities',
                'color' => '#8b5cf6', // violet
                'icon' => 'heroicon-o-bolt',
                'description' => 'Electricity, water, internet, etc.',
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
                'color' => '#3b82f6', // blue
                'icon' => 'heroicon-o-truck',
                'description' => 'Gas, public transport, car maintenance',
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'color' => '#ec4899', // pink
                'icon' => 'heroicon-o-ticket',
                'description' => 'Movies, concerts, hobbies',
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'color' => '#06b6d4', // cyan
                'icon' => 'heroicon-o-heart',
                'description' => 'Medical expenses and insurance',
            ],
            [
                'name' => 'Dining Out',
                'slug' => 'dining-out',
                'color' => '#f97316', // orange
                'icon' => 'heroicon-o-cake',
                'description' => 'Restaurants and cafes',
            ],
            [
                'name' => 'Shopping',
                'slug' => 'shopping',
                'color' => '#a855f7', // purple
                'icon' => 'heroicon-o-shopping-bag',
                'description' => 'Clothing, electronics, and other purchases',
            ],
            [
                'name' => 'Investment',
                'slug' => 'investment',
                'color' => '#14b8a6', // teal
                'icon' => 'heroicon-o-chart-bar',
                'description' => 'Stock purchases and investments',
            ],
            [
                'name' => 'Transfer',
                'slug' => 'transfer',
                'color' => '#6366f1', // indigo
                'icon' => 'heroicon-o-arrows-right-left',
                'description' => 'Transfers between accounts',
            ],
            [
                'name' => 'Other Income',
                'slug' => 'other-income',
                'color' => '#84cc16', // lime
                'icon' => 'heroicon-o-plus-circle',
                'description' => 'Bonuses, gifts, and other income',
            ],
            [
                'name' => 'Other Expense',
                'slug' => 'other-expense',
                'color' => '#64748b', // slate
                'icon' => 'heroicon-o-minus-circle',
                'description' => 'Miscellaneous expenses',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
