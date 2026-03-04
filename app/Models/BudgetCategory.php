<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Budget;

class BudgetCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'subcategory',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($budgetCategory) {
            if (empty($budgetCategory->slug)) {
                $budgetCategory->slug = Str::slug($budgetCategory->name);
            }
        });
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            Budget::class,
            'budget_category_id',
            'id',
            'id',
            'id'
        );
    }
}
