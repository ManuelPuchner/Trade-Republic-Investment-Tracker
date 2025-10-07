<?php

namespace App\Filament\Resources\Debts\Schemas;

use App\Models\Debtor;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class DebtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('debtor_id')
                    ->label('Schuldner')
                    ->options(Debtor::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->prefixIcon('heroicon-o-user')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),
                        TextInput::make('email')
                            ->label('E-Mail')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope'),
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone'),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Debtor::create($data)->id;
                    }),

                TextInput::make('amount')
                    ->label('Betrag')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->prefixIcon('heroicon-o-currency-euro'),

                Textarea::make('description')
                    ->label('Beschreibung')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Was war der Grund für diese Schuld?'),

                Toggle::make('is_paid')
                    ->label('Bereits bezahlt')
                    ->live()
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-clock'),

                RadioDeck::make('payment_method')
                    ->label('Zahlungsart')
                    ->options([
                        'cash' => 'Bar',
                        'bank_transfer' => 'Bank',
                        'trade_republic' => 'Trade Republic',
                        'other' => 'Andere',
                    ])
                    ->descriptions([
                        'cash' => 'Bargeld erhalten',
                        'bank_transfer' => 'Überweisung auf Bankkonto',
                        'trade_republic' => 'Einzahlung auf Trade Republic Konto',
                        'other' => 'Sonstige Zahlungsart',
                    ])
                    ->icons([
                        'cash' => 'heroicon-o-banknotes',
                        'bank_transfer' => 'heroicon-o-building-library',
                        'trade_republic' => 'heroicon-o-chart-bar',
                        'other' => 'heroicon-o-credit-card',
                    ])
                    ->visible(fn ($get) => $get('is_paid'))
                    ->live(onBlur: false)
                    ->columns(2)
                    ->colors('primary')
                    ->afterStateUpdated(function ($state, $set) {
                        // Trigger account_id update by setting it based on payment method
                        $firstAccount = match ($state) {
                            'cash' => Account::where('account_type', 'cash')->first(),
                            'bank_transfer' => Account::where('is_trade_republic', false)
                                ->where('account_type', '!=', 'cash')
                                ->first(),
                            'trade_republic' => Account::where('is_trade_republic', true)->first(),
                            default => null,
                        };
                        
                        $set('account_id', $firstAccount?->id);
                    }),

                Select::make('account_id')
                    ->label('Empfangenes Konto')
                    ->live()
                    ->options(function ($get) {
                        $paymentMethod = $get('payment_method');

                        if ($paymentMethod === 'cash') {
                            return Account::where('account_type', 'cash')->pluck('name', 'id');
                        } elseif ($paymentMethod === 'bank_transfer') {
                            return Account::where('is_trade_republic', false)
                                ->where('account_type', '!=', 'cash')
                                ->pluck('name', 'id');
                        } elseif ($paymentMethod === 'trade_republic') {
                            return Account::where('is_trade_republic', true)->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->afterStateHydrated(function ($component, $state, $get) {
                        // Set default on initial load if no value is set
                        if (!$state && $get('is_paid')) {
                            $paymentMethod = $get('payment_method');
                            
                            $firstAccount = match ($paymentMethod) {
                                'cash' => Account::where('account_type', 'cash')->first(),
                                'bank_transfer' => Account::where('is_trade_republic', false)
                                    ->where('account_type', '!=', 'cash')
                                    ->first(),
                                'trade_republic' => Account::where('is_trade_republic', true)->first(),
                                default => null,
                            };
                            
                            if ($firstAccount) {
                                $component->state($firstAccount->id);
                            }
                        }
                    })
                    ->searchable()
                    ->prefixIcon('heroicon-o-building-library')
                    ->visible(fn ($get) => $get('is_paid') && in_array($get('payment_method'), ['cash', 'bank_transfer', 'trade_republic'])),

                DateTimePicker::make('paid_at')
                    ->label('Bezahlt am')
                    ->nullable()
                    ->prefixIcon('heroicon-o-calendar')
                    ->visible(fn ($get) => $get('is_paid')),

                Select::make('transaction_id')
                    ->label('Verknüpfte Transaktion')
                    ->options(Transaction::with('entity')
                        ->whereHas('entity')
                        ->get()
                        ->mapWithKeys(function ($transaction) {
                            return [$transaction->id => "{$transaction->entity->name} - €{$transaction->amount} ({$transaction->date})"];
                        }))
                    ->nullable()
                    ->searchable()
                    ->prefixIcon('heroicon-o-arrow-path')
                    ->visible(fn ($get) => $get('is_paid') && $get('payment_method') === 'trade_republic'),
            ]);
    }
}
