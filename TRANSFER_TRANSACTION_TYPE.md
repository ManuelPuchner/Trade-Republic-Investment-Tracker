# Transfer Transaction Type Update

## Overview

Added a new "Transfer" transaction type and updated the Transaction Form to properly handle transfers between accounts.

## Changes Made

### 1. New Transaction Type Created

-   **Name**: Transfer
-   **ID**: 18
-   **Color**: info (blue badge)
-   Created in database via Tinker

### 2. Transaction Form Updates

**File**: `app/Filament/Resources/Transactions/Schemas/TransactionForm.php`

#### Changes to "to_account_id" Field (Step 4):

-   **Made REQUIRED** when "Transfer" type is selected
-   **Visible** for both "Transfer" and "Ausgabe" types
-   Now properly distinguishes between:
    -   **Transfer type**: Dedicated transfers between accounts (requires to_account_id)
    -   **Ausgabe type**: Can be either a regular expense OR a transfer

#### Changes to "entity_id" Field (Step 5):

-   **Label** changes dynamically:
    -   For "Transfer": Shows "Beschreibung (optional)"
    -   Other types maintain their specific labels
-   **Helper text** added for Transfer type: "Optionale Beschreibung für diese Überweisung"
-   **NOT REQUIRED** for Transfer type (description is optional)
-   **VISIBLE** for Transfer type (allows optional description)

#### Changes to "category_id" Field (Step 6):

-   **NOT VISIBLE** for Transfer type
-   Categories are not relevant for account-to-account transfers
-   Remains visible for "Ausgabe" and "Einzahlung" types

## Usage

### Creating a Transfer Transaction

1. **Select "Transfer"** as transaction type
2. **Select source account** (Von Konto) - REQUIRED
3. **Enter amount** - REQUIRED
4. **Select destination account** (Auf Konto) - REQUIRED
5. **Optional**: Add a description via entity field
6. **Optional**: Add notes

### Form Behavior

-   When "Transfer" is selected:
    -   ✅ to_account_id field appears and is REQUIRED
    -   ✅ entity_id field appears but is OPTIONAL
    -   ❌ category_id field is hidden (not relevant for transfers)
-   When "Ausgabe" is selected:
    -   ✅ to_account_id field appears but is OPTIONAL
    -   ✅ entity_id field appears (REQUIRED if no to_account_id)
    -   ✅ category_id field appears
    -   Allows for both regular expenses and transfers

## Benefits

1. **Clear Distinction**: Dedicated "Transfer" type makes it clear when moving money between accounts
2. **Required Fields**: Ensures all transfer transactions have both source and destination accounts
3. **Simplified UI**: Hides irrelevant fields (categories) for transfers
4. **Flexibility**: Entity field allows optional description for transfers
5. **Backward Compatible**: Existing "Ausgabe" type still supports transfers for legacy data

## Database Impact

-   No migration needed (uses existing `to_account_id` column)
-   New transaction type added to `transaction_types` table
-   All existing transactions remain unchanged

## Future Considerations

-   Consider migrating existing "Ausgabe" transfers to use the new "Transfer" type
-   Update reports/widgets to handle "Transfer" type appropriately
-   Consider excluding transfers from expense summaries/analytics
