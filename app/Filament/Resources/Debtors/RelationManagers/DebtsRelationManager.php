<?php

namespace App\Filament\Resources\Debtors\RelationManagers;

use App\Filament\Resources\Debts\DebtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DebtsRelationManager extends RelationManager
{
    protected static string $relationship = 'debts';

    protected static ?string $relatedResource = DebtResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
