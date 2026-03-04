<?php

namespace App\Filament\Pages;

use App\Models\Budget;
use App\Models\Transaction;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class BudgetOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Budget-Übersicht';

    protected static ?string $title = 'Budget-Übersicht';

    protected string $view = 'filament.pages.budget-overview';

    protected static string|UnitEnum|null $navigationGroup = 'Finanz Management';

    public ?int $selectedMonth = null;

    public ?int $selectedYear = null;

    public string $selectedPeriod = 'monthly';

    public string $transactionFilter = 'all'; // 'all', 'ausgabe', 'einzahlung

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function getBudgetData(): Collection
    {
        // Create a date from the selected month/year for activeOn scope
        $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);

        return Budget::with('category')
            ->where('period', $this->selectedPeriod)
            ->activeOn($date)
            // Nur Budgets mit Ausgaben-Kategorien anzeigen
            ->whereHas('category', function ($query) {
                $query->where('is_income_category', false);
            })
            ->get()
            ->map(function (Budget $budget) {
                $spent = $budget->getSpentAmount($this->selectedPeriod, $this->selectedMonth, $this->selectedYear);
                $percentage = $budget->getSpentPercentage($this->selectedPeriod, $this->selectedMonth, $this->selectedYear);
                $remaining = $budget->getRemainingAmount($this->selectedPeriod, $this->selectedMonth, $this->selectedYear);

                return [
                    'id' => $budget->id,
                    'name' => $budget->category->name,
                    'subcategory' => $budget->category->subcategory ?? $budget->category->name,
                    'category' => $budget->category->category ?? 'Sonstiges',
                    'budget' => (float) $budget->amount,
                    'spent' => (float) $spent,
                    'remaining' => (float) $remaining,
                    'percentage' => $percentage,
                    'status' => $percentage > 100 ? 'over' : ($percentage > 80 ? 'warning' : 'ok'),
                ];
            })
            ->sortBy('category')
            ->sortBy('subcategory');
    }

    public function getTotalStats(): array
    {
        $budgets = $this->getBudgetData();

        // Einnahmen und Ausgaben aus Transaktionen holen
        $totalIncome = Budget::getTotalIncome(
            $this->selectedPeriod,
            $this->selectedMonth,
            $this->selectedYear
        );

        $totalExpenses = Budget::getTotalExpenses(
            $this->selectedPeriod,
            $this->selectedMonth,
            $this->selectedYear
        );

        $availableBudget = $totalIncome - $totalExpenses;

        if ($budgets->isEmpty()) {
            return [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'available_budget' => $availableBudget,
                'total_budget' => 0,
                'total_spent' => 0,
                'total_remaining' => 0,
                'overall_percentage' => 0,
            ];
        }

        $total_budget = $budgets->sum('budget');
        $total_spent = $budgets->sum('spent');
        $total_remaining = $total_budget - $total_spent;
        $overall_percentage = $total_budget > 0 ? round(($total_spent / $total_budget) * 100, 2) : 0;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'available_budget' => $availableBudget,
            'total_budget' => $total_budget,
            'total_spent' => $total_spent,
            'total_remaining' => $total_remaining,
            'overall_percentage' => $overall_percentage,
        ];
    }

    public function getByCategory(): Collection
    {
        return $this->getBudgetData()
            ->groupBy('category')
            ->map(function ($items, $category) {
                $budget = $items->sum('budget');
                $spent = $items->sum('spent');
                $percentage = $budget > 0 ? round(($spent / $budget) * 100, 2) : 0;

                return [
                    'category' => $category,
                    'budget' => $budget,
                    'spent' => $spent,
                    'remaining' => $budget - $spent,
                    'percentage' => $percentage,
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('spent');
    }

    public function getTransactionsByCategory(): Collection
    {
        $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
        $query = Transaction::with(['category', 'type'])
            ->whereHas('category', function ($q) {
               
            })
            ->filterByPeriod($this->selectedPeriod, $this->selectedMonth, $this->selectedYear);

        // Filter by transaction type if selected
        if ($this->transactionFilter === 'ausgabe') {
            $query->whereHas('type', function ($q) {
                $q->where('name', 'Ausgabe');
            });
        } elseif ($this->transactionFilter === 'einzahlung' || $this->transactionFilter === 'einzahlungen') {
            $query->whereHas('type', function ($q) {
                $q->where('name', 'Einnahme');
            });
        }

        return $query->get()
            ->groupBy(function ($transaction) {
                return $transaction->category->category ?? 'Sonstiges';
            })
            ->map(function ($items, $categoryName) {
                // Group transactions by the actual category name (subcategory/name)
                $groupedByName = $items->groupBy(function ($trans) {
                    return $trans->category->name;
                });

                // Create summary rows for each unique category name
                $transactions = $groupedByName->map(function ($nameGroup, $categoryName) {
                    return [
                        'category' => $categoryName,
                        'subcategory' => $nameGroup->first()->category->subcategory ?? $categoryName,
                        'name' => $categoryName,
                        'amount' => (float) $nameGroup->sum('amount'),
                        'date' => $nameGroup->sortByDesc('date')->first()->date->format('d.m.Y'),
                        'type' => $nameGroup->first()->type->name ?? 'Unbekannt',
                        'notes' => '-',
                        'count' => $nameGroup->count(),
                    ];
                })->sortByDesc('amount')->values();

                return [
                    'category' => $categoryName,
                    'transactions' => $transactions,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('total');
    }
}
