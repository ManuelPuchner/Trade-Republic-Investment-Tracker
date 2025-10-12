<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('id', $direction);
                    })
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Datum')
                    ->date()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('date', $direction)->orderBy('id', 'desc');
                    })
                    ->searchable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('entity.name')
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
                    ->icon('heroicon-o-document-text'),
                TextColumn::make('amount')
                    ->label('Betrag')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('amount', $direction)->orderBy('id', 'desc');
                    })
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(function ($record) {
                        $amount = abs($record->amount);
                        $formatted = '€'.number_format($amount, 2, ',', '.');

                        // Determine prefix based on transaction type
                        // Subtracts (-): Kauf, Ausgabe, Saveback Steuer, Transfers
                        // Adds (+): Einzahlung, Verkauf, Zinsen, Dividenden, Saveback
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
                    }),

                TextColumn::make('category.name')
                    ->label('Kategorie')
                    ->badge()
                    ->color(fn ($record): array|string => $record->category?->color ? [
                        50 => $record->category->color.'1A',   // 10% opacity
                        100 => $record->category->color.'33',  // 20% opacity
                        200 => $record->category->color.'4D',  // 30% opacity
                        300 => $record->category->color.'66',  // 40% opacity
                        400 => $record->category->color.'80',  // 50% opacity
                        500 => $record->category->color,         // 100% opacity
                        600 => $record->category->color,
                        700 => $record->category->color,
                        800 => $record->category->color,
                        900 => $record->category->color,
                        950 => $record->category->color,
                    ] : 'gray'
                    )
                    ->icon(fn ($record): ?string => $record->category?->icon)
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('categories', 'transactions.category_id', '=', 'categories.id')
                            ->orderBy('categories.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    })
                    ->toggleable(),

                TextColumn::make('account.name')
                    ->label('Konto')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                            ->orderBy('accounts.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    })
                    ->searchable()
                    ->limit(15)
                    ->icon('heroicon-o-wallet')
                    ->toggleable(),

                TextColumn::make('type.name')
                    ->label('Typ')
                    ->badge()
                    ->color(fn ($record): string => $record->type->color ?? 'gray')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('transaction_types', 'transactions.type_id', '=', 'transaction_types.id')
                            ->orderBy('transaction_types.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    }),

                TextColumn::make('toAccount.name')
                    ->label('Zielkonto')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('group.name')
                    ->label('Gruppe')
                    ->badge()
                    ->color(fn ($record): array|string => $record->group?->color ? [
                        50 => $record->group->color.'1A',   // 10% opacity
                        100 => $record->group->color.'33',  // 20% opacity
                        200 => $record->group->color.'4D',  // 30% opacity
                        300 => $record->group->color.'66',  // 40% opacity
                        400 => $record->group->color.'80',  // 50% opacity
                        500 => $record->group->color,       // 100% opacity
                        600 => $record->group->color,
                        700 => $record->group->color,
                        800 => $record->group->color,
                        900 => $record->group->color,
                        950 => $record->group->color,
                    ] : 'gray')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->leftJoin('groups', 'transactions.group_id', '=', 'groups.id')
                            ->orderBy('groups.name', $direction)
                            ->orderBy('transactions.id', 'desc')
                            ->select('transactions.*');
                    })
                    ->searchable()
                    ->placeholder('—')
                    ->icon('heroicon-o-rectangle-stack')
                    ->toggleable(),

                TextColumn::make('parent_id')
                    ->label('Übergeordnet')
                    ->badge()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('parent_id', $direction)->orderBy('id', 'desc');
                    })
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

                SelectFilter::make('group_id')
                    ->label('Gruppe')
                    ->relationship('group', 'name'),

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
                            ->withFilename(fn () => 'transactions-'.date('Y-m-d'))
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
                                'transactions-overview-'.date('Y-m-d').'.csv',
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
                            }, 'transactions-overview-'.date('Y-m-d').'.pdf');
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
                            }, 'transactions-detailed-'.date('Y-m-d').'.pdf');
                        }),
                ])
                    ->label('Export Übersicht')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->button(),
                BulkActionGroup::make([
                    BulkAction::make('assign_to_group')
                        ->label('Zu Gruppe hinzufügen')
                        ->icon('heroicon-o-rectangle-stack')
                        ->color('info')
                        ->schema([
                            Select::make('group_id')
                                ->label('Gruppe auswählen')
                                ->relationship('group', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Gruppe auswählen...')
                                ->required()
                                ->createOptionForm([
                                    \Filament\Forms\Components\TextInput::make('name')
                                        ->label('Gruppenname')
                                        ->required()
                                        ->maxLength(255),
                                    \Filament\Forms\Components\Textarea::make('description')
                                        ->label('Beschreibung')
                                        ->rows(3)
                                        ->nullable(),
                                    \Filament\Forms\Components\ColorPicker::make('color')
                                        ->label('Farbe')
                                        ->default('#3B82F6')
                                        ->required(),
                                ])
                                ->createOptionUsing(function (array $data) {
                                    return \App\Models\Group::create($data)->id;
                                }),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function ($transaction) use ($data) {
                                $transaction->update(['group_id' => $data['group_id']]);
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Erfolgreich!')
                                ->body(count($records).' Transaktionen wurden zur Gruppe hinzugefügt.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('remove_from_group')
                        ->label('Aus Gruppe entfernen')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Aus Gruppe entfernen')
                        ->modalDescription('Sind Sie sicher, dass Sie die ausgewählten Transaktionen aus ihrer Gruppe entfernen möchten?')
                        ->modalSubmitActionLabel('Entfernen')
                        ->action(function (Collection $records): void {
                            $records->each(function ($transaction) {
                                $transaction->update(['group_id' => null]);
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Erfolgreich!')
                                ->body(count($records).' Transaktionen wurden aus ihrer Gruppe entfernt.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    ExportBulkAction::make()
                        ->label('Export Ausgewählte')
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'transactions-selected-'.date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
