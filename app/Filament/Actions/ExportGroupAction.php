<?php

namespace App\Filament\Actions;

use App\Models\Group;
use App\Services\GroupExportService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ExportGroupAction
{
    public static function make(): Action
    {
        return Action::make('export')
            ->label('PDF Export')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (Group $record) {
                try {
                    $exportService = new GroupExportService();
                    $pdf = $exportService->exportToPdf($record);
                    
                    $filename = 'gruppe-' . Str::slug($record->name) . '-' . now()->format('Y-m-d') . '.pdf';
                    
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename, [
                        'Content-Type' => 'application/pdf',
                    ]);
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Export fehlgeschlagen')
                        ->body('Der PDF-Export konnte nicht erstellt werden: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}