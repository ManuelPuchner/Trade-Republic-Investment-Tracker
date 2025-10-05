<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('date')->sortable()->searchable(),
                TextColumn::make('amount')->sortable()->searchable(),
                TextColumn::make('type.name')
                    ->label('Transaction Type')
                    ->badge()
                    ->color(fn ($record): string => $record->type->color ?? 'gray')
                    ->sortable(),
                TextColumn::make('entity.name')->label('Entity')->sortable()->searchable(),
                TextColumn::make('parent_id')->label('Parent')
                    ->badge()
                    ->sortable()
                    ->url(fn ($record) => $record->parent_id
                        ? route('filament.admin.resources.transactions.edit', $record->parent_id)
                        : null)
                    ->getStateUsing(fn ($record) => $record->parent_id),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('transaction_type_id')
                    ->label('Transaction Type')
                    ->relationship('type', 'name'),
                SelectFilter::make('entity_id')
                    ->label('Entity')
                    ->relationship('entity', 'name'),
                SelectFilter::make('parent_id')
                    ->label('Parent Transaction')
                    ->relationship('parent', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => '#'.str_pad($record->id, 6, '0', STR_PAD_LEFT).' - '.$record->entity?->name)
                    ->searchable(),
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('date_from')->label('From'),
                        DatePicker::make('date_to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['date_from'], fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date))
                            ->when($data['date_to'], fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
