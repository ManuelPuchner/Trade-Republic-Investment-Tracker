# CSV Export Feature

## Overview

CSV export functionality has been added to your Trade Republic Investment Tracker using the `pxlrbt/filament-excel` package.

## Installation

The following package has been installed:

-   `pxlrbt/filament-excel` (v3.1.0)
-   Dependencies: `maatwebsite/excel`, `phpoffice/phpspreadsheet`

## Features Added

### 1. Transactions Export

**Location:** `app/Filament/Resources/Transactions/Tables/TransactionsTable.php`

**Capabilities:**

-   **Export All**: Export all transactions (with current filters applied)
-   **Export Selected**: Export only selected transactions using bulk actions
-   **File Format**: CSV with German column headers
-   **Columns Included:**
    -   ID
    -   Datum (Date)
    -   Konto (Account)
    -   Betrag (Amount)
    -   Kategorie (Category)
    -   Zielkonto (Target Account)
    -   Typ (Type)
    -   Beschreibung (Description/Entity)
    -   Notizen (Notes)

### 2. Accounts Export

**Location:** `app/Filament/Resources/Accounts/Tables/AccountsTable.php`

**Capabilities:**

-   Export all accounts or selected accounts
-   **Columns Included:**
    -   Kontoname (Account Name)
    -   Bank
    -   Kontonummer (Account Number)
    -   Typ (Account Type)
    -   Anfangssaldo (Initial Balance)
    -   Aktueller Saldo (Current Balance)
    -   Trade Republic (Boolean)

### 3. Debts Export

**Location:** `app/Filament/Resources/Debts/Tables/DebtsTable.php`

**Capabilities:**

-   Export all debts or selected debts
-   **Columns Included:**
    -   Schuldner (Debtor)
    -   Betrag (Amount)
    -   Beschreibung (Description)
    -   Status (Paid/Open)
    -   Zahlungsart (Payment Method)
    -   Konto (Account)
    -   Bezahlt am (Paid At)

## How to Use

### Export All Records

1. Navigate to any of the following pages:
    - Transactions list
    - Accounts list
    - Debts list
2. Click the **"Export CSV"** button in the toolbar (top right)
3. The CSV file will be downloaded automatically with filename format: `{resource}-{date}.csv`

### Export Selected Records

1. Select one or more records using the checkboxes
2. Open the bulk actions dropdown (top right)
3. Click **"Export Ausgewählte"** (Export Selected)
4. The CSV file will be downloaded with filename format: `{resource}-selected-{date}.csv`

### With Filters Applied

-   Apply any filters (date range, type, category, etc.)
-   Use the export button
-   Only filtered records will be exported

## File Details

-   **Format:** CSV (Comma-Separated Values)
-   **Encoding:** UTF-8
-   **Filename:** Auto-generated with date stamp
-   **Headers:** German language (matching your UI)

## Technical Details

### Export Configuration

```php
ExcelExport::make()
    ->fromTable()  // Uses current table configuration
    ->withFilename(fn () => 'transactions-' . date('Y-m-d'))
    ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
    ->withColumns([...])  // Custom column definitions
```

### Benefits of This Implementation

1. **Respects Filters**: Exports only what you see on screen
2. **Respects Permissions**: Uses Filament's built-in authorization
3. **Customizable**: Easy to add/remove columns
4. **Localized**: Column headers in German
5. **Date-stamped**: Files are automatically named with current date

## Future Enhancements (Optional)

If you want to extend this in the future, you can:

1. **Add Excel Format** - Change `CSV` to `XLSX` for full Excel support
2. **Add PDF Export** - Use a different package like `barryvdh/laravel-dompdf`
3. **Custom Formatting** - Add number formatting, date formatting, etc.
4. **Multiple Sheets** - Export related data in separate sheets
5. **Scheduled Exports** - Set up automatic exports via Laravel scheduler
6. **Export Templates** - Create reusable export configurations

## Commands

```bash
# If you need to republish the config
php artisan vendor:publish --provider="pxlrbt\FilamentExcel\FilamentExcelServiceProvider"

# Clear cache after changes
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Package Documentation

-   Filament Excel: https://github.com/pxlrbt/filament-excel
-   Maatwebsite Excel: https://docs.laravel-excel.com/

## Notes

-   The export respects your table's default sorting
-   Large datasets may take a few seconds to export
-   CSV files can be opened in Excel, Google Sheets, or any spreadsheet software
-   UTF-8 encoding ensures proper display of German characters (ä, ö, ü, ß)
