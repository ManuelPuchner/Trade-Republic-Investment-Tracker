<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Models\Transaction;
use App\Models\TransactionType;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Transactions\TransactionResource;

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
            DeleteAction::make()
                ->after(function ($record) {
                    // If this is a transfer transaction, delete the linked transaction too
                    if ($record->parent_id) {
                        $linkedTransaction = Transaction::find($record->parent_id);
                        if ($linkedTransaction && $linkedTransaction->parent_id === $record->id) {
                            $linkedTransaction->delete();
                        }
                    }
                }),
        ];
    }

    protected function handleRecordUpdate($record, array $data): Transaction
    {
        // Check if this is a transfer transaction
        $isTransfer = false;
        
        if (isset($data['transaction_type_id'])) {
            $type = TransactionType::find($data['transaction_type_id']);
            $isTransfer = $type && $type->name === 'Transfer';
        }

        // If it's a transfer and has a destination account
        if ($isTransfer && !empty($data['to_account_id'])) {
            return $this->updateTransferTransactions($record, $data);
        }

        // Otherwise, update normally
        $record->update($data);
        return $record;
    }

    protected function updateTransferTransactions(Transaction $record, array $data): Transaction
    {
        // Find the linked transaction (if exists)
        $linkedTransaction = null;
        if ($record->parent_id) {
            $linkedTransaction = Transaction::find($record->parent_id);
            // Verify it's actually the linked transfer
            if ($linkedTransaction && $linkedTransaction->parent_id !== $record->id) {
                $linkedTransaction = null;
            }
        }

        // Update the current transaction (outgoing - the one with to_account_id)
        $record->update([
            'date' => $data['date'],
            'amount' => abs($data['amount']), // POSITIVE amount stored
            'transaction_type_id' => $data['transaction_type_id'],
            'account_id' => $data['account_id'],
            'to_account_id' => $data['to_account_id'],
            'entity_id' => $data['entity_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Update or create the linked incoming transaction
        if ($linkedTransaction) {
            // Update existing linked transaction
            $linkedTransaction->update([
                'date' => $data['date'],
                'amount' => abs($data['amount']), // POSITIVE amount stored
                'transaction_type_id' => $data['transaction_type_id'],
                'account_id' => $data['to_account_id'],
                'to_account_id' => null, // No to_account for incoming side
                'entity_id' => $data['entity_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        } else {
            // Create new linked transaction
            $linkedTransaction = Transaction::create([
                'date' => $data['date'],
                'amount' => abs($data['amount']), // POSITIVE amount stored
                'transaction_type_id' => $data['transaction_type_id'],
                'account_id' => $data['to_account_id'],
                'to_account_id' => null, // No to_account for incoming side
                'entity_id' => $data['entity_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'parent_id' => $record->id,
            ]);

            // Update the record to link back
            $record->update(['parent_id' => $linkedTransaction->id]);
        }

        return $record;
    }
}
