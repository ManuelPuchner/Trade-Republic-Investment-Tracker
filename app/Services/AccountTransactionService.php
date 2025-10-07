<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountTransactionService
{
    /**
     * Create a transfer between two accounts
     */
    public function createTransfer(
        Account $fromAccount,
        Account $toAccount,
        float $amount,
        Carbon $date = null,
        ?string $notes = null,
        ?int $transactionTypeId = null,
        ?int $categoryId = null
    ): Transaction {
        $date = $date ?? now();
        
        // Use Transfer category by default if not specified
        if (!$categoryId) {
            $transferCategory = Category::where('slug', 'transfer')->first();
            $categoryId = $transferCategory?->id;
        }

        return Transaction::create([
            'date' => $date,
            'amount' => $amount,
            'account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'category_id' => $categoryId,
            'transaction_type_id' => $transactionTypeId,
            'notes' => $notes ?? "Transfer from {$fromAccount->name} to {$toAccount->name}",
        ]);
    }

    /**
     * Create an income transaction
     */
    public function createIncome(
        Account $account,
        float $amount,
        Carbon $date = null,
        ?int $categoryId = null,
        ?int $transactionTypeId = null,
        ?string $notes = null
    ): Transaction {
        return Transaction::create([
            'date' => $date ?? now(),
            'amount' => abs($amount), // Ensure positive
            'account_id' => $account->id,
            'category_id' => $categoryId,
            'transaction_type_id' => $transactionTypeId,
            'notes' => $notes,
        ]);
    }

    /**
     * Create an expense transaction
     */
    public function createExpense(
        Account $account,
        float $amount,
        Carbon $date = null,
        ?int $categoryId = null,
        ?int $transactionTypeId = null,
        ?string $notes = null
    ): Transaction {
        return Transaction::create([
            'date' => $date ?? now(),
            'amount' => -abs($amount), // Ensure negative
            'account_id' => $account->id,
            'category_id' => $categoryId,
            'transaction_type_id' => $transactionTypeId,
            'notes' => $notes,
        ]);
    }

    /**
     * Get total balance across all accounts
     */
    public function getTotalBalance(): float
    {
        return Account::all()->sum(fn ($account) => $account->current_balance);
    }

    /**
     * Get balance by account type
     */
    public function getBalanceByType(string $type): float
    {
        return Account::where('account_type', $type)
            ->get()
            ->sum(fn ($account) => $account->current_balance);
    }

    /**
     * Get transactions summary for an account within a date range
     * Uses the same formula as KassenbestandWidget (NO Save Back included)
     */
    public function getAccountSummary(
        Account $account,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $transactions = $account->transactions()->whereBetween('date', [$startDate, $endDate]);

        // Positive contributors (add to balance)
        // Note: Save Back is NOT included to match KassenbestandWidget
        $einzahlungen = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Einzahlung'))->sum('amount');
        $verkaeufe = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Verkauf'))->sum('amount');
        $zinsen = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Zinsen'))->sum('amount');
        $dividenden = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Dividenden'))->sum('amount');
        
        // Negative contributors (subtract from balance)
        $kaeufe = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Kauf'))->sum('amount');
        $ausgaben = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Ausgabe'))->sum('amount');
        $savebackSteuer = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Saveback Steuer'))->sum('amount');
        
        // Transfers
        $incomingTransfers = $account->incomingTransfers()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
        
        $outgoingTransfers = (clone $transactions)
            ->whereNotNull('to_account_id')
            ->sum('amount');

        $income = $einzahlungen + $verkaeufe + $zinsen + $dividenden + $incomingTransfers;
        $expenses = $kaeufe + $ausgaben + $savebackSteuer + $outgoingTransfers;

        return [
            'starting_balance' => $account->balanceAtDate($startDate->copy()->subDay()),
            'income' => $income,
            'expenses' => $expenses,
            'einzahlungen' => $einzahlungen,
            'verkaeufe' => $verkaeufe,
            'zinsen' => $zinsen,
            'dividenden' => $dividenden,
            'kaeufe' => $kaeufe,
            'ausgaben' => $ausgaben,
            'saveback_steuer' => $savebackSteuer,
            'incoming_transfers' => $incomingTransfers,
            'outgoing_transfers' => $outgoingTransfers,
            'net_change' => $income - $expenses,
            'ending_balance' => $account->balanceAtDate($endDate),
            'transaction_count' => $transactions->get()->count(),
        ];
    }

    /**
     * Get spending by category for an account within a date range
     */
    public function getSpendingByCategory(
        Account $account,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        return $account->transactions()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('amount', '<', 0) // Only expenses
            ->whereNotNull('category_id')
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($transactions) {
                return [
                    'category' => $transactions->first()->category->name,
                    'total' => abs($transactions->sum('amount')),
                    'count' => $transactions->count(),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /**
     * Set initial balance for an account
     */
    public function setInitialBalance(
        Account $account,
        float $balance,
        Carbon $date = null
    ): Account {
        $account->update([
            'initial_balance' => $balance,
            'initial_balance_date' => $date ?? now(),
        ]);

        return $account->fresh();
    }
}
