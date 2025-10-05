<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use App\Models\PortfolioSetting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\KassenbestandWidget::class,
            \App\Filament\Widgets\PortfolioPerformanceWidget::class,
            \App\Filament\Widgets\TransactionTypeSummaryWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updatePortfolio')
                ->label('Update Portfolio Value')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->schema([
                    TextInput::make('portfolio_value')
                        ->label('New Portfolio Value')
                        ->numeric()
                        ->required()
                        ->default(PortfolioSetting::getCurrentPortfolioValue())
                        ->prefix('€')
                        ->minValue(0)
                        ->step(0.01),
                ])
                ->action(function (array $data): void {
                    PortfolioSetting::updateCurrentPortfolioValue($data['portfolio_value']);

                    Notification::make()
                        ->title('Portfolio value updated successfully')
                        ->success()
                        ->send();
                })
                ->after(function (): void {
                    // Refresh the page to update all widgets
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }
}
