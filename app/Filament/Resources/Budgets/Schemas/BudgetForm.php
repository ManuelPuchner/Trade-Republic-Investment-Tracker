<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\BudgetCategory;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('budget_category_id')
                    ->label('Kategorie')
                    ->options(function () {
                        $options = [];
                        BudgetCategory::query()
                            ->orderBy('category')
                            ->orderBy('subcategory')
                            ->orderBy('name')
                            ->get()
                            ->each(function ($category) use (&$options) {
                                $key = "{$category->category} > {$category->subcategory}";
                                $options[$key][$category->id] = $category->name;
                            });
                        return $options;
                    })
                    ->required()
                    ->searchable()
                    ->columnSpanFull()
                    ->prefixIcon('heroicon-o-tag'),

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
                    ->prefixIcon('heroicon-o-calendar'),

                TextInput::make('month')
                    ->label('Monat (optional)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->helperText('1-12. Erforderlich für monatliche und quartalsweise Budgets')
                    ->prefixIcon('heroicon-o-calendar'),

                TextInput::make('year')
                    ->label('Jahr')
                    ->numeric()
                    ->minValue(2000)
                    ->default(now()->year)
                    ->prefixIcon('heroicon-o-calendar'),

                Textarea::make('notes')
                    ->label('Notizen')
                    ->columnSpanFull()
                    ->rows(3)
                    ->helperText('Optionale Anmerkungen zu diesem Budget'),
            ])
            ->columns(2);
    }
}
