# Entity Type Feature

## Overview

Added a `type` field to the Entity model to classify entities as ETF, Company, or Person.

## Changes Made

### 1. Database Migration

-   **File**: `database/migrations/2025_10_07_183417_add_type_to_entities_table.php`
-   Added `type` column as ENUM with values: 'ETF', 'Company', 'Person'
-   Default value: 'Company'
-   Migration has been executed successfully

### 2. Entity Model

-   **File**: `app/Models/Entity.php`
-   Added `type` to `$fillable` array
-   Added `$casts` for type field
-   Created `getTypes()` static method returning available types with German labels:
    -   ETF → ETF
    -   Company → Unternehmen
    -   Person → Person

### 3. Entity Form

-   **File**: `app/Filament/Resources/Entities/Schemas/EntityForm.php`
-   Added Select field for entity type
-   Default value: 'Company'
-   Uses German labels from `Entity::getTypes()`
-   Added icon: `heroicon-o-tag`
-   Non-native select for better UX

### 4. Entities Table

-   **File**: `app/Filament/Resources/Entities/Tables/EntitiesTable.php`
-   Added `type` column with:
    -   Badge display
    -   German labels
    -   Color coding:
        -   ETF → Info (blue)
        -   Company → Success (green)
        -   Person → Warning (yellow)
    -   Icons:
        -   ETF → `heroicon-o-chart-bar`
        -   Company → `heroicon-o-building-office`
        -   Person → `heroicon-o-user`
-   Added filter for entity type
-   Enhanced other columns with icons and labels
-   Set default sort to name (ascending)

### 5. List Entities Page

-   **File**: `app/Filament/Resources/Entities/Pages/ListEntities.php`
-   Added tabs for filtering by entity type:
    -   **Alle** (All) - Shows all entities
    -   **ETF** - Shows only ETF entities
    -   **Unternehmen** (Companies) - Shows only Company entities
    -   **Personen** (Persons) - Shows only Person entities
-   Each tab displays badge count with appropriate color

## Features

1. **Type Classification**: Entities can now be classified as ETF, Company, or Person
2. **Visual Indicators**: Different colors and icons for each type
3. **Filtering**: Filter entities by type using the dropdown filter or tabs
4. **German Localization**: All labels are in German for consistency with the rest of the application
5. **Default Value**: New entities default to "Company" type
6. **Required Field**: Type selection is mandatory when creating/editing entities

## Database Schema

```sql
ALTER TABLE `entities` ADD `type` ENUM('ETF', 'Company', 'Person') NOT NULL DEFAULT 'Company' AFTER `name`;
```

## Usage

When creating or editing an entity, you must select the appropriate type:

-   **ETF**: For exchange-traded funds
-   **Unternehmen**: For company entities
-   **Person**: For individual persons

The type will be displayed in the entities list with appropriate visual styling and can be used for filtering and organization.
