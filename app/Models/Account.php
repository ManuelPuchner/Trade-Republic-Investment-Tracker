<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'bank_name',
        'account_type',
        'is_trade_republic'
    ];

    protected $casts = [
        'is_trade_republic' => 'boolean'
    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}