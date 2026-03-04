<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'category_id',
        'amount',
        'period', // 'monthly', 'quarterly', 'yearly'
        'month',
        'year',
        'notes',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Für Rückwärtskompatibilität während der Migration
    public function budgetCategory()
    {
        return $this->category();
    }

    /**
     * Scope to get active budgets for a specific date
     */
    public function scopeActiveOn($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : now();

        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $date);
        })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            });
    }

    /**
     * Get the active budget for a specific category and date
     */
    public static function getActiveBudgetForCategory($categoryId, $date = null, $period = 'monthly', $month = null, $year = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        $month = $month ?? $date->month;
        $year = $year ?? $date->year;

        return self::where('category_id', $categoryId)
            ->where('period', $period)
            ->when($period !== 'yearly', function ($q) use ($month) {
                return $q->where('month', $month);
            })
            ->where('year', '<=', $year) // Can use previous year's budget if no current one
            ->activeOn($date)
            ->orderBy('year', 'desc')
            ->orderBy('valid_from', 'desc')
            ->first();
    }

    /**
     * Get spent amount for this budget category
     * Includes all minus transaction types: Ausgabe, Kauf, Saveback Steuer, Steuer (Ausschüttung/Ausschüttungsgleicher Ertrag)
     * 
     * Uses the provided period/month/year from the UI, not the budget's stored month/year
     * (which are kept for backward compatibility but are not used for calculations)
     * 
     * @param string $period The period type (monthly, quarterly, yearly)
     * @param int $month The month to filter by
     * @param int $year The year to filter by
     */
    public function getSpentAmount($period, $month, $year)
    {
        return Transaction::where('category_id', $this->category_id)
            ->filterByPeriod($period, $month, $year)
            ->whereHas('type', function ($query) {
                $query->whereIn('name', [
                    'Ausgabe',
                    'Kauf',
                    'Saveback Steuer',
                    'Steuer (Ausschüttung/Ausschüttungsgleicher Ertrag)',
                ]);
            })
            ->sum('amount');
    }

    /**
     * Get percentage of budget spent
     */
    public function getSpentPercentage($period, $month, $year)
    {
        if ($this->amount == 0) {
            return 0;
        }
        $spent = $this->getSpentAmount($period, $month, $year);

        return round(($spent / $this->amount) * 100, 2);
    }

    /**
     * Get remaining budget
     */
    public function getRemainingAmount($period, $month, $year)
    {
        return $this->amount - $this->getSpentAmount($period, $month, $year);
    }

    /**
     * Get total income for the budget period
     */
    public static function getTotalIncome($period, $month = null, $year = null)
    {
        // Methode 1: Nach TransactionType filtern
        $incomeByType = Transaction::whereHas('type', function ($query) {
            $query->where('name', 'Einnahme');
        })
            ->filterByPeriod($period, $month, $year)
            ->sum('amount');

        // Methode 2: Nach Kategorie filtern (falls TransactionType nicht funktioniert)
        $incomeByCategory = Transaction::whereHas('category', function ($query) {
            $query->where('is_income_category', true);
        })
            ->filterByPeriod($period, $month, $year)
            ->sum('amount');

        // Verwende den höheren Wert (falls eine Methode 0 zurückgibt)
        return max($incomeByType, $incomeByCategory);
    }

    /**
     * Get total expenses for the budget period
     */
    public static function getTotalExpenses($period, $month = null, $year = null)
    {
        // Methode 1: Nach TransactionType filtern
        $expensesByType = Transaction::whereHas('type', function ($query) {
            $query->where('name', 'Ausgabe');
        })
            ->filterByPeriod($period, $month, $year)
            ->sum('amount');

        // Methode 2: Nach Kategorie filtern (falls TransactionType nicht funktioniert)
        $expensesByCategory = Transaction::whereHas('category', function ($query) {
            $query->where('is_income_category', false);
        })
            ->filterByPeriod($period, $month, $year)
            ->sum('amount');

        // Verwende den höheren Wert (falls eine Methode 0 zurückgibt)
        return max($expensesByType, $expensesByCategory);
    }

    /**
     * Get available budget (income - expenses)
     */
    public static function getAvailableBudget($period, $month = null, $year = null)
    {
        $income = self::getTotalIncome($period, $month, $year);
        $expenses = self::getTotalExpenses($period, $month, $year);

        return $income - $expenses;
    }
}
