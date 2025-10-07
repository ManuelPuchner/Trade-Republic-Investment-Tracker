<?php

namespace App\Filament\Resources\Entities\Pages;

use App\Models\Entity;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Entities\EntityResource;

class ListEntities extends ListRecords
{
    protected static string $resource = EntityResource::class;

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
                ->badge(fn () => Entity::count()),
            
            'etf' => Tab::make('ETF')
                ->icon('heroicon-o-chart-bar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'ETF'))
                ->badge(fn () => Entity::where('type', 'ETF')->count())
                ->badgeColor('info'),
            
            'company' => Tab::make('Unternehmen')
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'Company'))
                ->badge(fn () => Entity::where('type', 'Company')->count())
                ->badgeColor('success'),
            
            'person' => Tab::make('Personen')
                ->icon('heroicon-o-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'Person'))
                ->badge(fn () => Entity::where('type', 'Person')->count())
                ->badgeColor('warning'),
        ];
    }
}
