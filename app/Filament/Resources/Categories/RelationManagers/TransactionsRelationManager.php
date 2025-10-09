<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Account;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $relatedResource = TransactionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('entity.name')
                    ->label('Beschreibung')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('entities', 'transactions.entity_id', '=', 'entities.id')
                            ->orderBy('entities.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    })
                    ->searchable()
                    ->placeholder('—')
                    ->icon('heroicon-o-document-text')
                    ->limit(25)
                    ->tooltip(function (\Filament\Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }

                        return $state;
                    }),

                \Filament\Tables\Columns\TextColumn::make('date')
                    ->label('Datum')
                    ->date()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('date', $direction)->orderBy('id', 'desc');
                    })
                    ->searchable()
                    ->icon('heroicon-o-calendar'),

                \Filament\Tables\Columns\TextColumn::make('account.name')
                    ->label('Konto')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                            ->orderBy('accounts.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    })
                    ->searchable()
                    ->icon('heroicon-o-wallet')
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('amount')
                    ->label('Betrag')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('amount', $direction)->orderBy('id', 'desc');
                    })
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(function ($record) {
                        $amount = abs($record->amount);
                        $formatted = '€'.number_format($amount, 2, ',', '.');

                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match ($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };

                        return $subtractsFromAccount ? "- {$formatted}" : "+ {$formatted}";
                    })
                    ->color(function ($record): string {
                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match ($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };

                        return $subtractsFromAccount ? 'danger' : 'success';
                    })
                    ->icon(function ($record): string {
                        $typeName = $record->type?->name;
                        $subtractsFromAccount = match ($typeName) {
                            'Kauf', 'Ausgabe', 'Saveback Steuer' => true,
                            'Einzahlung', 'Verkauf', 'Zinsen', 'Dividenden', 'Save Back' => false,
                            default => $record->amount < 0 || $record->to_account_id !== null
                        };

                        return $subtractsFromAccount ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up';
                    })
                    ->summarize([
                        Sum::make()
                            ->label('Gesamt')
                            ->money('EUR')
                            ->formatStateUsing(fn ($state) => '€ '.number_format($state, 2, ',', '.')),
                        Average::make()
                            ->label('Durchschnitt')
                            ->money('EUR')
                            ->formatStateUsing(fn ($state) => '€ '.number_format($state, 2, ',', '.')),
                    ]),

                \Filament\Tables\Columns\TextColumn::make('type.name')
                    ->label('Typ')
                    ->badge()
                    ->color(fn ($record): string => $record->type->color ?? 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                \Filament\Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Konto')
                    ->relationship('account', 'name')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('entity_id')
                    ->label('Beschreibung')
                    ->relationship('entity', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Alle Konten')
                ->badge(fn () => $this->getOwnerRecord()->transactions()->count()),
        ];

        // Get all accounts that have transactions for this category
        $accounts = Account::whereHas('transactions', function (Builder $query) {
            $query->where('category_id', $this->getOwnerRecord()->id);
        })->orderBy('name')->get();

        foreach ($accounts as $account) {
            $tabs[$account->id] = Tab::make($account->name)
                ->badge(fn () => $this->getOwnerRecord()->transactions()->where('account_id', $account->id)->count())
                ->icon($account->is_trade_republic ? 'heroicon-o-chart-bar' : 'heroicon-o-building-library')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_id', $account->id));
        }

        return $tabs;
    }
}
