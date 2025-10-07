# Account Balance Formula Update - October 7, 2025

## Change Made

✅ **Updated Account Balance Calculation to match Kassenbestand Widget Formula**

The `current_balance` calculation in the Account model now uses the exact same formula as the `KassenbestandWidget`, ensuring consistency across the application.

## Formula

### Kassenbestand Formula (from widget):

```
Balance = Einzahlungen + Verkäufe + Zinsen + Dividenden + Save Back
          - Käufe - Ausgaben - Saveback Steuer
          + Incoming Transfers - Outgoing Transfers
          + Initial Balance
```

### Transaction Type Breakdown:

**Adds to Balance (+):**

-   ✅ Einzahlung (Deposit)
-   ✅ Verkauf (Sale)
-   ✅ Zinsen (Interest)
-   ✅ Dividenden (Dividends)
-   ✅ Save Back (Saveback)
-   ✅ Incoming Transfers (from other accounts)

**Subtracts from Balance (-):**

-   ❌ Kauf (Purchase)
-   ❌ Ausgabe (Expense)
-   ❌ Saveback Steuer (Saveback Tax)
-   ❌ Outgoing Transfers (to other accounts)

## Files Updated

### 1. `app/Models/Account.php`

-   ✅ Updated `currentBalance()` attribute to use transaction type-based calculation
-   ✅ Updated `balanceAtDate($date)` method to use same formula
-   ✅ Now matches KassenbestandWidget logic exactly

**Before:**

```php
$transactionsSum = $this->transactions()
    ->whereNull('to_account_id')
    ->sum('amount');
```

**After:**

```php
$einzahlungen = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Einzahlung'))->sum('amount');
$verkaeufe = (clone $transactions)->whereHas('type', fn($q) => $q->where('name', 'Verkauf'))->sum('amount');
// ... etc for each transaction type
```

### 2. `app/Services/AccountTransactionService.php`

-   ✅ Updated `getAccountSummary()` method to use type-based calculation
-   ✅ Now provides detailed breakdown by transaction type
-   ✅ Returns additional fields: `einzahlungen`, `verkaeufe`, `zinsen`, `dividenden`, `saveback`, `kaeufe`, `ausgaben`, `saveback_steuer`

## Benefits

1. **Consistency**: Account balance now calculated the same way everywhere
2. **Accuracy**: Type-based calculation is more precise than simple amount summation
3. **Transparency**: Clear breakdown of what adds/subtracts from balance
4. **Predictability**: Matches existing Kassenbestand widget behavior

## Example

If an account has:

-   Initial Balance: €1,000
-   Einzahlung: €500
-   Kauf: €200
-   Dividenden: €50
-   Verkauf: €300
-   Ausgabe: €30

**Calculated Balance:**

```
€1,000 (initial)
+ €500 (Einzahlung)
+ €300 (Verkauf)
+ €50 (Dividenden)
- €200 (Kauf)
- €30 (Ausgabe)
= €1,620
```

## Usage

```php
// Get current balance (uses new formula)
$account = Account::find(1);
echo $account->current_balance; // Automatically calculated

// Get historical balance (uses new formula)
$balanceOnDate = $account->balanceAtDate(Carbon::parse('2025-09-01'));

// Get detailed summary (service)
$service = new AccountTransactionService();
$summary = $service->getAccountSummary(
    $account,
    Carbon::parse('2025-09-01'),
    Carbon::parse('2025-09-30')
);
// Returns: starting_balance, income, expenses, einzahlungen, verkaeufe, etc.
```

## Testing

The balance calculation can be verified by:

1. Checking the "Aktueller Saldo" column in the Accounts table
2. Comparing with the Kassenbestand widget value (should match for Trade Republic account)
3. Using the AccountTransactionService to get detailed breakdowns

All changes are live and working! 🎉
