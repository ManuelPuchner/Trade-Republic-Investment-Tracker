<?php

namespace App\Filament\Resources\Groups\RelationManagers;

use App\Models\Account;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\Transactions\TransactionResource;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $relatedResource = TransactionResource::class;

    protected static ?string $relationshipTitle = 'Transactions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Datum')
                    ->sortable()
                    ->date(),

                TextColumn::make('amount')
                    ->label('Betrag')
                    ->money('eur')
                    ->summarize([
                        Summarizer::make()
                            ->label('Summary')
                            ->using(function (QueryBuilder $query): string {
                                // Use the provided query builder which already has pagination and filtering applied
                                $results = $query
                                    ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
                                    ->select('transactions.amount', 'transaction_types.name as type_name')
                                    ->get();

                                $netTotal = 0;
                                $dividendTotal = 0;

                                foreach ($results as $transaction) {
                                    switch ($transaction->type_name) {
                                        case 'Kauf':
                                        case 'Ausgabe':
                                        case 'Saveback Steuer':
                                            $netTotal -= $transaction->amount;
                                            break;
                                        case 'Verkauf':
                                        case 'Einzahlungen':
                                        case 'Zinsen':
                                        case 'Dividenden':
                                            $netTotal += $transaction->amount;
                                            if ($transaction->type_name === 'Dividenden') {
                                                $dividendTotal += $transaction->amount;
                                            }
                                            break;
                                        default:
                                            $netTotal += $transaction->amount;
                                            break;
                                    }
                                }

                                $netTotalFormatted = '€'.number_format($netTotal, 2);
                                $dividendTotalFormatted = $dividendTotal > 0 ? '€'.number_format($dividendTotal, 2) : '-';

                                return "Net Total: {$netTotalFormatted} | Dividends: {$dividendTotalFormatted}";
                            }),
                    ]),

                TextColumn::make('account.name')
                    ->label('Konto')
                    ->sortable()
                    ->icon('heroicon-o-credit-card')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                TextColumn::make('type.name')
                    ->label('Transaction Type')
                    ->badge()
                    ->color(fn ($record): string => $record->type->color ?? 'gray'),

                TextColumn::make('entity.name')
                    ->label('Entity')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                // Add filter groups as tabs
            ])
            ->filtersTriggerAction(
                fn ($action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => TransactionResource::getUrl('edit', ['record' => $record])),
                ViewAction::make()
                    ->url(fn ($record) => TransactionResource::getUrl('view', ['record' => $record])),
                DeleteAction::make()
                    ->url(fn ($record) => TransactionResource::getUrl('edit', ['record' => $record])),

            ]);
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Alle')
                ->badge(fn () => $this->getOwnerRecord()->transactions()->count()),
        ];

        // Get all accounts that have transactions for this group
        $accounts = Account::whereHas('transactions', function (EloquentBuilder $query) {
            $query->where('group_id', $this->getOwnerRecord()->id);
        })->orderBy('name')->get();

        foreach ($accounts as $account) {
            $tabs[$account->id] = Tab::make($account->name)
                ->badge(fn () => $this->getOwnerRecord()->transactions()->where('account_id', $account->id)->count())
                ->icon('heroicon-o-credit-card')
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('account_id', $account->id));
        }

        return $tabs;
    }
}
