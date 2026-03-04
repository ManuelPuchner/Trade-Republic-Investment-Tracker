<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Budget extends Model
{
    protected $fillable = [
        'budget_category_id',
        'amount',
        'period', // 'monthly', 'quarterly', 'yearly'
        'month',
        'year',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function budgetCategory()
    {
        return $this->belongsTo(BudgetCategory::class);
    }

    /**
     * Get transactions related to this budget category
     */
    public function getSpentAmount()
    {
        // Sum of transactions with matching budget category name
        return Transaction::whereHas('category', function ($query) {
            $query->where('name', 'like', '%' . $this->budgetCategory->name . '%');
        })
        ->filterByPeriod($this->period, $this->month, $this->year)
        ->whereHas('type', function ($query) {
            $query->where('name', 'Ausgabe');
        })
        ->sum('amount');
    }

    /**
     * Get percentage of budget spent
     */
    public function getSpentPercentage()
    {
        if ($this->amount == 0) {
            return 0;
        }
        return min(100, round(($this->getSpentAmount() / $this->amount) * 100, 2));
    }

    /**
     * Get remaining budget
     */
    public function getRemainingAmount()
    {
        return max(0, $this->amount - $this->getSpentAmount());
    }
}
