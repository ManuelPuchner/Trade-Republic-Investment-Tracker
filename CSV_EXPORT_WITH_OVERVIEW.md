# CSV Export with Overview Statistics

## 🎉 New Feature: Statistical Overview Exports!

In addition to detailed data exports, you can now export **comprehensive statistical overviews** for Transactions, Accounts, and Debts!

## Export Options Available

### For Each Resource (Transactions, Accounts, Debts):

1. **"Export CSV"** - Detailed data export

    - Exports all records with all columns
    - Respects current filters
    - Filename: `{resource}-{date}.csv`

2. **"Export Übersicht"** ⭐ NEW - Statistical overview

    - Green button with chart icon
    - Calculates comprehensive statistics
    - Respects current filters
    - Filename: `{resource}-overview-{date}.csv`

3. **"Export Ausgewählte"** - Selected records only
    - Available in bulk actions dropdown
    - Exports only checked records
    - Filename: `{resource}-selected-{date}.csv`

## What Statistics Are Included?

### Transactions Overview

**General Statistics:**

-   Total number of transactions
-   Total amount
-   Total income (Einzahlung, Verkauf, Zinsen, Dividenden, Save Back)
-   Total expenses (Kauf, Ausgabe, Saveback Steuer)
-   Net balance (income - expenses)
-   Average, highest, and lowest transaction amounts

**Breakdown by Transaction Type:**

-   Count, sum, and average for each type (Kauf, Verkauf, etc.)

**Breakdown by Account:**

-   Count and total sum per account

**Breakdown by Category:**

-   Count and total sum per category (if categorized)

### Accounts Overview

**General Statistics:**

-   Total number of accounts
-   Total balance across all accounts
-   Average balance per account
-   Highest and lowest account balances

**Breakdown by Account Type:**

-   Count, total balance, and average per type (Girokonto, Sparkonto, etc.)

**Breakdown by Bank:**

-   Count and total balance per bank

**Trade Republic vs Others:**

-   Separate counts and balances for Trade Republic vs other accounts

### Debts Overview

**General Statistics:**

-   Total number of debts
-   Total amount owed
-   Average, highest, and lowest debt amounts

**Status Analysis:**

-   Paid vs unpaid counts and amounts
-   Payment rate percentage

**Breakdown by Debtor:**

-   Count, total amount, paid count, and unpaid count per debtor

**Breakdown by Payment Method:**

-   Count and amount per payment method (Bargeld, Überweisung, etc.)

**Breakdown by Account:**

-   Count and amount per linked account

## How to Use

### Step 1: Optional Filtering

Apply any filters you want before exporting:

-   Date ranges
-   Transaction types
-   Account selection
-   Categories
-   Payment status

**Important:** Overview statistics are calculated based on your filtered data!

### Step 2: Choose Export Type

**For Detailed Data:**

-   Click **"Export CSV"** button
-   Get all individual records with all columns

**For Statistics:**

-   Click **"Export Übersicht"** button (green, with chart icon 📊)
-   Get a 2-column CSV with statistics labels and values

**For Selected Only:**

-   Check the records you want
-   Use bulk actions → **"Export Ausgewählte"**

### Step 3: Open in Spreadsheet Software

-   Excel
-   Google Sheets
-   LibreOffice Calc
-   Numbers (Mac)

## Example: Transactions Overview Output

```csv
Übersicht,Wert
=== ALLGEMEINE ÜBERSICHT ===,
Gesamtanzahl Transaktionen,204
Gesamtbetrag,"€1.234,56"
Einnahmen (gesamt),"€5.678,90"
Ausgaben (gesamt),"€4.444,34"
Netto Bilanz,"€1.234,56"
Durchschnittsbetrag,"€6,05"
Höchster Betrag,"€500,00"
Niedrigster Betrag,"€0,01"
,
=== NACH TRANSAKTIONSTYP ===,
Kauf - Anzahl,45
Kauf - Summe,"€2.345,67"
Kauf - Durchschnitt,"€52,13"
Verkauf - Anzahl,12
Verkauf - Summe,"€890,12"
...
```

## Use Cases

### Financial Analysis

-   Track your monthly income vs expenses
-   Analyze spending by category
-   Monitor account balances by type
-   See which debts are paid/unpaid

### Reporting

-   Generate month-end reports
-   Compare performance across accounts
-   Analyze investment patterns
-   Track debt payment progress

### Tax Preparation

-   Export transaction summaries
-   Calculate total income from investments
-   Track business expenses by category
-   Document paid debts

### Budgeting

-   See average spending per category
-   Compare actual vs planned expenses
-   Monitor account balance trends
-   Track payment methods usage

## Technical Implementation

### Export Classes Created:

-   `app/Exports/TransactionsOverviewExport.php`
-   `app/Exports/AccountsOverviewExport.php`
-   `app/Exports/DebtsOverviewExport.php`

These classes use Laravel Excel's `FromCollection`, `WithHeadings`, and `WithMapping` interfaces to generate formatted overview data.

### How It Works:

1. Gets filtered data from the table
2. Loads relationships (type, account, category, debtor)
3. Performs aggregations (sum, avg, count, groupBy)
4. Formats numbers as EUR currency
5. Outputs as 2-column CSV

## Tips & Tricks

### Combine Filters for Specific Insights

-   Filter by date range + transaction type to see monthly income
-   Filter by account + category to analyze account-specific spending
-   Filter by debtor to see total owed per person

### Use Both Export Types

-   Export detailed data for record-keeping
-   Export overview for quick analysis and reports
-   Compare different time periods by changing filters

### Import into Analysis Tools

-   Excel: Use pivot tables on detailed exports
-   Google Sheets: Create charts from overview data
-   PowerBI/Tableau: Import for advanced visualization

## File Format

-   **Format:** CSV (Comma-Separated Values)
-   **Encoding:** UTF-8 (proper German characters: ä, ö, ü, ß)
-   **Separator:** Comma (,)
-   **Decimal:** Comma (,) in number format
-   **Thousands:** Period (.) in number format
-   **Currency:** € symbol with proper formatting

## Benefits

✅ **Quick Insights** - No need to manually calculate totals  
✅ **Filtered Analysis** - Statistics based on your current view  
✅ **Professional Format** - Ready for presentations and reports  
✅ **Comprehensive** - Multiple breakdowns in one export  
✅ **Easy to Read** - Clear labels and formatted currency  
✅ **Time Saver** - Get overview in seconds, not minutes

## Support

If you need to customize the statistics or add new calculations:

1. Edit the respective export class in `app/Exports/`
2. Modify the `collection()` method to add/remove statistics
3. Update the `map()` method if you need different formatting

Enjoy your new statistical overview exports! 📊
