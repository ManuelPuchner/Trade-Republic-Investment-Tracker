<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public static function getCurrentPortfolioValue(): float
    {
        $setting = self::where('key', 'current_portfolio_value')->first();
        return $setting ? $setting->value : 0.00;
    }

    public static function setCurrentPortfolioValue(float $value): void
    {
        self::updateOrCreate(
            ['key' => 'current_portfolio_value'],
            [
                'value' => $value,
                'description' => 'Current total portfolio value',
            ]
        );
    }

    public static function updateCurrentPortfolioValue(float $value): void
    {
        self::setCurrentPortfolioValue($value);
    }
}
