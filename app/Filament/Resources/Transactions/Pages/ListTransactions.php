<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Models\Account;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Transactions\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function mount(): void
    {
        parent::mount();

        // Check if there's a parent_id parameter in the URL
        if (request()->has('parent_id')) {
            $parentId = request()->get('parent_id');

            // Set the table filter programmatically
            $this->tableFilters = [
                'parent_id' => ['value' => $parentId],
            ];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Alle Transaktionen')
                ->badge(fn () => \App\Models\Transaction::count()),
        ];

        // Add a tab for each account
        $accounts = Account::orderBy('name')->get();

        foreach ($accounts as $account) {
            $tabs['account_'.$account->id] = Tab::make($account->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_id', $account->id))
                ->badge(fn () => $account->transactions()->count());
        }

        return $tabs;
    }
}
