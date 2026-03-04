# Budgeting System Implementation

## Overview

A complete budgeting system has been implemented for the Trade Republic Investment Tracker application. This allows users to create, manage, and monitor budgets across 392 predefined expense and income categories.

## Components Created

### Database

- **Migrations**
    - `2025_03_04_000001_create_budget_categories_table.php` - Creates the budget_categories table with Name, Slug, Category, and Subcategory fields
    - `2025_03_04_000002_create_budgets_table.php` - Creates the budgets table with amount, period (monthly/quarterly/yearly), month, year, and notes fields

### Models

- **BudgetCategory** (`app/Models/BudgetCategory.php`)
    - Stores 392 predefined budget categories
    - Organized by main category (Einnahmen/Ausgaben) and subcategory
    - Has many relationships with Budget model

- **Budget** (`app/Models/Budget.php`)
    - Represents individual budget entries
    - Supports monthly, quarterly, and yearly periods
    - Methods for calculating:
        - `getSpentAmount()` - Total amount spent in the budget period
        - `getSpentPercentage()` - Percentage of budget used
        - `getRemainingAmount()` - Remaining budget amount

### Models Enhancement

- **Transaction** - Added `scopeFilterByPeriod()` helper method for filtering transactions by time period

### Filament Resources

- **BudgetResource** (`app/Filament/Resources/Budgets/BudgetResource.php`)
    - Full CRUD interface for managing budgets
    - Navigation group: "Finanz Management"
    - Icon: Banknotes

#### Pages

- **ListBudgets** - Table view of all budgets with filters
- **CreateBudget** - Form to create new budgets
- **EditBudget** - Form to edit existing budgets
- **ViewBudget** - View budget details

#### Schemas

- **BudgetForm** - Form fields for budget creation/editing
    - Category select with hierarchical grouping
    - Amount (numeric, in EUR)
    - Period selector (monthly/quarterly/yearly)
    - Optional month and year fields
    - Notes textarea

#### Tables

- **BudgetsTable** - Table columns and filters
    - Shows category, subcategory, amount, period, month, year
    - Filters by period and main category
    - Sortable columns
    - Toggle hidden columns

### Pages

- **BudgetOverview** (`app/Filament/Pages/BudgetOverview.php`)
    - Custom analytics dashboard
    - Located at: `/admin/budget-overview`
    - Navigation group: "Finanz Management"
    - Features:
        - Filter by period (monthly/quarterly/yearly), month, and year
        - Overall statistics:
            - Total budget
            - Total spent
            - Total remaining
            - Overall percentage used
        - Breakdown by main category with:
            - Budget vs. spent comparison
            - Percentage and progress bars
            - Color-coded status (green/yellow/red)
        - Detailed budget table with:
            - Category and subcategory
            - Budget amount
            - Amount spent
            - Remaining amount
            - Percentage used with status badges

### Views

- **budget-overview.blade.php** - Analytics dashboard view
    - Responsive grid layout
    - Interactive filters
    - Progress bars and status indicators
    - Summary statistics cards

## Budget Categories

All 392 budget categories have been seeded, organized into:

### Income (Einnahmen)

- Arbeitseinkommen (Employment Income) - 13 categories
- Staatliche Leistungen & Beihilfen (Government Benefits) - 11 categories
- Kapitalerträge & Vermögen (Capital Income & Assets) - 8 categories
- Sonstige Einnahmen (Other Income) - 10 categories

### Expenses (Ausgaben)

