<?php

namespace App\Filament\Resources\BudgetCategories\Pages;

use Filament\Schemas\Schema;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BudgetCategories\BudgetCategoryResource;
use App\Filament\Resources\BudgetCategories\Schemas\BudgetCategoryForm;

class CreateBudgetCategory extends CreateRecord
{
    protected static string $resource = BudgetCategoryResource::class;

    protected function getFormSchema(): Schema
    {
        return BudgetCategoryForm::configure(Schema::make());
    }
}
