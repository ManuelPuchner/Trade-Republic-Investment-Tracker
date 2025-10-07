<?php

namespace App\Filament\Resources\Debts\Schemas;

use App\Models\Debt;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DebtInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('debtor.name')
                    ->label('Schuldner')
                    ->icon('heroicon-o-user')
                    ->placeholder('Kein Schuldner zugewiesen')
                    ->url(fn ($record) => $record->debtor ? route('filament.admin.resources.debtors.view', $record->debtor) : null)
                    ->color(fn ($record) => $record->debtor ? 'primary' : 'gray'),

                TextEntry::make('amount')
                    ->label('Betrag')
                    ->money('EUR')
                    ->icon('heroicon-o-currency-euro')
                    ->color(fn ($record) => $record->is_paid ? 'success' : 'warning'),

                TextEntry::make('description')
                    ->label('Beschreibung')
                    ->icon('heroicon-o-document-text'),

                TextEntry::make('is_paid')
                    ->label('Status')
                    ->getStateUsing(fn ($record): string => $record->is_paid ? 'Bezahlt' : 'Offen')
                    ->icon(fn ($record): string => $record->is_paid ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $record->is_paid ? 'success' : 'danger'),

                TextEntry::make('payment_method')
                    ->label('Zahlungsart')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state) {
                            return 'Keine Zahlungsart ausgewählt';
                        }

                        return Debt::getPaymentMethods()[$state] ?? $state;
                    })
                    ->icon(function (?string $state): ?string {
                        return match ($state) {
                            'cash' => 'heroicon-o-banknotes',
                            'bank_transfer' => 'heroicon-o-building-library',
                            'trade_republic' => 'heroicon-o-chart-bar',
                            'other' => 'heroicon-o-credit-card',
                            default => null
                        };
                    })
                    ->color(fn ($record) => $record->payment_method ? 'success' : 'gray'),

                TextEntry::make('transaction.entity.name')
                    ->label('Verknüpfte Transaktion')
                    ->icon('heroicon-o-arrow-path')
                    ->placeholder('Keine Transaktion verknüpft')
                    ->formatStateUsing(function ($record) {
                        if (! $record->transaction) {
                            return null;
                        }

                        return "{$record->transaction->entity->name} - €{$record->transaction->amount} ({$record->transaction->date})";
                    })
                    ->url(fn ($record) => $record->transaction ? route('filament.admin.resources.transactions.view', $record->transaction) : null)
                    ->color(fn ($record) => $record->transaction ? 'primary' : 'gray'),

                TextEntry::make('paid_at')
                    ->label('Bezahlt am')
                    ->dateTime()
                    ->icon('heroicon-o-calendar')
                    ->placeholder('Noch nicht bezahlt')
                    ->color(fn ($record) => $record->paid_at ? 'success' : 'gray'),

                TextEntry::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(),
            ]);
    }
}