- Wohnen - Grundkosten (Housing - Basics) - 9 categories
- Wohnen - Energie & Versorgung (Housing - Energy & Utilities) - 10 categories
- Wohnen - Kommunikation & Medien (Housing - Communication & Media) - 5 categories
- Wohnen - Haushalt & Einrichtung (Housing - Household & Furnishings) - 13 categories
- Mobilität - Auto (Mobility - Car) - 21 categories
- Mobilität - Öffentliche Verkehrsmittel (Mobility - Public Transit) - 5 categories
- Mobilität - Sonstige Mobilität (Mobility - Other) - 8 categories
- Versicherungen - Personenversicherungen (Insurance - Personal) - 8 categories
- Versicherungen - Sachversicherungen (Insurance - Property) - 7 categories
- Ernährung - Lebensmittel (Food - Groceries) - 10 categories
- Ernährung - Außer-Haus-Verpflegung (Food - Dining Out) - 12 categories
- Kleidung - Bekleidung (Clothing - Apparel) - 17 categories
- Kleidung - Körperpflege & Kosmetik (Clothing - Personal Care) - 18 categories
- Gesundheit - Medizinische Versorgung (Health - Medical) - 13 categories
- Gesundheit - Gesundheitsvorsorge (Health - Prevention) - 8 categories
- Bildung - Ausbildung (Education - Training) - 10 categories
- Bildung - Weiterbildung (Education - Continuing) - 9 categories
- Kinder - Grundbedürfnisse (Children - Basics) - 8 categories
- Kinder - Freizeit & Entwicklung (Children - Leisure & Development) - 5 categories
- Freizeit - Medien & Streaming (Leisure - Media & Streaming) - 13 categories
- Freizeit - Hobbies - 18 categories
- Freizeit - Kultur & Ausgehen (Leisure - Culture & Going Out) - 9 categories
- Freizeit - Urlaub & Reisen (Leisure - Vacation & Travel) - 11 categories
- Technologie - Hardware - 11 categories
- Technologie - Software & Services - 9 categories
- Technologie - Reparatur & Wartung (Technologie - Repair & Maintenance) - 3 categories
- Haustiere (Pets) - 14 categories
- Soziales & Geschenke (Social & Gifts) - 13 categories
- Finanzen - Sparen & Investieren (Finance - Saving & Investing) - 9 categories
- Finanzen - Kredite & Schulden (Finance - Loans & Debt) - 6 categories
- Finanzen - Bankgebühren (Finance - Bank Fees) - 4 categories
- Finanzen - Vorsorge (Finance - Provisions) - 3 categories
- Rechtliches & Behörden (Legal & Government) - 13 categories
- Sonstiges - Unterhaltszahlungen (Other - Alimony) - 2 categories
- Sonstiges - Persönliche Dienstleistungen (Other - Personal Services) - 8 categories
- Sonstiges - Unvorhergesehenes (Other - Unforeseen) - 3 categories
- Sonstiges - Laster & Genussmittel (Other - Vices & Indulgences) - 4 categories

## How to Use

### Create a Budget

1. Navigate to **Budgets** in the Filament admin panel
2. Click **Create**
3. Select a category from the hierarchical dropdown
4. Enter the budget amount in EUR
5. Select the period (monthly, quarterly, or yearly)
6. Enter the month (1-12) for monthly/quarterly budgets
7. Select the year
8. Add optional notes
9. Click **Create**

### View Budget Overview

1. Navigate to **Budget-Übersicht** in the Finanz Management menu
2. Select filters:
    - Choose period type (monthly, quarterly, yearly)
    - For non-yearly periods, select the specific month
    - Select the year
3. View:
    - Overall statistics (total budget, spent, remaining, percentage)
    - Breakdown by main category with visual progress bars
    - Detailed table of all budgets with spending analysis

### Features

- **Real-time Spending Analysis**: Spent amounts are calculated from transactions automatically
- **Visual Status Indicators**:
    - Green: Under 80% utilization
    - Yellow: 80-100% utilization
    - Red: Over 100% utilization
- **Period-based Organization**: Budgets can be set for specific months or years
- **Hierarchical Categories**: Categories are organized by main category and subcategory for easy navigation

## Technical Details

### Database

- PostgreSQL with foreign key constraints
- Unique constraint on (budget_category_id, period, month, year) combination

### Spending Calculation

- Transactions are matched by category name using `whereHas('category', ...)`
- Only "Ausgabe" (expense) transaction types are included
- Period filtering supports monthly, quarterly, and yearly scopes

### Filament Integration

- Registered in `AdminPanelProvider`
- Discovered automatically via `discoverResources()` method
- Pages discovered via `discoverPages()` method
- Navigation group: "Finanz Management"

## Future Enhancements

- Budget alerts and notifications
- Budget templates for recurring patterns
- Multi-currency support
- Historical budget trends and comparisons
- Budget reports and exports
