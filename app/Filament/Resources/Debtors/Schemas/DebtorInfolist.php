<?php

namespace App\Filament\Resources\Debtors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DebtorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name')
                    ->icon('heroicon-o-user')
                    ->size('lg')
                    ->weight('bold')
                    ->color('primary'),
                    
                TextEntry::make('email')
                    ->label('E-Mail')
                    ->icon('heroicon-o-envelope')
                    ->placeholder('Keine E-Mail hinterlegt')
                    ->copyable()
                    ->copyMessage('E-Mail kopiert!')
                    ->color(fn ($record) => $record->email ? 'success' : 'gray'),
                    
                TextEntry::make('phone')
                    ->label('Telefon')
                    ->icon('heroicon-o-phone')
                    ->placeholder('Keine Telefonnummer hinterlegt')
                    ->copyable()
                    ->copyMessage('Telefonnummer kopiert!')
                    ->color(fn ($record) => $record->phone ? 'success' : 'gray'),
                    
                TextEntry::make('total_debt_amount')
                    ->label('Gesamtschulden')
                    ->money('EUR')
                    ->icon('heroicon-o-currency-euro')
                    ->getStateUsing(fn ($record) => $record->total_debt_amount)
                    ->color('warning')
                    ->weight('bold'),
                    
                TextEntry::make('open_debt_amount')
                    ->label('Offene Schulden')
                    ->money('EUR')
                    ->icon('heroicon-o-clock')
                    ->getStateUsing(fn ($record) => $record->open_debt_amount)
                    ->color(fn ($record) => $record->open_debt_amount > 0 ? 'danger' : 'success')
                    ->weight('bold'),
                    
                TextEntry::make('paid_debt_amount')
                    ->label('Bezahlte Schulden')
                    ->money('EUR')
                    ->icon('heroicon-o-check-circle')
                    ->getStateUsing(fn ($record) => $record->paid_debt_amount)
                    ->color('success')
                    ->weight('bold'),
                    
                TextEntry::make('notes')
                    ->label('Notizen')
                    ->icon('heroicon-o-document-text')
                    ->placeholder('Keine Notizen vorhanden')
                    ->color(fn ($record) => $record->notes ? 'primary' : 'gray')
                    ->columnSpanFull(),
                    
                TextEntry::make('created_at')
                    ->label('Erstellt am')
                    ->icon('heroicon-o-calendar')
                    ->dateTime()
                    ->color('gray'),
            ]);
    }
}
