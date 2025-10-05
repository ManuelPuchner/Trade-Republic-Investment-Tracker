<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class AccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Kontoname'),
                    
                TextEntry::make('account_number')
                    ->label('Kontonummer'),
                    
                TextEntry::make('bank_name')
                    ->label('Bank'),
                    
                TextEntry::make('account_type')
                    ->label('Kontotyp')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'checking' => 'Girokonto',
                        'savings' => 'Sparkonto',
                        'investment' => 'Anlagekonto',
                        'other' => 'Sonstiges',
                        default => $state,
                    }),
                    
                IconEntry::make('is_trade_republic')
                    ->label('Trade Republic Konto')
                    ->boolean(),
                    
                TextEntry::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(),
            ]);
    }
}
