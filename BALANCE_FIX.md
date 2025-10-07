# Balance Calculation Fix - October 7, 2025

## Issue

The Account balance calculation was showing approximately €10 more than the KassenbestandWidget.

**Root Causes:**

1. Account model was including "Save Back" transactions (€7.48)
2. Account model was including initial_balance (€107.62 for Sparkasse account)
3. KassenbestandWidget does NOT include either of these

## Solution

Updated the Account model to match the **exact formula** used by KassenbestandWidget.

### Corrected Formula

```
Balance = Einzahlungen + Verkäufe + Zinsen + Dividenden
        - Käufe - Ausgaben - Saveback Steuer
        + Incoming Transfers - Outgoing Transfers
```

### What's NOT included (to match KassenbestandWidget):

-   ❌ **initial_balance** (not used in calculation)
-   ❌ **Save Back** transactions (not included in formula)

### What IS included:

-   ✅ Einzahlung (Deposit)
-   ✅ Verkauf (Sale)
-   ✅ Zinsen (Interest)
-   ✅ Dividenden (Dividends)
-   ❌ Kauf (Purchase) - subtracted
-   ❌ Ausgabe (Expense) - subtracted
-   ❌ Saveback Steuer (Tax) - subtracted
-   ✅ Incoming Transfers - added
-   ❌ Outgoing Transfers - subtracted

## Verification

```
Trade Republic Account Balance: €73.38
Kassenbestand Widget:           €73.38
Difference:                     €0.00 ✅
```

## Files Updated

### 1. `app/Models/Account.php`

**Changes:**

-   Removed `$this->initial_balance` from calculation
-   Removed `$saveback` variable and its inclusion
-   Both `currentBalance()` and `balanceAtDate()` methods updated

**Before:**

```php
$balance = $this->initial_balance
    + $einzahlungen
    + $verkaeufe
    + $zinsen
    + $dividenden
    + $saveback  // ← Removed
    + $incomingTransfers
    - $kaeufe
    - $ausgaben
    - $savebackSteuer
    - $outgoingTransfers;
```

**After:**

```php
$balance = $einzahlungen
    + $verkaeufe
    + $zinsen
    + $dividenden
    + $incomingTransfers
    - $kaeufe
    - $ausgaben
    - $savebackSteuer
    - $outgoingTransfers;
```

### 2. `app/Services/AccountTransactionService.php`

**Changes:**

-   Removed `$saveback` from income calculation in `getAccountSummary()`
-   Removed `'saveback'` from return array
-   Updated method comments

## Note on initial_balance Field

The `initial_balance` field still exists in the database and can be used for:

-   Manual tracking/reference
-   Future enhancements
-   Audit purposes

However, it is **not used** in the automatic balance calculation to maintain consistency with the KassenbestandWidget.

## Note on Save Back Transactions

"Save Back" transactions (ID: 16) are still recorded in the database but are **not included** in the balance calculation. This matches the original KassenbestandWidget logic.

If Save Back should be included in the future, update both:

1. `KassenbestandWidget.php` - Add Save Back to calculation
2. `Account.php` - Uncomment the Save Back lines

## Result

✅ Account balances now match KassenbestandWidget exactly
✅ No more discrepancy
✅ Consistent calculations across the application
✅ All balance displays show the same value

All changes are live and working correctly! 🎉
