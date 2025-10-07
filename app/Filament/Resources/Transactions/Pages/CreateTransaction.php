<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Check if this is a transfer transaction
        $isTransfer = false;
        
        if (isset($data['transaction_type_id'])) {
            $type = TransactionType::find($data['transaction_type_id']);
            $isTransfer = $type && $type->name === 'Transfer';
        }

        // If it's a transfer and has a destination account, create two transactions
        if ($isTransfer && !empty($data['to_account_id'])) {
            return $this->createTransferTransactions($data);
        }

        // Otherwise, create a normal transaction
        return static::getModel()::create($data);
    }

    protected function createTransferTransactions(array $data): Model
    {
        // Create the outgoing transaction (from source account)
        $outgoingTransaction = Transaction::create([
            'date' => $data['date'],
            'amount' => abs($data['amount']), // POSITIVE amount stored
            'transaction_type_id' => $data['transaction_type_id'],
            'account_id' => $data['account_id'],
            'to_account_id' => $data['to_account_id'],
            'entity_id' => $data['entity_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'parent_id' => null,
        ]);

        // Create the incoming transaction (to destination account)
        $incomingTransaction = Transaction::create([
            'date' => $data['date'],
            'amount' => abs($data['amount']), // POSITIVE amount stored
            'transaction_type_id' => $data['transaction_type_id'],
            'account_id' => $data['to_account_id'],
            'to_account_id' => null, // No to_account for incoming side
            'entity_id' => $data['entity_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'parent_id' => $outgoingTransaction->id, // Link to outgoing transaction
        ]);

        // Update the outgoing transaction to link to the incoming one
        $outgoingTransaction->update([
            'parent_id' => $incomingTransaction->id,
        ]);

        // Return the outgoing transaction as the "main" one
        return $outgoingTransaction;
    }
}
