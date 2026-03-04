<?php

namespace App\Filament\Resources\BudgetCategories;

use BackedEnum;
use App\Models\BudgetCategory;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\BudgetCategories\Pages\ListBudgetCategories;
use App\Filament\Resources\BudgetCategories\Pages\CreateBudgetCategory;
use App\Filament\Resources\BudgetCategories\Pages\EditBudgetCategory;
use App\Filament\Resources\BudgetCategories\Schemas\BudgetCategoryForm;
use App\Filament\Resources\BudgetCategories\Tables\BudgetCategoriesTable;
use UnitEnum;

class BudgetCategoryResource extends Resource
{
    protected static ?string $model = BudgetCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Kategorien';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Budget';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BudgetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgetCategories::route('/'),
            'create' => CreateBudgetCategory::route('/create'),
            'edit' => EditBudgetCategory::route('/{record}/edit'),
        ];
    }
}
