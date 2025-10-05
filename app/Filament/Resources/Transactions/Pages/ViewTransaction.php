<?php

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Entities\EntityResource;
use App\Filament\Resources\Transactions\TransactionResource;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();

        if (! $record->relationLoaded('entity')) {
            $record->load('entity');
        }

        $entityName = $record->entity?->name ?? 'No Entity';
        $date = $record->date ? \Carbon\Carbon::parse($record->date)->format('M j, Y') : 'No Date';

        return "{$entityName} - {$date}";
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Transaction ID - Full width for emphasis
                TextEntry::make('id')
                    ->label('Transaction ID')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => '#'.str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->columnSpan(2),

                // Amount - Full width for emphasis
                TextEntry::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->weight(FontWeight::Bold)
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->icon(fn ($state) => $state >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down')
                    ->columnSpan(2),

                // Date and Type - Side by side
                TextEntry::make('date')
                    ->label('Date')
                    ->date('F j, Y')
                    ->icon('heroicon-o-calendar-days')
                    ->color('gray'),

                TextEntry::make('type.name')
                    ->label('Transaction Type')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-tag'),

                // Entity - Full width
                TextEntry::make('entity.name')
                    ->label('Entity')
                    ->placeholder('No entity assigned')
                    ->icon('heroicon-o-building-office-2')
                    ->color('gray')
                    ->url(fn ($record) => $record->entity_id
                        ? EntityResource::getUrl('edit', ['record' => $record->entity_id])
                        : null
                    )
                    ->columnSpan(2),

                // Parent and Children - Side by side
                TextEntry::make('parent.id')
                    ->label('Parent Transaction')
                    ->placeholder('This is a root transaction')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? '#'.str_pad($state, 6, '0', STR_PAD_LEFT) : null)
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->url(fn ($record) => $record->parent_id
                        ? TransactionResource::getUrl('view', ['record' => $record->parent_id])
                        : null
                    ),

                TextEntry::make('children_count')
                    ->label('Child Transactions')
                    ->getStateUsing(fn ($record) => $record->children()->count())
                    ->formatStateUsing(fn (int $state): string => $state === 0 ? 'None' : $state.' child'.($state === 1 ? '' : 'ren'))
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'gray')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->hint(fn ($record) => $record->children()->count() > 0 ? 'Use header action to view children' : null),

                // Timestamps - Side by side
                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y \a\t g:i A')
                    ->icon('heroicon-o-plus-circle')
                    ->color('gray'),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y \a\t g:i A')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('viewChildren')
                ->label('View Child Transactions')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('success')
                ->visible(fn () => $this->getRecord()->children()->count() > 0)
                ->url(fn () => TransactionResource::getUrl('index').'?parent_id='.$this->getRecord()->id),
        ];
    }
}
