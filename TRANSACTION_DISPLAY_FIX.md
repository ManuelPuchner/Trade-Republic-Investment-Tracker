# Transaction Display Fix - October 7, 2025

## Changes Made

### ✅ 1. Corrected Amount Prefix Logic

The transaction amount display now correctly shows **+** or **-** based on the **transaction type**, not just the amount value.

#### Prefix Rules by Transaction Type:

**Subtracts from Account (Red, - prefix, ↓ icon):**

-   ❌ **Kauf** (Purchase)
-   ❌ **Ausgabe** (Expense)
-   ❌ **Saveback Steuer** (Saveback Tax)
-   ❌ **Transfers** (when to_account_id is set)

**Adds to Account (Green, + prefix, ↑ icon):**

-   ✅ **Einzahlung** (Deposit)
-   ✅ **Verkauf** (Sale)
-   ✅ **Zinsen** (Interest)
-   ✅ **Dividenden** (Dividends)
-   ✅ **Save Back** (Saveback)

#### Visual Examples:

```
Transaction Type: Kauf (Purchase)
Amount: 500.00
Display: - €500,00 (in red with ↓ icon)

Transaction Type: Dividenden (Dividends)
Amount: 25.50
Display: + €25,50 (in green with ↑ icon)

Transaction Type: Verkauf (Sale)
Amount: 1200.00
Display: + €1.200,00 (in green with ↑ icon)
```

### ✅ 2. Entity Column Always Visible & Renamed

**Changes:**

-   Column renamed from **"Wertpapier"** (Security) → **"Beschreibung"** (Description)
-   Column is now **always visible** (no longer hidden by default)
-   More appropriate since the table contains both securities AND other expenses

**Form Label Updated:**

-   Changed from "Wertpapier" → **"Beschreibung / Wertpapier"**
-   Helper text: "Wertpapier oder Beschreibung der Transaktion"
-   Makes it clear this field can be used for any transaction description

## Implementation Details

### Amount Display Logic

The system now uses a `match` expression based on the transaction type name:

```php
$subtractsFromAccount = match($typeName) {
    'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
    'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
    default => $record->amount < 0 || $record->to_account_id !== null
};
```

### Transaction Type IDs (for reference)

-   ID: 10 - Einzahlung
-   ID: 11 - Kauf
-   ID: 12 - Verkauf
-   ID: 13 - Zinsen
-   ID: 14 - Dividenden
-   ID: 15 - Ausgabe
-   ID: 16 - Save Back
-   ID: 17 - Saveback Steuer

## Files Modified

1. **app/Filament/Resources/Transactions/Tables/TransactionsTable.php**

    - Updated amount column formatting logic to use transaction type
    - Renamed entity column from "Wertpapier" to "Beschreibung"
    - Made entity column always visible (removed `toggleable(isToggledHiddenByDefault: true)`)

2. **app/Filament/Resources/Transactions/Schemas/TransactionForm.php**
    - Updated entity_id field label to "Beschreibung / Wertpapier"
    - Changed icon from chart-bar to document-text
    - Updated helper text and placeholder

## Result

✅ Transaction amounts now display correctly based on their type
✅ Purchase (Kauf) shows as red with minus (- €500,00)
✅ Sale (Verkauf) shows as green with plus (+ €1.200,00)  
✅ Dividends show as green with plus (+ €25,50)
✅ Expenses (Ausgabe) show as red with minus (- €50,00)
✅ Entity/Description column is always visible in the table
✅ Column name better reflects mixed usage (securities + expenses)

All changes are live and working! 🎉
