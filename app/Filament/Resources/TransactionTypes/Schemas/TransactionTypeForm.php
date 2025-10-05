<?php

namespace App\Filament\Resources\TransactionTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class TransactionTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                Select::make('color')
                    ->label('Badge Color')
                    ->options([
                        'primary' => 'Primary (Blue)',
                        'secondary' => 'Secondary (Gray)',
                        'success' => 'Success (Green)',
                        'danger' => 'Danger (Red)',
                        'warning' => 'Warning (Orange)',
                        'info' => 'Info (Light Blue)',
                        'gray' => 'Gray',
                    ])
                    ->default('gray')
                    ->required(),
            ]);
    }
}
