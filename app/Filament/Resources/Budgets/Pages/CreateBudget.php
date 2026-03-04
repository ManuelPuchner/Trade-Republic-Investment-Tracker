<?php

namespace App\Filament\Resources\Budgets\Pages;

use App\Filament\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure required fields based on period
        if ($data['period'] !== 'yearly' && ! $data['month']) {
            $data['month'] = now()->month;
        }

        if (! $data['year']) {
            $data['year'] = now()->year;
        }

        return $data;
    }
}
