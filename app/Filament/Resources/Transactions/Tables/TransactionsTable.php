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
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Actions\Action;

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
                ExportAction::make()
                    ->label('Export CSV')
                    ->exports([
                        ExcelExport::make('table')
                            ->fromTable()
                            ->withFilename(fn () => 'transactions-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('date')->heading('Datum'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('account.name')->heading('Konto'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('amount')->heading('Betrag'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('category.name')->heading('Kategorie'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('toAccount.name')->heading('Zielkonto'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('type.name')->heading('Typ'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('entity.name')->heading('Beschreibung'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('notes')->heading('Notizen'),
                            ]),
                    ]),
                \Filament\Actions\ActionGroup::make([
                    Action::make('exportOverviewCSV')
                        ->label('Export als CSV')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $transactions = $query->with(['type', 'account', 'category'])->get();
                            
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\TransactionsOverviewExport($transactions),
                                'transactions-overview-' . date('Y-m-d') . '.csv',
                                \Maatwebsite\Excel\Excel::CSV
                            );
                        }),
                    Action::make('exportOverviewPDF')
                        ->label('Export als PDF')
                        ->icon('heroicon-o-document')
                        ->color('warning')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $transactions = $query->with(['type', 'account', 'category'])->get();
                            
                            // Calculate statistics (same as TransactionsOverviewExport)
                            $totalTransactions = $transactions->count();
                            $totalAmount = $transactions->sum('amount');
                            
                            // Calculate Einnahmen and Ausgaben separately
                            $einzahlungen = $transactions->where('type.name', 'Einzahlung')->sum('amount');
                            $verkaeufe = $transactions->where('type.name', 'Verkauf')->sum('amount');
                            $zinsen = $transactions->where('type.name', 'Zinsen')->sum('amount');
                            $dividenden = $transactions->where('type.name', 'Dividenden')->sum('amount');
                            
                            $kaeufe = abs($transactions->where('type.name', 'Kauf')->sum('amount'));
                            $ausgaben = abs($transactions->where('type.name', 'Ausgabe')->sum('amount'));
                            $savebackSteuer = abs($transactions->where('type.name', 'Saveback Steuer')->sum('amount'));
                            
                            $income = $einzahlungen + $verkaeufe + $zinsen + $dividenden;
                            $expenses = $kaeufe + $ausgaben + $savebackSteuer;
                            $kassenbestand = $income - $expenses;
                            
                            $avgAmount = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;
                            $maxAmount = $transactions->max('amount') ?? 0;
                            $minAmount = $transactions->min('amount') ?? 0;
                            
                            // Group by type
                            $byType = $transactions->groupBy('type.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                    'avg' => $group->avg('amount'),
                                ];
                            })->toArray();
                            
                            // Group by account
                            $byAccount = $transactions->groupBy('account.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                ];
                            })->toArray();
                            
                            // Group by category
                            $byCategory = $transactions->filter(fn ($t) => $t->category)->groupBy('category.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                ];
                            })->toArray();
                            
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.transactions-overview-pdf', [
                                'totalTransactions' => $totalTransactions,
                                'totalAmount' => $totalAmount,
                                'income' => $income,
                                'expenses' => $expenses,
                                'kassenbestand' => $kassenbestand,
                                'einzahlungen' => $einzahlungen,
                                'verkaeufe' => $verkaeufe,
                                'zinsen' => $zinsen,
                                'dividenden' => $dividenden,
                                'kaeufe' => $kaeufe,
                                'ausgaben' => $ausgaben,
                                'savebackSteuer' => $savebackSteuer,
                                'avgAmount' => $avgAmount,
                                'maxAmount' => $maxAmount,
                                'minAmount' => $minAmount,
                                'byType' => $byType,
                                'byAccount' => $byAccount,
                                'byCategory' => $byCategory,
                            ]);
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'transactions-overview-' . date('Y-m-d') . '.pdf');
                        }),
                    Action::make('exportDetailedPDF')
                        ->label('Export mit Details')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\DatePicker::make('date_from')
                                ->label('Von Datum')
                                ->default(now()->startOfMonth())
                                ->maxDate(now()),
                            \Filament\Forms\Components\DatePicker::make('date_to')
                                ->label('Bis Datum')
                                ->default(now())
                                ->maxDate(now()),
                            \Filament\Forms\Components\Toggle::make('include_table')
                                ->label('Komplette Tabelle einschließen')
                                ->helperText('Fügt alle Transaktionen des Zeitraums zur PDF hinzu')
                                ->default(false),
                        ])
                        ->action(function ($livewire, array $data) {
                            $query = $livewire->getFilteredTableQuery();
                            
                            // Apply date filters
                            if (isset($data['date_from'])) {
                                $query->whereDate('date', '>=', $data['date_from']);
                            }
                            if (isset($data['date_to'])) {
                                $query->whereDate('date', '<=', $data['date_to']);
                            }
                            
                            // Sort by date descending
                            $query->orderBy('date', 'desc')->orderBy('id', 'desc');
                            
                            $transactions = $query->with(['type', 'account', 'category', 'entity', 'toAccount'])->get();
                            
                            // Calculate statistics
                            $totalTransactions = $transactions->count();
                            $totalAmount = $transactions->sum('amount');
                            
                            $einzahlungen = $transactions->where('type.name', 'Einzahlung')->sum('amount');
                            $verkaeufe = $transactions->where('type.name', 'Verkauf')->sum('amount');
                            $zinsen = $transactions->where('type.name', 'Zinsen')->sum('amount');
                            $dividenden = $transactions->where('type.name', 'Dividenden')->sum('amount');
                            
                            $kaeufe = abs($transactions->where('type.name', 'Kauf')->sum('amount'));
                            $ausgaben = abs($transactions->where('type.name', 'Ausgabe')->sum('amount'));
                            $savebackSteuer = abs($transactions->where('type.name', 'Saveback Steuer')->sum('amount'));
                            
                            $income = $einzahlungen + $verkaeufe + $zinsen + $dividenden;
                            $expenses = $kaeufe + $ausgaben + $savebackSteuer;
                            $kassenbestand = $income - $expenses;
                            
                            $avgAmount = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;
                            $maxAmount = $transactions->max('amount') ?? 0;
                            $minAmount = $transactions->min('amount') ?? 0;
                            
                            // Group by type
                            $byType = $transactions->groupBy('type.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                    'avg' => $group->avg('amount'),
                                ];
                            })->toArray();
                            
                            // Group by account
                            $byAccount = $transactions->groupBy('account.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                ];
                            })->toArray();
                            
                            // Group by category
                            $byCategory = $transactions->filter(fn ($t) => $t->category)->groupBy('category.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                ];
                            })->toArray();
                            
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.transactions-detailed-pdf', [
                                'totalTransactions' => $totalTransactions,
                                'totalAmount' => $totalAmount,
                                'income' => $income,
                                'expenses' => $expenses,
                                'kassenbestand' => $kassenbestand,
                                'einzahlungen' => $einzahlungen,
                                'verkaeufe' => $verkaeufe,
                                'zinsen' => $zinsen,
                                'dividenden' => $dividenden,
                                'kaeufe' => $kaeufe,
                                'ausgaben' => $ausgaben,
                                'savebackSteuer' => $savebackSteuer,
                                'avgAmount' => $avgAmount,
                                'maxAmount' => $maxAmount,
                                'minAmount' => $minAmount,
                                'byType' => $byType,
                                'byAccount' => $byAccount,
                                'byCategory' => $byCategory,
                                'includeTable' => $data['include_table'] ?? false,
                                'transactions' => $data['include_table'] ? $transactions : collect(),
                                'dateFrom' => $data['date_from'] ?? null,
                                'dateTo' => $data['date_to'] ?? null,
                            ]);
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'transactions-detailed-' . date('Y-m-d') . '.pdf');
                        }),
                ])
                    ->label('Export Übersicht')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->button(),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Export Ausgewählte')
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'transactions-selected-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
