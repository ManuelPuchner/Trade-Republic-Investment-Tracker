# Transfer System Fix - Positive Amounts Only

## Problem

The initial implementation was storing negative amounts in the database, which violated the principle that all transaction amounts should be positive.

## Solution

All amounts are now stored as **POSITIVE values**. The transaction direction (incoming vs outgoing) is determined by the presence of the `to_account_id` field.

## Key Changes

### 1. Transaction Creation (CreateTransaction.php)

```php
// OLD (WRONG):
$outgoingTransaction->amount = -abs($amount);  // Negative
$incomingTransaction->amount = abs($amount);   // Positive

// NEW (CORRECT):
$outgoingTransaction->amount = abs($amount);   // Positive ✅
$outgoingTransaction->to_account_id = $toAccount; // Has destination
$incomingTransaction->amount = abs($amount);   // Positive ✅
$incomingTransaction->to_account_id = null;    // No destination (it's incoming!)
```

### 2. Account Balance Calculation (Account.php)

```php
// OLD (WRONG):
$incomingTransfers = $this->incomingTransfers()->sum('amount');
$outgoingTransfers = $transactions->whereNotNull('to_account_id')->sum('amount');

// NEW (CORRECT):
// Incoming: Transfer type WITHOUT to_account_id
$incomingTransfers = $transactions
    ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
    ->whereNull('to_account_id')
    ->sum('amount');

// Outgoing: Transfer type WITH to_account_id
$outgoingTransfers = $transactions
    ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
    ->whereNotNull('to_account_id')
    ->sum('amount');
```

## How It Works Now

### Transfer Structure

```
Transfer €100 from Account A to Account B:

Outgoing (Account A):
├─ amount: 100.00 (positive in DB)
├─ account_id: A
├─ to_account_id: B (has destination = outgoing)
└─ Effect on balance: -100.00 (subtracted because to_account_id exists)

Incoming (Account B):
├─ amount: 100.00 (positive in DB)
├─ account_id: B
├─ to_account_id: NULL (no destination = incoming)
└─ Effect on balance: +100.00 (added because to_account_id is null)
```

### Logic Rule

-   **Has `to_account_id`** = Outgoing transfer = SUBTRACT from balance
-   **Has `to_account_id` = NULL** = Incoming transfer = ADD to balance

## Benefits

✅ All amounts positive in database
✅ Clean, consistent data model
✅ Easy to understand and maintain
✅ Correct balance calculations
✅ No negative values anywhere

## Files Modified

1. `app/Filament/Resources/Transactions/Pages/CreateTransaction.php`
2. `app/Filament/Resources/Transactions/Pages/EditTransaction.php`
3. `app/Models/Account.php`
4. `DUAL_TRANSACTION_TRANSFER_SYSTEM.md`

## Testing Checklist

-   [ ] Create a new transfer transaction
-   [ ] Verify both transactions have positive amounts in DB
-   [ ] Verify source account balance decreased
-   [ ] Verify destination account balance increased
-   [ ] Verify total balance unchanged
-   [ ] Edit a transfer and verify both update
-   [ ] Delete a transfer and verify both are deleted

## Migration Note

If you have existing transactions with negative amounts, run:

```sql
UPDATE transactions SET amount = ABS(amount) WHERE amount < 0;
```
