# Cash Reserves & Bank Accounts Tracking System

## Overview

This system extends your investment tracker to manage cash reserves and bank accounts with comprehensive transaction tracking and categorization.

## Features Implemented

### 1. **Categories Table**

-   **Purpose**: Categorize all transactions for better organization and reporting
-   **Fields**:
    -   `name`: Category name (e.g., "Groceries", "Salary")
    -   `slug`: URL-friendly identifier
    -   `color`: Visual color for UI display
    -   `icon`: Heroicon for visual identification
    -   `description`: Optional category description

**Default Categories Created:**

-   Salary, Groceries, Rent, Utilities, Transportation
-   Entertainment, Healthcare, Dining Out, Shopping
-   Investment, Transfer, Other Income, Other Expense

### 2. **Enhanced Accounts Table**

New fields added:

-   `initial_balance`: Starting balance for the account
-   `initial_balance_date`: Date when the initial balance is set

**Account Types Supported:**

-   🏦 Girokonto (Checking)
-   💰 Sparkonto (Savings)
-   📈 Anlagekonto (Investment)
-   💵 Bargeld (Cash)
-   📄 Sonstiges (Other)

**Balance Calculation:**

-   **Current Balance** = Initial Balance + All Transactions (income/expenses) + Incoming Transfers - Outgoing Transfers
-   Accessible via `$account->current_balance` (computed attribute)
-   Historical balance: `$account->balanceAtDate($date)`

### 3. **Enhanced Transactions Table**

New fields added:

-   `account_id`: The account this transaction belongs to (required for all transactions)
-   `category_id`: Transaction category (optional)
-   `to_account_id`: Destination account for transfers (optional, only for transfers)
-   `notes`: Additional notes/description for the transaction

### 4. **Transfer System**

**How transfers work:**

1. Create a single transaction record
2. Set `account_id` to the source account (where money leaves)
3. Set `to_account_id` to the destination account (where money arrives)
4. The amount is subtracted from source and added to destination
5. Optionally use the "Transfer" category

**Example: Cash to Bank Transfer**

```php
Transaction::create([
    'date' => now(),
    'amount' => 500.00, // €500
    'account_id' => $cashAccountId, // From cash
    'to_account_id' => $bankAccountId, // To bank account
    'category_id' => $transferCategoryId,
    'notes' => 'Depositing cash at bank',
]);
```

**Example: Bank to Trade Republic Transfer**

```php
Transaction::create([
    'date' => now(),
    'amount' => 1000.00, // €1000
    'account_id' => $bankAccountId, // From bank
    'to_account_id' => $tradeRepublicAccountId, // To Trade Republic
    'category_id' => $transferCategoryId,
    'notes' => 'Funding investment account',
]);
```

### 5. **Regular Transactions (Income/Expenses)**

For non-transfer transactions, just set the `account_id` and leave `to_account_id` as null.

**Example: Salary Income**

```php
Transaction::create([
    'date' => now(),
    'amount' => 3000.00, // +€3000
    'account_id' => $bankAccountId,
    'category_id' => $salaryCategoryId,
    'notes' => 'October salary',
]);
```

**Example: Grocery Expense**

```php
Transaction::create([
    'date' => now(),
    'amount' => -45.50, // -€45.50
    'account_id' => $cashAccountId,
    'category_id' => $groceriesCategoryId,
    'notes' => 'Weekly groceries at Rewe',
]);
```

## Usage in Filament

### Managing Accounts

1. Navigate to **Konten** (Accounts) in the sidebar
2. Create/Edit accounts with:
    - Basic info (name, bank, account type)
    - Initial balance and date
3. View current balance automatically calculated in the table

### Managing Categories

1. Navigate to **Kategorien** (Categories) in the sidebar
2. Create custom categories with colors and icons
3. All transactions can be tagged with categories

### Creating Transactions

1. Navigate to **Transactions** in the sidebar
2. Fill in the form:
    - **Date**: Transaction date
    - **Amount**: Positive for income, negative for expenses
    - **Konto**: Source account (required)
    - **Kategorie**: Category (optional)
    - **Zielkonto**: Only fill for transfers
    - **Notes**: Additional information

### Viewing Account Balances

The accounts table shows:

-   Initial balance (toggleable)
-   **Current balance** (calculated automatically)
    -   Green with ↑ arrow if positive
    -   Red with ↓ arrow if negative

## Model Relationships

### Account Model

```php
$account->transactions // All outgoing transactions
$account->incomingTransfers // All incoming transfers
$account->current_balance // Computed current balance
$account->balanceAtDate($date) // Historical balance
```

### Transaction Model

```php
$transaction->account // Source account
$transaction->toAccount // Destination account (for transfers)
$transaction->category // Transaction category
$transaction->isTransfer() // Check if it's a transfer
```

### Category Model

```php
$category->transactions // All transactions in this category
```

## Migration Files Created

1. `2025_10_07_000001_create_categories_table.php`
2. `2025_10_07_000002_add_initial_balance_to_accounts_table.php`
3. `2025_10_07_000003_add_account_and_category_to_transactions_table.php`

## Files Modified

-   `app/Models/Account.php` - Added balance calculations
-   `app/Models/Transaction.php` - Added new relationships
-   `app/Models/Category.php` - New model created
-   `app/Filament/Resources/Accounts/Schemas/AccountForm.php` - Updated form
-   `app/Filament/Resources/Accounts/Tables/AccountsTable.php` - Added balance columns
-   `app/Filament/Resources/Transactions/Schemas/TransactionForm.php` - Enhanced with new fields
-   `app/Filament/Resources/Transactions/Tables/TransactionsTable.php` - Added new columns and filters
-   `app/Filament/Resources/Categories/` - New resource created

## Next Steps

### Setting Up Your Accounts

1. Go to **Konten** and create your accounts:
    - Cash account
    - Bank account(s)
    - Trade Republic account
2. Set the initial balance for each account as of today (or a specific date)

### Recording Transactions

1. For regular income/expenses: Set account and category
2. For transfers: Set source account and destination account
3. Use negative amounts for expenses
4. Use positive amounts for income

### Tracking Your Finances

-   View account balances in the Accounts table
-   Filter transactions by account, category, or date
-   Use categories for expense analysis
-   Track cash flow between accounts

## Tips

-   Use the "Transfer" category for all account transfers
-   Keep notes descriptive for future reference
-   Set your initial balances accurately for correct current balance calculations
-   Regularly check that your actual account balances match the system's calculated balances
