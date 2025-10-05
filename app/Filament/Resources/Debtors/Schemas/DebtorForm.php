<?php

namespace App\Filament\Resources\Debtors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DebtorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('email')
                    ->label('E-Mail')
                    ->email()
                    ->maxLength(255),
                    
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(255),
                    
                Textarea::make('notes')
                    ->label('Notizen')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
