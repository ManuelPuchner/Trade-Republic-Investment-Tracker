<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'transaction_type_id',
        'entity_id',
        'parent_id',
        'account_id',
        'category_id',
        'to_account_id',
        'group_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Scope to add id as secondary sort if not already sorting by id
     */
    public function scopeWithSecondaryIdSort($query)
    {
        $orders = $query->getQuery()->orders ?? [];
        $hasIdOrder = collect($orders)->contains(fn($order) => 
            isset($order['column']) && $order['column'] === 'id'
        );
        
        if (!$hasIdOrder) {
            $query->orderBy('id', 'desc');
        }
        
        return $query;
    }

    public function type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function parent()
    {
        return $this->belongsTo(Transaction::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Transaction::class, 'parent_id');
    }

    public function debt()
    {
        return $this->hasOne(Debt::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Check if this is a transfer transaction
     */
    public function isTransfer(): bool
    {
        return !is_null($this->to_account_id);
    }

    /**
     * Scope for filtering non-transfer transactions
     */
    public function scopeNonTransfer($query)
    {
        return $query->whereNull('to_account_id');
    }

    /**
     * Scope for filtering transfer transactions
     */
    public function scopeTransfers($query)
    {
        return $query->whereNotNull('to_account_id');
    }
}
