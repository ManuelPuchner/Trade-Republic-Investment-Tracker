<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Entity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Date')
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric(),
                Select::make('transaction_type_id')
                    ->label('Transaction Type')
                    ->relationship('type', 'name')
                    ->required(),
                Select::make('entity_id')
                    ->label('Entity')
                    ->relationship('entity', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Entity Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Entity::create($data)->getKey();
                    })
                    ->nullable(),
                Select::make('parent_id')
                    ->label('Parent Transaction')
                    ->relationship('parent', 'id')
                    ->searchable()
                    ->nullable()
                    ->helperText('Nur für SaveBack + Steuer'),
            ]);
    }
}
