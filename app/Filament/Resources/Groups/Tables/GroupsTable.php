<?php

namespace App\Filament\Resources\Groups\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class GroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('color')
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
                   
                TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label('Transactions')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            ]);
    }
}
