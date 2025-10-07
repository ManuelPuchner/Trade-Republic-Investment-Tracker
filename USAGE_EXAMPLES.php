<?php

/**
 * AccountTransactionService Usage Examples
 * 
 * This file demonstrates how to use the AccountTransactionService
 * to manage your accounts and transactions programmatically.
 */

use App\Services\AccountTransactionService;
use App\Models\Account;
use App\Models\Category;
use Carbon\Carbon;

// Initialize the service
$service = new AccountTransactionService();

// ========================================
// 1. SETTING UP ACCOUNTS
// ========================================

// Create a cash account
$cashAccount = Account::create([
    'name' => 'Bargeld',
    'account_type' => 'cash',
    'initial_balance' => 500.00, // €500 in cash
    'initial_balance_date' => now(),
]);

// Create a bank account
$bankAccount = Account::create([
    'name' => 'Sparkasse Girokonto',
    'account_number' => 'DE89370400440532013000',
    'bank_name' => 'Sparkasse',
    'account_type' => 'checking',
    'initial_balance' => 2500.00, // €2500 in bank
    'initial_balance_date' => now(),
]);

// Create Trade Republic account
$tradeRepublicAccount = Account::create([
    'name' => 'Trade Republic',
    'account_type' => 'investment',
    'is_trade_republic' => true,
    'initial_balance' => 1000.00, // €1000 invested
    'initial_balance_date' => now(),
]);

// Or update initial balance later
$service->setInitialBalance($cashAccount, 500.00, now());

// ========================================
// 2. RECORDING INCOME
// ========================================

// Get salary category
$salaryCategory = Category::where('slug', 'salary')->first();

// Record salary received in bank account
$service->createIncome(
    account: $bankAccount,
    amount: 3000.00, // €3000
    date: Carbon::parse('2025-10-01'),
    categoryId: $salaryCategory->id,
    notes: 'October 2025 salary'
);

// ========================================
// 3. RECORDING EXPENSES
// ========================================

// Get categories
$groceriesCategory = Category::where('slug', 'groceries')->first();
$rentCategory = Category::where('slug', 'rent')->first();

// Pay rent from bank account
$service->createExpense(
    account: $bankAccount,
    amount: 800.00, // €800
    date: Carbon::parse('2025-10-01'),
    categoryId: $rentCategory->id,
    notes: 'October rent'
);

// Buy groceries with cash
$service->createExpense(
    account: $cashAccount,
    amount: 45.50, // €45.50
    date: Carbon::parse('2025-10-05'),
    categoryId: $groceriesCategory->id,
    notes: 'Weekly groceries at Rewe'
);

// ========================================
// 4. MAKING TRANSFERS
// ========================================

// Transfer cash to bank
$service->createTransfer(
    fromAccount: $cashAccount,
    toAccount: $bankAccount,
    amount: 200.00, // €200
    date: Carbon::parse('2025-10-10'),
    notes: 'Depositing cash at bank branch'
);

// Transfer from bank to Trade Republic for investing
$service->createTransfer(
    fromAccount: $bankAccount,
    toAccount: $tradeRepublicAccount,
    amount: 500.00, // €500
    date: Carbon::parse('2025-10-15'),
    notes: 'Funding investment account'
);

// ========================================
// 5. CHECKING BALANCES
// ========================================

// Get current balance for an account
$currentBalance = $cashAccount->current_balance;
echo "Cash balance: €{$currentBalance}\n";

// Get balance at a specific date
$balanceOnOct10 = $cashAccount->balanceAtDate(Carbon::parse('2025-10-10'));
echo "Cash balance on Oct 10: €{$balanceOnOct10}\n";

// Get total balance across all accounts
$totalBalance = $service->getTotalBalance();
echo "Total balance across all accounts: €{$totalBalance}\n";

// Get balance by account type
$cashBalances = $service->getBalanceByType('cash');
$checkingBalances = $service->getBalanceByType('checking');
echo "Total in cash accounts: €{$cashBalances}\n";
echo "Total in checking accounts: €{$checkingBalances}\n";

// ========================================
// 6. ANALYZING TRANSACTIONS
// ========================================

// Get monthly summary for an account
$summary = $service->getAccountSummary(
    account: $bankAccount,
    startDate: Carbon::parse('2025-10-01'),
    endDate: Carbon::parse('2025-10-31')
);

echo "Bank Account October Summary:\n";
echo "- Starting Balance: €{$summary['starting_balance']}\n";
echo "- Income: €{$summary['income']}\n";
echo "- Expenses: €{$summary['expenses']}\n";
echo "- Incoming Transfers: €{$summary['incoming_transfers']}\n";
echo "- Outgoing Transfers: €{$summary['outgoing_transfers']}\n";
echo "- Net Change: €{$summary['net_change']}\n";
echo "- Ending Balance: €{$summary['ending_balance']}\n";
echo "- Total Transactions: {$summary['transaction_count']}\n";

// Get spending by category
$spendingByCategory = $service->getSpendingByCategory(
    account: $bankAccount,
    startDate: Carbon::parse('2025-10-01'),
    endDate: Carbon::parse('2025-10-31')
);

echo "\nSpending by Category:\n";
foreach ($spendingByCategory as $item) {
    echo "- {$item['category']}: €{$item['total']} ({$item['count']} transactions)\n";
}

// ========================================
// 7. DIRECT TRANSACTION CREATION
// ========================================

use App\Models\Transaction;

// You can also create transactions directly if you need more control
Transaction::create([
    'date' => now(),
    'amount' => -35.00, // Negative for expense
    'account_id' => $cashAccount->id,
    'category_id' => Category::where('slug', 'entertainment')->first()->id,
    'notes' => 'Movie tickets',
]);

// Create a transfer directly
Transaction::create([
    'date' => now(),
    'amount' => 100.00,
    'account_id' => $cashAccount->id, // From cash
    'to_account_id' => $bankAccount->id, // To bank
    'category_id' => Category::where('slug', 'transfer')->first()->id,
    'notes' => 'Quick deposit',
]);

// ========================================
// 8. QUERYING TRANSACTIONS
// ========================================

// Get all transactions for an account
$allTransactions = $cashAccount->transactions;

// Get transfers only
$transfers = Transaction::transfers()
    ->where('account_id', $cashAccount->id)
    ->get();

// Get non-transfer transactions
$regularTransactions = Transaction::nonTransfer()
    ->where('account_id', $cashAccount->id)
    ->get();

// Get transactions by category
$groceryTransactions = Transaction::where('category_id', $groceriesCategory->id)
    ->where('account_id', $cashAccount->id)
    ->get();

// Get transactions in date range
$octoberTransactions = $cashAccount->transactions()
    ->whereBetween('date', ['2025-10-01', '2025-10-31'])
    ->get();

// Calculate total spent on groceries in October
$totalGroceries = $cashAccount->transactions()
    ->whereBetween('date', ['2025-10-01', '2025-10-31'])
    ->where('category_id', $groceriesCategory->id)
    ->where('amount', '<', 0) // Only expenses
    ->sum('amount');

echo "\nTotal spent on groceries in October: €" . abs($totalGroceries) . "\n";
