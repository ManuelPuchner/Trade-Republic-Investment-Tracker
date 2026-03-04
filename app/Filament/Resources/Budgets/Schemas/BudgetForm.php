<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use App\Models\Category;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategorie')
                    ->options(function () {
                        $options = [];
                        
                        // Nur Ausgaben-Kategorien für Budgets anzeigen
                        Category::expenseCategories()
                            ->orderBy('category')
                            ->orderBy('subcategory')
                            ->orderBy('name')
                            ->get()
                            ->each(function ($category) use (&$options) {
                                // Gruppierung nach Hauptkategorie und Unterkategorie
                                if ($category->category && $category->subcategory) {
                                    $key = "{$category->category} > {$category->subcategory}";
                                } elseif ($category->category) {
                                    $key = $category->category;
                                } else {
                                    $key = "Sonstiges";
                                }
                                
                                $options[$key][$category->id] = $category->name;
                            });
                        
                        return $options;
                    })
                    ->required()
                    ->searchable()
                    ->columnSpanFull()
                    ->prefixIcon('heroicon-o-tag')
                    ->helperText('Nur Ausgaben-Kategorien können budgetiert werden'),

                TextInput::make('amount')
                    ->label('Budget-Betrag')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->required()
                    ->minValue(0)
                    ->prefixIcon('heroicon-o-currency-euro')
                    ->helperText('Gesamtbudget für diese Kategorie'),

                Select::make('period')
                    ->label('Zeitraum')
                    ->options([
                        'monthly' => 'Monatlich',
                        'quarterly' => 'Quartalsweise',
                        'yearly' => 'Jährlich',
                    ])
                    ->default('monthly')
                    ->required()
                    ->prefixIcon('heroicon-o-calendar')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Monat auf null setzen für jährliche Budgets
                        if ($state === 'yearly') {
                            $set('month', null);
                        } elseif (empty($state)) {
                            $set('month', now()->month);
                        }
                    }),

                TextInput::make('month')
                    ->label('Monat')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->default(now()->month)
                    ->helperText('1-12. Erforderlich für monatliche und quartalsweise Budgets')
                    ->prefixIcon('heroicon-o-calendar')
                    ->required(fn ($get) => in_array($get('period'), ['monthly', 'quarterly']))
                    ->visible(fn ($get) => $get('period') !== 'yearly'),

                TextInput::make('year')
                    ->label('Jahr')
                    ->numeric()
                    ->minValue(2000)
                    ->default(now()->year)
                    ->required()
                    ->prefixIcon('heroicon-o-calendar'),

                Textarea::make('notes')
                    ->label('Notizen')
                    ->columnSpanFull()
                    ->rows(3)
                    ->helperText('Optionale Anmerkungen zu diesem Budget'),

                DatePicker::make('valid_from')
                    ->label('Gültig ab')
                    ->hint('Datum, ab dem dieses Budget aktiv ist')
                    ->helperText('Leer lassen für unbegrenzten Gültigkeitsbeginn')
                    ->prefixIcon('heroicon-o-calendar')
                    ->columnSpanFull(),

                DatePicker::make('valid_until')
                    ->label('Gültig bis')
                    ->hint('Datum, bis zu dem dieses Budget aktiv ist')
                    ->helperText('Leer lassen für unbegrenzten Gültigkeitszeitraum (Budget bleibt aktiv)')
                    ->prefixIcon('heroicon-o-calendar')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}