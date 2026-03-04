<?php

namespace App\Filament\Resources\BudgetCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'budget_categories', column: 'slug', ignoreRecord: true),

                Select::make('category')
                    ->label('Hauptkategorie')
                    ->options([
                        'Einnahmen' => 'Einnahmen',
                        'Ausgaben' => 'Ausgaben',
                    ])
                    ->required(),

                TextInput::make('subcategory')
                    ->label('Unterkategorie')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
