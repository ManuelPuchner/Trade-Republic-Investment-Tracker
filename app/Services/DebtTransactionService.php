<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Entity;

class DebtTransactionService
{
    /**
     * Erstellt automatisch eine Trade Republic Transaktion für eine bezahlte Schuld
     */
    public function createTradeRepublicTransaction(Debt $debt): ?Transaction
    {
        // Überprüfe ob es sich um eine Trade Republic Zahlung handelt
        if ($debt->payment_method !== Debt::PAYMENT_TRADE_REPUBLIC || !$debt->is_paid) {
            return null;
        }

        // Überprüfe ob bereits eine Transaktion verknüpft ist
        if ($debt->transaction_id) {
            return Transaction::find($debt->transaction_id);
        }

        // Suche oder erstelle TransactionType für Einzahlungen
        $depositType = TransactionType::firstOrCreate(
            ['name' => 'Einzahlung von Schulden'],
            ['color' => '#10B981'] // Grün für positive Transaktionen
        );

        // Suche oder erstelle Entity für Schulden-Rückzahlungen
        $debtEntity = Entity::firstOrCreate(
            ['name' => 'Schulden-Rückzahlung'],
            ['name' => 'Schulden-Rückzahlung']
        );

        // Erstelle die Transaktion
        $transaction = Transaction::create([
            'date' => $debt->paid_at ?? now(),
            'amount' => $debt->amount,
            'transaction_type_id' => $depositType->id,
            'entity_id' => $debtEntity->id,
            'parent_id' => null,
        ]);

        // Verknüpfe die Transaktion mit der Schuld
        $debt->update(['transaction_id' => $transaction->id]);

        return $transaction;
    }

    /**
     * Entfernt die Verknüpfung zwischen Schuld und Transaktion
     */
    public function unlinkTransaction(Debt $debt): void
    {
        $debt->update(['transaction_id' => null]);
    }
}