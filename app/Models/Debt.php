<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'amount',
        'debtor_id',
        'description',
        'payment_method',
        'transaction_id',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    // Payment methods
    const PAYMENT_CASH = 'cash';

    const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    const PAYMENT_TRADE_REPUBLIC = 'trade_republic';

    const PAYMENT_OTHER = 'other';

    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_CASH => 'Bar',
            self::PAYMENT_BANK_TRANSFER => 'Banküberweisung',
            self::PAYMENT_TRADE_REPUBLIC => 'Trade Republic',
            self::PAYMENT_OTHER => 'Andere',
        ];
    }

    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return self::getPaymentMethods()[$this->payment_method] ?? $this->payment_method;
    }
}
