<?php

namespace App\Filament\Resources\Budgets\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Category;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.category')
                    ->label('Hauptkategorie')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-folder'),

                TextColumn::make('category.subcategory')
                    ->label('Unterkategorie')
                    ->searchable()
                    ->sortable()
                    ->placeholder('–')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('category.name')
                    ->label('Kategorie')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag')
                    ->weight('bold'),

                TextColumn::make('amount')
                    ->label('Budget')
                    ->money('EUR')
                    ->sortable()
                    ->alignment('end')
                    ->weight('bold'),

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
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('month')
                    ->label('Monat')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? sprintf('%02d', $state) : '–')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('year')
                    ->label('Jahr')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('notes')
                    ->label('Notizen')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('–'),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y H:i')
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
                    ])
                    ->native(false),

                SelectFilter::make('category')
                    ->label('Hauptkategorie')
                    ->options(function () {
                        return Category::expenseCategories()
                            ->whereNotNull('category')
                            ->distinct()
                            ->pluck('category', 'category')
                            ->toArray();
                    })
                    ->native(false)
                    ->multiple()
                    ->preload(),

                SelectFilter::make('year')
                    ->label('Jahr')
                    ->options(function () {
                        $currentYear = now()->year;
                        $years = [];
                        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->native(false),

                SelectFilter::make('month')
                    ->label('Monat')
                    ->options([
                        1 => 'Januar',
                        2 => 'Februar',
                        3 => 'März',
                        4 => 'April',
                        5 => 'Mai',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'August',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Dezember',
                    ])
                    ->native(false),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc');
    }
}