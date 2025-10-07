# PDF Export Feature

## Overview

The system now supports exporting statistical overview data as **PDF documents** in addition to CSV format. The export options are accessible via a dropdown button that reveals both CSV and PDF format choices.

## Features

### 1. **Dropdown Button Interface**

-   Single "Export Übersicht" button with chart icon
-   Click to reveal dropdown with two options:
    -   **Export als CSV** - Download overview as CSV file
    -   **Export als PDF** - Download overview as formatted PDF document

### 2. **Available Export Locations**

#### Transactions Table

-   Path: Transactions → Actions toolbar
-   PDF includes:
    -   General overview (total transactions, total amount)
    -   Kassenbestand calculation (matching widget formula)
    -   Detailed income breakdown (Einzahlungen, Verkäufe, Zinsen, Dividenden)
    -   Detailed expenses breakdown (Käufe, Ausgaben, Saveback Steuer)
    -   Additional statistics (average, max, min amounts)
    -   Breakdown by transaction type
    -   Breakdown by account (Konto)
    -   Breakdown by category

#### Accounts Table

-   Path: Accounts → Actions toolbar
-   PDF includes:
    -   General overview (total accounts, total balance)
    -   Balance statistics (average, max, min)
    -   Breakdown by account type
    -   Breakdown by bank

#### Debts Table

-   Path: Debts → Actions toolbar
-   PDF includes:
    -   General overview (total debts, total amount)
    -   Debt statistics (average, max, min)
    -   Breakdown by debtor
    -   Breakdown by debt type

## Technical Implementation

### 1. **Package Used**

```bash
barryvdh/laravel-dompdf v3.1.1
```

### 2. **PDF View Templates**

All PDF templates are located in `resources/views/exports/`:

-   `transactions-overview-pdf.blade.php` - Transactions overview
-   `accounts-overview-pdf.blade.php` - Accounts overview
-   `debts-overview-pdf.blade.php` - Debts overview

### 3. **PDF Styling**

-   **Font**: DejaVu Sans (supports German characters: ä, ö, ü, ß, €)
-   **Layout**: Professional table-based design
-   **Colors**:
    -   Blue theme for transactions (matching Filament primary color)
    -   Green for positive amounts
    -   Red for negative amounts/debts
-   **Formatting**: German number format (comma as decimal separator, period as thousands separator)
-   **Footer**: Automatic timestamp and application name

### 4. **Dropdown Implementation**

Each table file has the export button structured as follows:

```php
Action::make('exportOverview')
    ->label('Export Übersicht')
    ->icon('heroicon-o-chart-bar')
    ->color('success')
    ->dropdown()  // Enable dropdown
    ->dropdownActions([
        Action::make('exportOverviewCSV')
            ->label('Export als CSV')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->action(function ($livewire) {
                // CSV export logic
            }),
        Action::make('exportOverviewPDF')
            ->label('Export als PDF')
            ->icon('heroicon-o-document')
            ->color('warning')
            ->action(function ($livewire) {
                // PDF generation logic
            }),
    ]),
```

## Usage

### For End Users

1. Navigate to Transactions, Accounts, or Debts list
2. Apply any filters you want (the export will respect active filters)
3. Click the **"Export Übersicht"** button in the toolbar
4. Choose your format:
    - **CSV** - Opens/downloads CSV file (Excel compatible)
    - **PDF** - Downloads formatted PDF document

### File Naming Convention

-   CSV: `{resource}-overview-YYYY-MM-DD.csv`
-   PDF: `{resource}-overview-YYYY-MM-DD.pdf`

Examples:

-   `transactions-overview-2024-01-15.csv`
-   `accounts-overview-2024-01-15.pdf`
-   `debts-overview-2024-01-15.pdf`

## PDF Content Details

### Transactions Overview PDF

#### Section 1: General Overview

-   Total number of transactions
-   Total amount sum

#### Section 2: Kassenbestand (Cash Balance)

-   **Kassenbestand calculation**: Income - Expenses
-   **Income breakdown**:
    -   Einzahlungen (Deposits)
    -   Verkäufe (Sales)
    -   Zinsen (Interest)
    -   Dividenden (Dividends)
