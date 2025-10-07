<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = ['name', 'type'];

    protected $casts = [
        'type' => 'string',
    ];

    // Available entity types
    public static function getTypes(): array
    {
        return [
            'ETF' => 'ETF',
            'Company' => 'Unternehmen',
            'Person' => 'Person',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
