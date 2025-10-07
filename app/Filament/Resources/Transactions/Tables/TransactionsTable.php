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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('date')
                    ->label('Datum')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-calendar'),
                
                TextColumn::make('account.name')
                    ->label('Konto')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-wallet')
                    ->toggleable(),
                
                TextColumn::make('amount')
                    ->label('Betrag')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(function ($record) {
                        $amount = abs($record->amount);
                        $formatted = '€' . number_format($amount, 2, ',', '.');
                        
                        // Determine prefix based on transaction type
                        // Subtracts (-): Kauf, Ausgabe, Saveback Steuer, Transfers
                        // Adds (+): Einzahlung, Verkauf, Zinsen, Dividenden, Saveback
                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };
                        
                        return $subtractsFromAccount ? "- {$formatted}" : "+ {$formatted}";
                    })
                    ->color(function ($record): string {
                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };
                        return $subtractsFromAccount ? 'danger' : 'success';
                    })
                    ->icon(function ($record): string {
                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };
                        return $subtractsFromAccount ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up';
                    }),
                
                TextColumn::make('category.name')
                    ->label('Kategorie')
                    ->badge()
                    ->color(fn ($record): string => $record->category?->color ?? 'gray')
                    ->icon(fn ($record): ?string => $record->category?->icon)
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('toAccount.name')
                    ->label('Zielkonto')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('type.name')
                    ->label('Typ')
                    ->badge()
                    ->color(fn ($record): string => $record->type->color ?? 'gray')
                    ->sortable(),
                
                TextColumn::make('entity.name')
                    ->label('Beschreibung')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—')
                    ->icon('heroicon-o-document-text'),
                
                TextColumn::make('parent_id')
                    ->label('Übergeordnet')
                    ->badge()
                    ->sortable()
                    ->url(fn ($record) => $record->parent_id
                        ? route('filament.admin.resources.transactions.edit', $record->parent_id)
                        : null)
                    ->getStateUsing(fn ($record) => $record->parent_id)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('notes')
                    ->label('Notizen')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Konto')
                    ->relationship('account', 'name'),
                
                SelectFilter::make('category_id')
                    ->label('Kategorie')
                    ->relationship('category', 'name'),
                
                SelectFilter::make('to_account_id')
                    ->label('Zielkonto (Transfers)')
                    ->relationship('toAccount', 'name'),
                
                SelectFilter::make('transaction_type_id')
                    ->label('Transaktionstyp')
                    ->relationship('type', 'name'),
                
                SelectFilter::make('entity_id')
                    ->label('Wertpapier')
                    ->relationship('entity', 'name'),
                
                SelectFilter::make('parent_id')
                    ->label('Übergeordnete Transaktion')
                    ->relationship('parent', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => '#'.str_pad($record->id, 6, '0', STR_PAD_LEFT).' - '.$record->entity?->name)
                    ->searchable(),
                
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('date_from')->label('Von'),
                        DatePicker::make('date_to')->label('Bis'),
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
