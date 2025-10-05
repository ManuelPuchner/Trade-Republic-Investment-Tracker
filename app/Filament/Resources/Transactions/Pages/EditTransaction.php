<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();
        
        if (!$record->relationLoaded('entity')) {
            $record->load('entity');
        }

        $entityName = $record->entity?->name ?? 'No Entity';
        $date = $record->date ? \Carbon\Carbon::parse($record->date)->format('M j, Y') : 'No Date';

        return "Edit {$entityName} - {$date}";
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
