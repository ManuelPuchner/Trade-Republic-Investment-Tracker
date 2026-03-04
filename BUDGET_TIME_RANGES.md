# Budgeting System Enhancement: Time-based Budget Ranges

## Overview

The budgeting system has been enhanced to support **time-based budget ranges**. Instead of creating a new budget every month, you can now set a budget to be active for a specific period of time or indefinitely.

## Problem Solved

Previously, if your income or expenses changed (e.g., moving out, job change, salary increase), you had to manually create and manage separate budgets for each time period. Now you can:

- Set a single budget that applies for multiple months
- Define when a budget becomes active and when it expires
- Have different budgets for different life phases without manual recreation
- Let budgets apply indefinitely (no expiration date)

## How It Works

### New Database Fields

Two new optional fields have been added to the budgets table:

1. **`valid_from`** (Date, nullable)
    - The date when this budget becomes active
    - If left blank, the budget is valid from the beginning of time (will be used for all past/current dates)
    - Example: `2026-03-01`

2. **`valid_until`** (Date, nullable)
    - The date when this budget expires
    - If left blank, the budget never expires (applies indefinitely)
    - Example: `2026-08-31` or leave empty to apply forever

### Usage Examples

#### Example 1: Current Budget (Indefinite)

- **Amount**: €1000/month
- **Period**: Monthly
- **Month**: March
- **Year**: 2026
- **Valid from**: 2026-03-01
- **Valid until**: _(leave blank)_

This budget will be active from March 1, 2026 onwards with no expiration date.

#### Example 2: Future Budget (with End Date)

- **Amount**: €2500/month
- **Period**: Monthly
- **Month**: September
- **Year**: 2026
- **Valid from**: 2026-09-01
- **Valid until**: 2026-08-31

This budget is active for your apartment moving phase (September to August of next year).

#### Example 3: Legacy Budget (Active Until Transition)

- **Amount**: €1200/month
- **Period**: Monthly
- **Month**: March
- **Year**: 2026
- **Valid from**: 2026-01-01
- **Valid until**: 2026-02-28

This budget was your previous budget that expires at the end of February.

## How the System Finds Active Budgets

When viewing the Budget Overview or calculating budget status:

1. The system checks the current date (or selected date)
2. For each budget category, it finds all budgets matching:
    - The same category
    - The selected period (monthly, quarterly, yearly)
    - The selected month (if applicable)
    - An active date range (valid_from ≤ today ≤ valid_until)
3. It uses the most recent active budget (ordered by year and valid_from date)

### Budget Selection Priority

If multiple budgets are active on a given date:

- Newer budgets (later `valid_from` date) take precedence
- Budgets for the current or most recent year are preferred

## Form Changes

When creating or editing a budget, you'll now see two additional date picker fields:

### Valid From

- Optional date picker
- Specifies when the budget becomes active
- Leave blank if this budget should apply to all past dates

### Valid Until

- Optional date picker
- Specifies when the budget expires
- Leave blank if this budget should apply indefinitely

## Migration Details

A new migration `2026_03_04_add_budget_date_ranges.php` was created that:

- Adds `valid_from` and `valid_until` columns to the budgets table
- Both columns are nullable to maintain backward compatibility
- Can be reversed if needed (uses `dropColumn` in down method)

## Code Changes

### Model Changes (Budget.php)

**New Fillable Fields:**

```php
'valid_from',
'valid_until',
```

**New Casts:**

```php
'valid_from' => 'date',
'valid_until' => 'date',
```

**New Scope - `activeOn($date)`:**

```php
$budget->activeOn(now()) // Find budgets active on a specific date
```

**New Static Method - `getActiveBudgetForCategory()`:**

```php
Budget::getActiveBudgetForCategory($categoryId, $date, $period, $month, $year)
```

### Form Changes (BudgetForm.php)

Added two new DatePicker components:

- `valid_from`: With hint "Datum, ab dem dieses Budget aktiv ist"
- `valid_until`: With hint "Datum, bis zu dem dieses Budget aktiv ist"

### Overview Changes (BudgetOverview.php)

Updated `getBudgetData()` method to:

- Create a Carbon date from selected month/year
- Use the `activeOn()` scope to filter only active budgets
- Include `where('year', '<=', $this->selectedYear)` to allow using previous year's budgets

## Backward Compatibility

All changes are fully backward compatible:

- Existing budgets without date ranges will continue to work
- Budgets with `valid_from = null` and `valid_until = null` are considered always active
- No changes required for existing budget entries

## Benefits

✅ **No more monthly budget recreation** - Set it once, apply it for months or indefinitely
✅ **Better life phase management** - Handle salary changes, moves, etc. gracefully
✅ **Future planning** - Set budgets for upcoming events (moving, new job)
✅ **Clear transition dates** - Know exactly when budgets change
✅ **Simple date-based logic** - Easy to understand and maintain

## Best Practices

1. **Set a valid_from date** when creating a new budget - makes it clear when it becomes effective
2. **Use previous month's last day for valid_until** - if transitioning to a new budget
3. **Leave valid_until blank** for budgets that should apply indefinitely
4. **Review periodically** - Check upcoming months to see if budget transitions are correctly set up
