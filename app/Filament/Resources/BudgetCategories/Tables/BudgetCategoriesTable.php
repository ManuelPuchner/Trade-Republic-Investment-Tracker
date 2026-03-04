<?php

namespace App\Filament\Resources\BudgetCategories\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BudgetCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Hauptkategorie')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subcategory')
                    ->label('Unterkategorie')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Hauptkategorie')
                    ->options([
                        'Einnahmen' => 'Einnahmen',
                        'Ausgaben' => 'Ausgaben',
                    ])
                    ->multiple(),

                SelectFilter::make('subcategory')
                    ->label('Unterkategorie')
                    ->options(function () {
                        return \App\Models\BudgetCategory::distinct()
                            ->pluck('subcategory', 'subcategory');
                    })
                    ->multiple()
                    ->preload(),
            ])
            ->defaultSort('category')
            ->defaultSort('subcategory')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
