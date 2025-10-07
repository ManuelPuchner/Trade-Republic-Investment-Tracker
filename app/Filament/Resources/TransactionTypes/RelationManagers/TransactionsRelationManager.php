<?php

namespace App\Filament\Resources\TransactionTypes\RelationManagers;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\TransactionTypes\TransactionTypeResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'Transactions';

    protected static ?string $relatedResource = TransactionTypeResource::class;

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
            ])
            ->defaultSort('date', 'desc')
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
}
