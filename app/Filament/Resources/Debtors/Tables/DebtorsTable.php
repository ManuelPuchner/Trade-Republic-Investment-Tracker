<?php

namespace App\Filament\Resources\Debtors\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class DebtorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->weight('bold'),
                    
                    
                TextColumn::make('debts_count')
                    ->label('Anzahl Schulden')
                    ->counts('debts')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('total_debt_amount')
                    ->label('Gesamtbetrag')
                    ->money('EUR')
                    ->getStateUsing(fn ($record) => $record->total_debt_amount)
                    ->sortable()
                    ->icon('heroicon-o-currency-euro')
                    ->color('warning'),
                    
                TextColumn::make('open_debt_amount')
                    ->label('Offene Schulden')
                    ->money('EUR')
                    ->getStateUsing(fn ($record) => $record->open_debt_amount)
                    ->sortable()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color(fn ($record) => $record->open_debt_amount > 0 ? 'danger' : 'success'),
                    
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
