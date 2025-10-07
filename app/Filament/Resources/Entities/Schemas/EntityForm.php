<?php

namespace App\Filament\Resources\Entities\Schemas;

use App\Models\Entity;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
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
                    ->unique(ignoreRecord: true)
                    ->prefixIcon('heroicon-o-building-storefront'),

                Select::make('type')
                    ->label('Typ')
                    ->options(Entity::getTypes())
                    ->required()
                    ->default('Company')
                    ->prefixIcon('heroicon-o-tag')
                    ->native(false),
            ]);
    }
}
