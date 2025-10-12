<?php

namespace App\Filament\Resources\Groups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Gruppenname')
                    ->icon('heroicon-o-tag')
                    ->size('lg')
                    ->weight('bold'),

                TextEntry::make('description')
                    ->label('Beschreibung')
                    ->placeholder('Keine Beschreibung angegeben')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),

                TextEntry::make('color')
                    ->label('Farbe')
                    ->icon('heroicon-o-swatch')
                    ->badge()
                    ->color(fn ($record): array|string => $record->color ? [
                        50 => $record->color.'1A',   // 10% opacity
                        100 => $record->color.'33',  // 20% opacity
                        200 => $record->color.'4D',  // 30% opacity
                        300 => $record->color.'66',  // 40% opacity
                        400 => $record->color.'80',  // 50% opacity
                        500 => $record->color,       // 100% opacity
                        600 => $record->color,
                        700 => $record->color,
                        800 => $record->color,
                        900 => $record->color,
                        950 => $record->color,
                    ] : 'gray'),

                TextEntry::make('transactions_count')
                    ->label('Anzahl Transaktionen')
                    ->getStateUsing(fn ($record) => $record->transactions()->count())
                    ->icon('heroicon-o-banknotes')
                    ->badge()
                    ->color('primary'),

                TextEntry::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y H:i')
                    ->icon('heroicon-o-plus-circle'),

                TextEntry::make('updated_at')
                    ->label('Zuletzt bearbeitet')
                    ->dateTime('d.m.Y H:i')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->columns(2);
    }
}
