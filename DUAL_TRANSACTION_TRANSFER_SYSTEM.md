# Dual Transaction Transfer System

## Overview

Updated the Transfer transaction type to automatically create TWO separate transactions - one outgoing and one incoming - when transferring money between accounts. **All amounts are stored as POSITIVE values in the database**, and the sign is determined by the transaction context (presence of `to_account_id`).

## Key Principles

### 1. All Amounts Are Positive

-   **Database Storage**: All transaction amounts are stored as positive values
-   **Sign Logic**: Determined by transaction type and `to_account_id` field
-   **Balance Calculation**: Uses logic to add or subtract based on transaction type

### 2. Transfer Transaction Structure

**Outgoing Transaction** (source account):

-   Has `to_account_id` set (destination account)
-   Amount is POSITIVE in database
-   **Subtracts** from account balance (because to_account_id is present)

**Incoming Transaction** (destination account):

-   Has `to_account_id` as NULL
-   Amount is POSITIVE in database
-   **Adds** to account balance (because to_account_id is null)

## Changes Made

### 1. CreateTransaction Page

**File**: `app/Filament/Resources/Transactions/Pages/CreateTransaction.php`

#### Updated Logic:

-   **`createTransferTransactions()`**: Creates both transactions with POSITIVE amounts
-   Outgoing: `amount = abs($amount)`, has `to_account_id`
-   Incoming: `amount = abs($amount)`, `to_account_id = null`

### 2. EditTransaction Page

**File**: `app/Filament/Resources/Transactions/Pages/EditTransaction.php`

#### Updated Logic:

-   **`updateTransferTransactions()`**: Updates both transactions with POSITIVE amounts
-   Maintains the outgoing/incoming structure
-   Ensures `to_account_id` is set correctly

### 3. Account Model

**File**: `app/Models/Account.php`

#### New Balance Calculation Logic:

```php
// Incoming transfers: Transfer type WITHOUT to_account_id (ADD to balance)
$incomingTransfers = $transactions
    ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
    ->whereNull('to_account_id')
    ->sum('amount');

// Outgoing transfers: Transfer type WITH to_account_id (SUBTRACT from balance)
$outgoingTransfers = $transactions
    ->whereHas('type', fn ($q) => $q->where('name', 'Transfer'))
    ->whereNotNull('to_account_id')
    ->sum('amount');
```

#### Changes:

-   ✅ Removed `incomingTransfers()` relationship (no longer needed)
-   ✅ Uses `to_account_id` presence to determine transfer direction
-   ✅ All amounts are positive; sign determined by context
-   ✅ Updated both `currentBalance()` and `balanceAtDate()` methods

## Database Structure

### Transaction Relationships

```
Transfer from Account A (ID: 5) to Account B (ID: 7) - Amount: €100

Outgoing Transaction (ID: 1)
├─ amount: 100.00 (POSITIVE)
├─ account_id: 5 (Source - Account A)
├─ to_account_id: 7 (Destination - Account B)
└─ parent_id: 2 (Links to incoming)
   → SUBTRACTS €100 from Account A balance

Incoming Transaction (ID: 2)
├─ amount: 100.00 (POSITIVE)
├─ account_id: 7 (Destination - Account B)
├─ to_account_id: NULL (No destination - it's incoming!)
└─ parent_id: 1 (Links to outgoing)
   → ADDS €100 to Account B balance
```

## Balance Calculation Logic

### For Each Account:

```
Balance =
  + Einzahlungen
  + Verkäufe
  + Zinsen
  + Dividenden
  + Incoming Transfers (Transfer type, to_account_id IS NULL)
  - Käufe
  - Ausgaben
  - Saveback Steuer
  - Outgoing Transfers (Transfer type, to_account_id IS NOT NULL)
```

### Key Points:

1. **Incoming Transfer**: `type = 'Transfer'` AND `to_account_id IS NULL` → ADD amount
2. **Outgoing Transfer**: `type = 'Transfer'` AND `to_account_id IS NOT NULL` → SUBTRACT amount
3. **All amounts positive**: No negative values in database

## Benefits

### 1. Clean Database

-   ✅ All amounts stored as positive values
-   ✅ No negative numbers in amount column
-   ✅ Consistent data model

### 2. Accurate Account Balances

-   ✅ Each account shows correct balance
-   ✅ Transfers properly add/subtract from accounts
-   ✅ Total balance across all accounts remains consistent

### 3. Clear Transfer Logic

-   ✅ Outgoing = has `to_account_id`
-   ✅ Incoming = no `to_account_id`
-   ✅ Both linked via `parent_id`

## Usage

### Creating a Transfer

1. Select "Transfer" as transaction type
2. Enter amount (e.g., 100.00)
3. Select source account
4. Select destination account
5. Click "Create"

**Result**: Two transactions created:

```
Outgoing (Source Account):
- Amount: 100.00 (positive in DB)
- to_account_id: [destination]
- Effect: -100.00 on balance

Incoming (Destination Account):
- Amount: 100.00 (positive in DB)
- to_account_id: NULL
- Effect: +100.00 on balance
```

## Example Scenario

**Transfer €500 from "Checking Account" (ID: 5) to "Savings Account" (ID: 7)**

### Created Transactions:

```
Transaction #1 (Outgoing):
- Date: 2025-10-07
- Type: Transfer
- Account: Checking Account (5)
- Amount: 500.00 (positive)
- To Account: Savings Account (7)
- Parent ID: 2
→ Balance Effect: -€500.00

Transaction #2 (Incoming):
- Date: 2025-10-07
- Type: Transfer
- Account: Savings Account (7)
- Amount: 500.00 (positive)
- To Account: NULL
- Parent ID: 1
→ Balance Effect: +€500.00
```

### Account Balances:

-   **Checking Account**: Decreased by €500
-   **Savings Account**: Increased by €500
-   **Total Balance**: Unchanged ✅

## Migration Notes

### Important for Existing Data:

If you have ANY transactions with negative amounts, they need to be converted to positive values. The system now relies entirely on the `to_account_id` field and transaction type to determine the sign.

### Quick Check:

```sql
-- Find any negative amounts (should be ZERO after fix)
SELECT COUNT(*) FROM transactions WHERE amount < 0;
```

### Conversion (if needed):

```sql
-- Convert all negative amounts to positive
UPDATE transactions SET amount = ABS(amount) WHERE amount < 0;
```

## Technical Notes

### Why to_account_id is NULL for Incoming?

-   Prevents double-counting in balance calculations
-   Clear indicator: "This transaction has no further destination, it's the final recipient"
-   Simpler query logic: `WHERE to_account_id IS NULL` = incoming

### Parent ID Linking

-   Both transactions reference each other via `parent_id`
-   Circular reference is intentional
-   Enables finding the paired transaction from either side
-   Used for editing and deleting linked transactions

## Troubleshooting

### Problem: Balances are doubled

**Cause**: Old logic counting both `to_account_id` presence and absence
**Fix**: Applied in Account.php - only Transfer type uses this logic

### Problem: Transfers not working

**Check**:

1. Is transaction type "Transfer"?
2. Does outgoing have `to_account_id` set?
3. Does incoming have `to_account_id` as NULL?
4. Are both amounts positive?

### Problem: Cannot delete incomingTransfers relationship

**Solution**: Already removed - no longer needed with new logic
