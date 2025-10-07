<?php

namespace App\Filament\Resources\Debts\Tables;

use App\Models\Debt;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

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
                    ->color(fn ($record) => $record->is_paid ? 'success' : 'warning')
                    ->summarize([
                        Sum::make()
                            ->label('Gesamt')
                            ->money('EUR')
                            ->formatStateUsing(fn ($state) => '€ '.number_format($state, 2, ',', '.')),
                    ]),

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
                    ])
                    ->summarize([
                        Count::make()
                            ->label('Anzahl'),
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
                ExportAction::make()
                    ->label('Export CSV')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'debts-'.date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('debtor.name')->heading('Schuldner'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('amount')->heading('Betrag'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('description')->heading('Beschreibung'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('is_paid')->heading('Status')->formatStateUsing(fn ($state) => $state ? 'Bezahlt' : 'Offen'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('payment_method')->heading('Zahlungsart'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('account.name')->heading('Konto'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('paid_at')->heading('Bezahlt am'),
                            ]),
                    ]),
                \Filament\Actions\ActionGroup::make([
                    Action::make('exportOverviewCSV')
                        ->label('Export als CSV')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $debts = $query->with(['debtor', 'account'])->get();
                            
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\DebtsOverviewExport($debts),
                                'debts-overview-' . date('Y-m-d') . '.csv',
                                \Maatwebsite\Excel\Excel::CSV
                            );
                        }),
                    Action::make('exportOverviewPDF')
                        ->label('Export als PDF')
                        ->icon('heroicon-o-document')
                        ->color('warning')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $debts = $query->with(['debtor', 'account'])->get();
                            
                            // Calculate statistics
                            $totalDebts = $debts->count();
                            $totalAmount = $debts->sum('amount');
                            $avgAmount = $totalDebts > 0 ? $totalAmount / $totalDebts : 0;
                            $maxAmount = $debts->max('amount') ?? 0;
                            $minAmount = $debts->min('amount') ?? 0;
                            
                            // Open vs Paid debts
                            $openDebts = $debts->where('is_paid', false);
                            $paidDebts = $debts->where('is_paid', true);
                            $openDebtsCount = $openDebts->count();
                            $paidDebtsCount = $paidDebts->count();
                            $openDebtsAmount = $openDebts->sum('amount');
                            $paidDebtsAmount = $paidDebts->sum('amount');
                            
                            // Group by debtor
                            $byDebtor = $debts->groupBy('debtor.name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                ];
                            })->toArray();
                            
                            // Group by type
                            $byType = $debts->groupBy('type')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum('amount'),
                                    'avg' => $group->avg('amount'),
                                ];
                            })->toArray();
                            
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.debts-overview-pdf', [
                                'totalDebts' => $totalDebts,
                                'totalAmount' => $totalAmount,
                                'avgAmount' => $avgAmount,
                                'maxAmount' => $maxAmount,
                                'minAmount' => $minAmount,
                                'openDebtsCount' => $openDebtsCount,
                                'paidDebtsCount' => $paidDebtsCount,
                                'openDebtsAmount' => $openDebtsAmount,
                                'paidDebtsAmount' => $paidDebtsAmount,
                                'byDebtor' => $byDebtor,
                                'byType' => $byType,
                            ]);
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'debts-overview-' . date('Y-m-d') . '.pdf');
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
                                ->withFilename(fn () => 'debts-selected-'.date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
