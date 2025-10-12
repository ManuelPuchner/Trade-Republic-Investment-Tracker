<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Account;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Group;
use App\Models\TransactionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Step 1: Date & Type Selection (always first)
                DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->prefixIcon('heroicon-o-calendar')
                    ->default(now())
                    ->columnSpan(1),

                Select::make('transaction_type_id')
                    ->label('Transaktionstyp')
                    ->relationship('type', 'name')
                    ->required()
                    ->prefixIcon('heroicon-o-document-text')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Clear dependent fields when type changes
                        $set('to_account_id', null);
                        $set('entity_id', null);
                        $set('category_id', null);
                    })
                    ->columnSpan(1),

                // Step 2: Account Selection (always required)
                Select::make('account_id')
                    ->label('Von Konto')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-wallet')
                    ->columnSpan(1),

                // Step 3: Amount (always required)
                TextInput::make('amount')
                    ->label('Betrag')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->prefixIcon('heroicon-o-currency-euro')
                    ->step(0.01)
                    ->columnSpan(1),

                // Step 4: Transfer Target Account (only for Transfer type - Ausgabe with to_account)
                Select::make('to_account_id')
                    ->label('Auf Konto (Überweisung)')
                    ->relationship('toAccount', 'name')
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-arrow-right-circle')
                    ->helperText('Nur ausfüllen bei Überweisungen zwischen Konten')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // If to_account is set, clear entity_id (transfer mode)
                        if ($state) {
                            $set('entity_id', null);
                        }
                    })
                    ->required(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);

                        // Required for Transfer type
                        return $type && $type->name === 'Transfer';
                    })
                    ->visible(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);

                        // Show for Transfer type (dedicated transfer type)
                        // OR for Ausgabe type (can be transfer or regular expense)
                        return $type && in_array($type->name, ['Transfer', 'Ausgabe']);
                    })
                    ->columnSpan(2),

                // Step 5: Entity/Description (for investment transactions, deposits, or non-transfer expenses)
                Select::make('entity_id')
                    ->label(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return 'Beschreibung';
                        }

                        $type = TransactionType::find($typeId);
                        if (! $type) {
                            return 'Beschreibung';
                        }

                        // For investment transactions: "Wertpapier"
                        if (in_array($type->name, ['Kauf', 'Verkauf', 'Dividenden', 'Save Back'])) {
                            return 'Wertpapier';
                        }

                        // For deposits: "Von wem / Beschreibung"
                        if ($type->name === 'Einzahlung') {
                            return 'Von wem / Beschreibung';
                        }

                        // For expenses: "Wo ausgegeben"
                        if ($type->name === 'Ausgabe') {
                            return 'Wo ausgegeben / Beschreibung';
                        }

                        // For transfers: "Beschreibung"
                        if ($type->name === 'Transfer') {
                            return 'Beschreibung (optional)';
                        }

                        return 'Beschreibung';
                    })
                    ->relationship('entity', 'name')
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-document-text')
                    ->helperText(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return 'Beschreibung der Transaktion';
                        }

                        $type = TransactionType::find($typeId);
                        if (! $type) {
                            return 'Beschreibung der Transaktion';
                        }

                        if (in_array($type->name, ['Kauf', 'Verkauf', 'Dividenden', 'Save Back'])) {
                            return 'Wähle das gehandelte Wertpapier';
                        }

                        if ($type->name === 'Einzahlung') {
                            return 'Von wem hast du das Geld erhalten?';
                        }

                        if ($type->name === 'Ausgabe') {
                            return 'Wo hast du das Geld ausgegeben? (z.B. McDonalds, Amazon, etc.)';
                        }

                        if ($type->name === 'Transfer') {
                            return 'Optionale Beschreibung für diese Überweisung';
                        }

                        return 'Beschreibung der Transaktion';
                    })
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Entity::create($data)->getKey();
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // If entity is set, clear to_account_id (expense mode, not transfer)
                        if ($state) {
                            $set('to_account_id', null);
                        }
                    })
                    ->required(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);
                        if (! $type) {
                            return false;
                        }

                        // Required for investment transactions
                        if (in_array($type->name, ['Kauf', 'Verkauf', 'Dividenden', 'Save Back'])) {
                            return true;
                        }

                        // For Ausgabe: required if NOT a transfer (no to_account_id)
                        if ($type->name === 'Ausgabe') {
                            return empty($get('to_account_id'));
                        }

                        // For Transfer: NOT required (optional description)
                        if ($type->name === 'Transfer') {
                            return false;
                        }

                        return false;
                    })
                    ->visible(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);
                        if (! $type) {
                            return false;
                        }

                        // Hide for Saveback Steuer (uses parent_id instead)
                        if ($type->name === 'Saveback Steuer') {
                            return false;
                        }

                        // Hide for Zinsen (no entity needed)
                        if ($type->name === 'Zinsen') {
                            return false;
                        }

                        // Show for Einzahlung
                        if ($type->name === 'Einzahlung') {
                            return true;
                        }

                        // For Transfer: show (as optional description field)
                        if ($type->name === 'Transfer') {
                            return true;
                        }

                        // For Ausgabe: show only if NOT a transfer
                        if ($type->name === 'Ausgabe') {
                            return empty($get('to_account_id'));
                        }

                        return true;
                    })
                    ->columnSpan(2),

                // Step 6: Category (for expenses/income, not for investments)
                Select::make('category_id')
                    ->label('Kategorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-tag')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Kategoriename')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Category::create($data)->getKey();
                    })
                    ->visible(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);
                        if (! $type) {
                            return false;
                        }

                        // Show for Ausgabe and Einzahlung (non-investment transactions)
                        // Hide for Transfer (not needed for transfers between accounts)
                        return in_array($type->name, ['Ausgabe', 'Einzahlung']);
                    })
                    ->columnSpan(2),

                // Step 7: Parent Transaction (only for Saveback Steuer)
                Select::make('parent_id')
                    ->label('Übergeordnete Save Back Transaktion')
                    ->relationship('parent', 'id')
                    ->searchable()
                    ->prefixIcon('heroicon-o-link')
                    ->helperText('Wähle die zugehörige Save Back Transaktion')
                    ->required(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);

                        return $type && $type->name === 'Saveback Steuer';
                    })
                    ->visible(function ($get) {
                        $typeId = $get('transaction_type_id');
                        if (! $typeId) {
                            return false;
                        }

                        $type = TransactionType::find($typeId);

                        return $type && $type->name === 'Saveback Steuer';
                    })
                    ->columnSpanFull(),

                // Step 8: Group (optional grouping for analysis)
                Select::make('group_id')
                    ->label('Gruppe')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-rectangle-stack')
                    ->helperText('Ordne diese Transaktion einer Gruppe für bessere Analyse zu')
                    ->nullable()
                    ->columnSpan(2),

                // Step 9: Notes (always optional, always at the end)
                Textarea::make('notes')
                    ->label('Notizen')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Zusätzliche Informationen zu dieser Transaktion')
                    ->nullable(),
            ])
            ->columns(2);
    }
}