-   **Expenses breakdown**:
    -   Käufe (Purchases)
    -   Ausgaben (Expenses)
    -   Saveback Steuer (Saveback Tax)

#### Section 3: Further Statistics

-   Average amount
-   Highest amount
-   Lowest amount

#### Section 4: By Transaction Type

Table showing count, sum, and average for each transaction type

#### Section 5: By Account

Table showing count and sum for each account

#### Section 6: By Category

Table showing count and sum for each category

### Accounts Overview PDF

#### Section 1: General Overview

-   Total number of accounts
-   Total balance (highlighted, color-coded)

#### Section 2: Balance Statistics

-   Average balance
-   Highest balance
-   Lowest balance

#### Section 3: By Account Type

Table showing count, total balance, and average for each type

#### Section 4: By Bank

Table showing count and total balance for each bank

### Debts Overview PDF

#### Section 1: General Overview

-   Total number of debts
-   Total debt amount (highlighted in red)

#### Section 2: Statistics

-   Average debt amount
-   Highest debt
-   Lowest debt

#### Section 3: By Debtor

Table showing count and total amount for each debtor

#### Section 4: By Type

Table showing count, sum, and average for each debt type

## Code Locations

### Modified Files

1. `app/Filament/Resources/Transactions/Tables/TransactionsTable.php`

    - Updated `exportOverview` action to use dropdown
    - Added PDF generation logic for transactions

2. `app/Filament/Resources/Accounts/Tables/AccountsTable.php`

    - Updated `exportOverview` action to use dropdown
    - Added PDF generation logic for accounts

3. `app/Filament/Resources/Debts/Tables/DebtsTable.php`
    - Updated `exportOverview` action to use dropdown
    - Added PDF generation logic for debts

### Created Files

1. `resources/views/exports/transactions-overview-pdf.blade.php`

    - HTML/CSS template for transactions overview PDF

2. `resources/views/exports/accounts-overview-pdf.blade.php`

    - HTML/CSS template for accounts overview PDF

3. `resources/views/exports/debts-overview-pdf.blade.php`
    - HTML/CSS template for debts overview PDF

## Benefits

### 1. **User Experience**

-   Clean dropdown interface - one button for multiple formats
-   Consistent button location across all tables
-   Professional PDF formatting for presentations/reports

### 2. **Format Selection**

-   **CSV**: For data analysis, Excel import, further processing
-   **PDF**: For viewing, printing, sharing with stakeholders

### 3. **Data Accuracy**

-   Both formats use the same filtered data
-   Kassenbestand calculation matches widget exactly
-   German formatting throughout (€, commas, periods)

### 4. **Flexibility**

-   Respects table filters
-   Works with search and date range filters
-   Includes all relevant statistics

## Future Enhancements (Possible)

1. **PDF Customization**

    - Company logo support
    - Custom header/footer text
    - Color scheme preferences

2. **Additional Formats**

    - Excel (.xlsx) with formatting
    - JSON for API integration

3. **Scheduled Exports**

    - Automatic daily/weekly/monthly PDF generation
    - Email delivery of reports

4. **Chart Integration**
    - Include visual charts in PDF
    - Match widget charts

## Troubleshooting

### PDF Generation Issues

**Issue**: German characters (ä, ö, ü, ß, €) not displaying correctly

-   **Solution**: The PDF templates use `DejaVu Sans` font which supports UTF-8 characters
-   Ensure `meta charset="utf-8"` is in the HTML head

**Issue**: PDF layout breaks

-   **Solution**: Check page-break styles, ensure tables fit within page width

**Issue**: Large datasets cause timeout

-   **Solution**: Apply filters to reduce dataset size before exporting

### Dropdown Not Showing

**Issue**: Dropdown doesn't appear when clicking button

-   **Verify**: `->dropdown()` method is called on the Action
-   **Verify**: `->dropdownActions([...])` contains sub-actions

## Summary

The PDF export feature provides a professional way to export statistical overview data with:

-   ✅ Dropdown button for format selection (CSV/PDF)
-   ✅ Formatted PDF documents with tables and statistics
-   ✅ German language and number formatting
-   ✅ Consistent across Transactions, Accounts, and Debts
-   ✅ Filter-aware exports
-   ✅ Automatic date-based file naming
