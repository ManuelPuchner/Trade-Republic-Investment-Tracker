<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Kontoname')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('account_number')
                    ->label('Kontonummer')
                    ->maxLength(255),
                
                TextInput::make('bank_name')
                    ->label('Bank')
                    ->maxLength(255),
                
                Select::make('account_type')
                    ->label('Kontotyp')
                    ->options([
                        'checking' => 'Girokonto',
                        'savings' => 'Sparkonto',
                        'investment' => 'Anlagekonto',
                        'other' => 'Sonstiges'
                    ])
                    ->default('checking')
                    ->required(),
                
                Toggle::make('is_trade_republic')
                    ->label('Trade Republic Konto')
                    ->helperText('Markieren Sie dies, wenn es sich um Ihr Trade Republic Konto handelt')
                    ->default(false)
            ]);
    }
}
