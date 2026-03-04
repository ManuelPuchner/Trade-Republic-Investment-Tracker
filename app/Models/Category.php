<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'description',
        'category',
        'subcategory',
        'is_income_category',
    ];

    protected $casts = [
        'is_income_category' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Scope for expense categories only
     */
    public function scopeExpenseCategories($query)
    {
        return $query->where('is_income_category', false);
    }

    /**
     * Scope for income categories only
     */
    public function scopeIncomeCategories($query)
    {
        return $query->where('is_income_category', true);
    }
}