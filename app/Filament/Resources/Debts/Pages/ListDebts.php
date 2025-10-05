<?php

namespace App\Filament\Resources\Debts\Pages;

use App\Filament\Resources\Debts\DebtResource;
use App\Models\Debt;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDebts extends ListRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'alle' => Tab::make('Alle')
                ->icon('heroicon-o-queue-list')
                ->badge(fn () => Debt::count()),
            
            'unbezahlt' => Tab::make('Unbezahlt')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', false))
                ->badge(fn () => Debt::where('is_paid', false)->count())
                ->badgeColor('danger'),
            
            'bezahlt' => Tab::make('Bezahlt')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', true))
                ->badge(fn () => Debt::where('is_paid', true)->count())
                ->badgeColor('success'),
        ];
    }
}
