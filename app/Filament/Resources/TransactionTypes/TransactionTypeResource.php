<?php

namespace App\Filament\Resources\TransactionTypes;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\TransactionType;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\TransactionTypes\Pages\EditTransactionType;
use App\Filament\Resources\TransactionTypes\Pages\ListTransactionTypes;
use App\Filament\Resources\TransactionTypes\Pages\CreateTransactionType;
use App\Filament\Resources\TransactionTypes\Schemas\TransactionTypeForm;
use App\Filament\Resources\TransactionTypes\Tables\TransactionTypesTable;
use App\Filament\Resources\TransactionTypes\RelationManagers\TransactionsRelationManager;

class TransactionTypeResource extends Resource
{
    protected static ?string $model = TransactionType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TransactionTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionTypes::route('/'),
            'create' => CreateTransactionType::route('/create'),
            'edit' => EditTransactionType::route('/{record}/edit'),
        ];
    }
}
