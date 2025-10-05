<?php

namespace App\Filament\Resources\Entities\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class EntityForm
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
            ]);
    }
}
