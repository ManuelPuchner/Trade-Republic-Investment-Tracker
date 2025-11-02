<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class KassenbestandWidget extends BaseWidget
{
    protected static ?int $sort = 1; // This will appear first
    
    protected function getStats(): array
    {
        // Calculate Kassenbestand
        $kassenbestand = $this->calculateKassenbestand();
        
        return [
            Stat::make('💰 Kassenbestand', '€'.number_format($kassenbestand, 2))
                ->description('Available cash balance')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($kassenbestand >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'col-span-full',
                ])
        ];
    }

    protected function getColumns(): int
    {
        return 1; // Full width
    }

    protected function calculateKassenbestand(): float
    {
        // Kassenbestand: Einzahlungen + Verkäufe + Zinsen + Dividenden - Käufe - Ausgaben - Saveback Steuer
        
        // Positive contributors (additions to cash)
        $einzahlungen = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Einzahlung');
        })->sum('amount');
        
        $verkaeufe = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Verkauf');
        })->sum('amount');
        
        $zinsen = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Zinsen');
        })->sum('amount');
        
        $dividenden = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Dividenden');
        })->sum('amount');
        
        // Negative contributors (reductions from cash)
        $kaeufe = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Kauf');
        })->sum('amount');
        
        $ausgaben = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Ausgabe');
        })->sum('amount');
        
        $savebackSteuer = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Saveback Steuer');
        })->sum('amount');

        $ausschuettungssteuer = Transaction::whereHas('type', function($query) {
            $query->where('name', 'Steuer (Ausschüttung/Ausschüttungsgleicher Ertrag)');
        })->sum('amount');
        
        // Calculate final balance
        $kassenbestand = $einzahlungen + $verkaeufe + $zinsen + $dividenden - $kaeufe - $ausgaben - $savebackSteuer - $ausschuettungssteuer;

        return $kassenbestand;
    }
}