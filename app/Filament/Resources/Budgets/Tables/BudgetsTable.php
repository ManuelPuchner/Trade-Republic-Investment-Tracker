<?php

namespace App\Filament\Resources\Budgets\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetCategory.name')
                    ->label('Kategorie')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag'),

                TextColumn::make('budgetCategory.subcategory')
                    ->label('Unterkategorie')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('amount')
                    ->label('Budget')
                    ->money('EUR')
                    ->sortable()
                    ->alignment('end'),

                BadgeColumn::make('period')
                    ->label('Zeitraum')
                    ->colors([
                        'primary' => 'monthly',
                        'secondary' => 'quarterly',
                        'success' => 'yearly',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Monatlich',
                        'quarterly' => 'Quartalsweise',
                        'yearly' => 'Jährlich',
                    })
                    ->sortable(),

                TextColumn::make('month')
                    ->label('Monat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('year')
                    ->label('Jahr')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Zeitraum')
                    ->options([
                        'monthly' => 'Monatlich',
                        'quarterly' => 'Quartalsweise',
                        'yearly' => 'Jährlich',
                    ]),

                SelectFilter::make('budgetCategory.category')
                    ->label('Hauptkategorie')
                    ->relationship('budgetCategory', 'category')
                    ->multiple()
                    ->preload(),
            ])
            ->defaultSort('year', 'desc');
    }
}
