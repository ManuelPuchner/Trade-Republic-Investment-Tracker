<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Check if there's a parent_id parameter in the URL
        if (request()->has('parent_id')) {
            $parentId = request()->get('parent_id');
            
            // Set the table filter programmatically
            $this->tableFilters = [
                'parent_id' => ['value' => $parentId],
            ];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
