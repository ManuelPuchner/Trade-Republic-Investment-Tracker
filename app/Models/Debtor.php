<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function getTotalDebtAmountAttribute()
    {
        return $this->debts()->sum('amount');
    }

    public function getOpenDebtAmountAttribute()
    {
        return $this->debts()->where('is_paid', false)->sum('amount');
    }

    public function getPaidDebtAmountAttribute()
    {
        return $this->debts()->where('is_paid', true)->sum('amount');
    }
}
