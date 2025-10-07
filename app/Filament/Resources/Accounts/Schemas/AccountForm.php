<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_type')
                    ->label('Kontotyp')
                    ->options([
                        'checking' => 'Girokonto',
                        'savings' => 'Sparkonto',
                        'investment' => 'Anlagekonto',
                        'cash' => 'Bargeld',
                        'other' => 'Sonstiges'
                    ])
                    ->default('checking')
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-o-credit-card')
                    ->helperText('Wählen Sie zuerst den Kontotyp aus')
                    ->columnSpanFull(),
                
                TextInput::make('name')
                    ->label('Kontoname')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-wallet')
                    ->placeholder(fn ($get) => match($get('account_type')) {
                        'cash' => 'z.B. Bargeld im Portemonnaie',
                        'checking' => 'z.B. Sparkasse Girokonto',
                        'savings' => 'z.B. Sparkasse Sparkonto',
                        'investment' => 'z.B. Trade Republic',
                        default => 'Name des Kontos'
                    }),
                
                TextInput::make('bank_name')
                    ->label('Bank')
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-building-library')
                    ->hidden(fn ($get) => $get('account_type') === 'cash')
                    ->required(fn ($get) => in_array($get('account_type'), ['checking', 'savings']))
                    ->placeholder('z.B. Sparkasse, Deutsche Bank'),
                
                TextInput::make('account_number')
                    ->label('Kontonummer / IBAN')
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-hashtag')
                    ->hidden(fn ($get) => $get('account_type') === 'cash')
                    ->placeholder('Optional'),
                
                Toggle::make('is_trade_republic')
                    ->label('Trade Republic Konto')
                    ->helperText('Markieren Sie dies, wenn es sich um Ihr Trade Republic Konto handelt')
                    ->default(false)
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->visible(fn ($get) => $get('account_type') === 'investment')
                    ->columnSpanFull(),

                TextInput::make('initial_balance')
                    ->label('Anfangssaldo')
                    ->numeric()
                    ->default(0)
                    ->prefix('€')
                    ->required()
                    ->helperText('Der Kontostand zu Beginn')
                    ->prefixIcon('heroicon-o-currency-euro'),
                
                DateTimePicker::make('initial_balance_date')
                    ->label('Datum des Anfangssaldos')
                    ->default(now())
                    ->required()
                    ->helperText('Ab wann gilt dieser Anfangssaldo')
                    ->prefixIcon('heroicon-o-calendar'),
            ])
            ->columns(2);
    }
}
