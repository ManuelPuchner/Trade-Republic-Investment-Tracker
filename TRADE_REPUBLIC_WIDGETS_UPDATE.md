# Trade Republic Widgets Update - October 7, 2025

## Changes Made

### ✅ 1. Widgets Now Filter by Trade Republic Account

Both widgets now only show data from the Trade Republic account:

#### Portfolio Performance Widget

**Updated to:**

-   ✅ Only calculate performance based on Trade Republic account transactions
-   ✅ Filter Käufe (purchases) by `account_id`
-   ✅ Filter Verkäufe (sales) by `account_id`
-   ✅ Show error message if Trade Republic account not found
-   ✅ Updated heading: "📈 Trade Republic Portfolio Performance"

**Before:**

```php
$kaeufe = Transaction::whereHas('type', fn($q) => $q->where('name', 'Kauf'))
    ->sum('amount');
```

**After:**

```php
$kaeufe = Transaction::where('account_id', $accountId)
    ->whereHas('type', fn($q) => $q->where('name', 'Kauf'))
    ->sum('amount');
```

#### Transaction Type Summary Widget

**Updated to:**

-   ✅ Only show transactions from Trade Republic account
-   ✅ Filter all transaction type summaries by `account_id`
-   ✅ Calculate totals only for Trade Republic
-   ✅ Show error message if Trade Republic account not found
-   ✅ Updated heading: "📊 Trade Republic Transaction Summary by Type"

**Before:**

```php
$transactionTypeSums = Transaction::select('transaction_type_id')
    ->groupBy('transaction_type_id')
    ->get();
```

**After:**

```php
$transactionTypeSums = Transaction::select('transaction_type_id')
    ->where('account_id', $tradeRepublicAccount->id)
    ->groupBy('transaction_type_id')
    ->get();
```

### ✅ 2. Widgets Now Collapsible on Dashboard

Created a custom dashboard layout with a collapsible section for Trade Republic details.

#### Dashboard Layout:

```
┌─────────────────────────────────────┐
│ Account Balances Overview           │  ← Always visible
│ (All accounts)                      │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ ▼ 📈 Trade Republic Details         │  ← Collapsible (starts collapsed)
│                                     │
│   Portfolio Performance             │
│   - Current value                   │
│   - Performance %                   │
│                                     │
│   Transaction Summary               │
│   - By transaction type             │
│   - Totals                          │
└─────────────────────────────────────┘
```

#### Features:

-   ✅ **Collapsible**: Click to expand/collapse
-   ✅ **Starts collapsed**: Hidden by default to reduce clutter
-   ✅ **Persists state**: Remembers if you expanded it (uses browser storage)
-   ✅ **Clear heading**: "📈 Trade Republic Details"
-   ✅ **Description**: Explains what's inside the section

## Files Modified

### 1. `app/Filament/Widgets/PortfolioPerformanceWidget.php`

-   Added `Account` import
-   Added `$columnSpan = 'full'` for full-width display
-   Updated `calculateGesamtInvestiert()` to accept `$accountId` parameter
-   Added Trade Republic account lookup
-   Added error handling for missing account
-   Updated heading with emoji

### 2. `app/Filament/Widgets/TransactionTypeSummaryWidget.php`

-   Added `Account` import
-   Added `$columnSpan = 'full'` for full-width display
-   Added Trade Republic account lookup
-   Filtered all queries by `account_id`
-   Added error handling for missing account
-   Updated heading with emoji

### 3. `app/Filament/Pages/Dashboard.php` (new)

-   Added `protected static string $view = 'filament.pages.dashboard'`
-   Points to custom dashboard view

### 4. `resources/views/filament/pages/dashboard.blade.php` (new)

-   Created custom dashboard layout
-   Account Balances Overview always visible at top
-   Trade Republic widgets in collapsible section
-   Uses Filament's `x-filament::section` component with:
    -   `collapsible` - Makes it expandable/collapsible
    -   `collapsed` - Starts in collapsed state
    -   `persist-collapsed` - Remembers user's preference
    -   `id="trade-republic-details"` - Unique identifier

## Benefits

### Accuracy

✅ Widgets now show **only Trade Republic data**, not mixed with other accounts
✅ Portfolio performance calculated correctly for investment account
✅ Transaction summaries reflect only investment transactions

### User Experience

✅ **Cleaner dashboard** - Trade Republic details hidden by default
✅ **Expandable on demand** - Click to see detailed investment info
✅ **Persistent state** - Stays open if you want it open
✅ **Clear labels** - Emojis and descriptions help identify content

### Organization

✅ General account overview at top (all accounts)
✅ Specific Trade Republic details in dedicated section
✅ Logical grouping of related information

## Usage

1. **View Dashboard**: Account Balances Overview visible at top
2. **Expand Trade Republic Details**: Click on "📈 Trade Republic Details" section
3. **View Performance**: See portfolio value and performance percentage
4. **View Transaction Summary**: See breakdown by transaction type
5. **Collapse Section**: Click again to hide the details

The collapsed state is saved in your browser, so it will remember your preference! 🎉
