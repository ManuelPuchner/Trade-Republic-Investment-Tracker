<?php

namespace App\Filament\Resources\Entities\Tables;

use App\Models\Entity;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class EntitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-storefront')
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Typ')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Entity::getTypes()[$state] ?? $state)
                    ->colors([
                        'info' => 'ETF',
                        'success' => 'Company',
                        'warning' => 'Person',
                    ])
                    ->icons([
                        'heroicon-o-chart-bar' => 'ETF',
                        'heroicon-o-building-office' => 'Company',
                        'heroicon-o-user' => 'Person',
                    ]),

                TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label('Transaktionen')
                    ->sortable()
                    ->icon('heroicon-o-banknotes'),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Typ')
                    ->options(Entity::getTypes()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }
}
