<?php

namespace App\Filament\Resources\Transactions;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Finanz Management';

    protected static ?string $slug = 'transactions'; // <- this defines your route segment

    protected static ?string $recordTitleAttribute = 'date';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        // Ensure the relationship is loaded
        if (! $record->relationLoaded('entity')) {
            $record->load('entity');
        }

        $entityName = $record->entity?->name ?? 'No Entity';
        $date = $record->date ? \Carbon\Carbon::parse($record->date)->format('M j, Y') : 'No Date';

        return "{$entityName} - {$date}";
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        if (! $record->relationLoaded('entity')) {
            $record->load('entity');
        }

        $entityName = $record->entity?->name ?? 'No Entity';
        $date = $record->date ? \Carbon\Carbon::parse($record->date)->format('M j, Y') : 'No Date';

        return "{$entityName} - {$date}";
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
            'view' => ViewTransaction::route('/{record}'),
        ];
    }
}
