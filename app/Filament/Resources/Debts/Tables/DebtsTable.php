<?php

namespace App\Filament\Resources\Debts\Tables;

use App\Models\Debt;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class DebtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('debtor.name')
                    ->label('Schuldner')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->placeholder('Unbekannt'),

                TextColumn::make('amount')
                    ->label('Betrag')
                    ->money('EUR')
                    ->sortable()
                    ->icon('heroicon-o-currency-euro')
                    ->weight('bold')
                    ->color(fn ($record) => $record->is_paid ? 'success' : 'warning'),

                TextColumn::make('description')
                    ->label('Beschreibung')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return $state;
                    })
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('is_paid')
                    ->label('Status')
                    ->getStateUsing(fn ($record): string => $record->is_paid ? 'Bezahlt' : 'Offen')
                    ->badge()
                    ->colors([
                        'success' => 'Bezahlt',
                        'danger' => 'Offen',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Bezahlt',
                        'heroicon-o-clock' => 'Offen',
                    ]),

                TextColumn::make('payment_method')
                    ->label('Zahlungsart')
                    ->getStateUsing(fn ($record): ?string => $record->payment_method ? Debt::getPaymentMethods()[$record->payment_method] ?? $record->payment_method : null
                    )
                    ->badge()
                    ->colors([
                        'success' => fn ($state): bool => $state === 'Bar',
                        'info' => fn ($state): bool => $state === 'Banküberweisung',
                        'warning' => fn ($state): bool => $state === 'Trade Republic',
                        'gray' => fn ($state): bool => $state === 'Andere',
                    ])
                    ->icons([
                        'heroicon-o-banknotes' => fn ($state): bool => $state === 'Bar',
                        'heroicon-o-building-library' => fn ($state): bool => $state === 'Banküberweisung',
                        'heroicon-o-chart-bar' => fn ($state): bool => $state === 'Trade Republic',
                        'heroicon-o-credit-card' => fn ($state): bool => $state === 'Andere',
                    ])
                    ->placeholder('-'),

                TextColumn::make('account.name')
                    ->label('Konto')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('paid_at')
                    ->label('Bezahlt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_paid')
                    ->label('Status')
                    ->placeholder('Alle')
                    ->trueLabel('Bezahlt')
                    ->falseLabel('Offen'),

                SelectFilter::make('payment_method')
                    ->label('Zahlungsart')
                    ->options(Debt::getPaymentMethods()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('mark_paid')
                    ->label('Als bezahlt markieren')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Debt $record): bool => ! $record->is_paid)
                    ->requiresConfirmation()
                    ->action(fn (Debt $record) => $record->update([
                        'is_paid' => true,
                        'paid_at' => now(),
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
