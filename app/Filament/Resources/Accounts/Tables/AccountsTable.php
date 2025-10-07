<?php

namespace App\Filament\Resources\Accounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Actions\Action;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kontoname')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-credit-card')
                    ->weight('bold'),
                
                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-library'),
                
                TextColumn::make('account_type')
                    ->label('Typ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'checking' => '🏦 Girokonto',
                        'savings' => '💰 Sparkonto',
                        'investment' => '📈 Anlagekonto',
                        'cash' => '💵 Bargeld',
                        'other' => '📄 Sonstiges',
                        default => $state,
                    })
                    ->sortable(),
                
                TextColumn::make('initial_balance')
                    ->label('Anfangssaldo')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('current_balance')
                    ->label('Aktueller Saldo')
                    ->money('EUR')
                    ->weight('bold')
                    ->color(fn ($state): string => $state >= 0 ? 'success' : 'danger')
                    ->icon(fn ($state): string => $state >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                    ->sortable(),
                
                IconColumn::make('is_trade_republic')
                    ->label('Trade Republic')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                TextColumn::make('created_at')
                    ->label('Erstellt am')
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
                ExportAction::make()
                    ->label('Export CSV')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'accounts-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('name')->heading('Kontoname'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('bank_name')->heading('Bank'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('account_number')->heading('Kontonummer'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('account_type')->heading('Typ'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('initial_balance')->heading('Anfangssaldo'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('current_balance')->heading('Aktueller Saldo'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('is_trade_republic')->heading('Trade Republic'),
                            ]),
                    ]),
                \Filament\Actions\ActionGroup::make([
                    Action::make('exportOverviewCSV')
                        ->label('Export als CSV')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $accounts = $query->get();
                            
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\AccountsOverviewExport($accounts),
                                'accounts-overview-' . date('Y-m-d') . '.csv',
                                \Maatwebsite\Excel\Excel::CSV
                            );
                        }),
                    Action::make('exportOverviewPDF')
                        ->label('Export als PDF')
                        ->icon('heroicon-o-document')
                        ->color('warning')
                        ->action(function ($livewire) {
                            $query = $livewire->getFilteredTableQuery();
                            $accounts = $query->get();
                            
                            // Calculate statistics using current_balance accessor
                            $totalAccounts = $accounts->count();
                            $totalBalance = $accounts->sum(fn($account) => $account->current_balance);
                            $avgBalance = $totalAccounts > 0 ? $totalBalance / $totalAccounts : 0;
                            $balances = $accounts->pluck('current_balance');
                            $maxBalance = $balances->max() ?? 0;
                            $minBalance = $balances->min() ?? 0;
                            
                            // Group by type
                            $byType = $accounts->groupBy('account_type')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum(fn($account) => $account->current_balance),
                                    'avg' => $group->avg(fn($account) => $account->current_balance),
                                ];
                            })->toArray();
                            
                            // Group by bank
                            $byBank = $accounts->groupBy('bank_name')->map(function ($group) {
                                return [
                                    'count' => $group->count(),
                                    'sum' => $group->sum(fn($account) => $account->current_balance),
                                ];
                            })->toArray();
                            
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.accounts-overview-pdf', [
                                'totalAccounts' => $totalAccounts,
                                'totalBalance' => $totalBalance,
                                'avgBalance' => $avgBalance,
                                'maxBalance' => $maxBalance,
                                'minBalance' => $minBalance,
                                'byType' => $byType,
                                'byBank' => $byBank,
                            ]);
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'accounts-overview-' . date('Y-m-d') . '.pdf');
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
                                ->withFilename(fn () => 'accounts-selected-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
