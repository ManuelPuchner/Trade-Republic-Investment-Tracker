<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'bank_name',
        'account_type',
        'is_trade_republic',
        'initial_balance',
        'initial_balance_date',
    ];

    protected $casts = [
        'is_trade_republic' => 'boolean',
        'initial_balance' => 'decimal:2',
        'initial_balance_date' => 'datetime',
    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomingTransfers()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * Calculate current balance based on transactions
     * For Trade Republic: Uses KassenbestandWidget formula (no initial_balance)
     * For other accounts: Includes initial_balance
     */
    public function currentBalance(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Get all transactions for this account
                $transactions = $this->transactions();

                // Positive contributors (add to balance)
                // Note: Save Back is NOT included to match KassenbestandWidget
                $einzahlungen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Einzahlung'))->sum('amount');
                $verkaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Verkauf'))->sum('amount');
                $zinsen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Zinsen'))->sum('amount');
                $dividenden = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Dividenden'))->sum('amount');

                // Negative contributors (subtract from balance)
                $kaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Kauf'))->sum('amount');
                $ausgaben = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Ausgabe'))->sum('amount');
                $savebackSteuer = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Saveback Steuer'))->sum('amount');

                // Transfers
                $incomingTransfers = $this->incomingTransfers()->sum('amount');
                $outgoingTransfers = (clone $transactions)->whereNotNull('to_account_id')->sum('amount');

                // Calculate balance from transactions
                $transactionsBalance = $einzahlungen
                    + $verkaeufe
                    + $zinsen
                    + $dividenden
                    + $incomingTransfers
                    - $kaeufe
                    - $ausgaben
                    - $savebackSteuer
                    - $outgoingTransfers;

                // For non-Trade Republic accounts, include initial_balance
                // Trade Republic calculates from transactions only (matching KassenbestandWidget)
                if (!$this->is_trade_republic) {
                    return $this->initial_balance + $transactionsBalance;
                }

                return $transactionsBalance;
            }
        );
    }

    /**
     * Get balance at a specific date
     */
    public function balanceAtDate($date)
    {
        // Get all transactions for this account up to the date
        $transactions = $this->transactions()->where('date', '<=', $date);

        // Positive contributors
        $einzahlungen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Einzahlung'))->sum('amount');
        $verkaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Verkauf'))->sum('amount');
        $zinsen = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Zinsen'))->sum('amount');
        $dividenden = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Dividenden'))->sum('amount');

        // Negative contributors
        $kaeufe = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Kauf'))->sum('amount');
        $ausgaben = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Ausgabe'))->sum('amount');
        $savebackSteuer = (clone $transactions)->whereHas('type', fn ($q) => $q->where('name', 'Saveback Steuer'))->sum('amount');

        // Transfers
        $incomingTransfers = $this->incomingTransfers()->where('date', '<=', $date)->sum('amount');
        $outgoingTransfers = (clone $transactions)->whereNotNull('to_account_id')->sum('amount');

        return $einzahlungen
            + $verkaeufe
            + $zinsen
            + $dividenden
            + $incomingTransfers
            - $kaeufe
            - $ausgaben
            - $savebackSteuer
            - $outgoingTransfers;
    }
}
