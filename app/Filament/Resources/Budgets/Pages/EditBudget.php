<?php

namespace App\Filament\Resources\Budgets\Pages;

use App\Filament\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure required fields based on period
        if ($data['period'] !== 'yearly' && !$data['month']) {
            $data['month'] = now()->month;
        }
        
        if (!$data['year']) {
            $data['year'] = now()->year;
        }

        return $data;
    }
}
