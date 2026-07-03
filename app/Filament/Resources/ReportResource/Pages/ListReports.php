<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ListReports extends ManageRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_xlsx')
                ->label('Export XLSX')
                ->icon('heroicon-o-arrow-down-tray')
                ->url('/api/v1/reports/attendance/export?format=xlsx')
                ->openUrlInNewTab(),
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url('/api/v1/reports/attendance/export?format=csv')
                ->openUrlInNewTab(),
            Action::make('export_doc_xlsx')
                ->label('Export Dokumentasi XLSX')
                ->icon('heroicon-o-arrow-down-tray')
                ->url('/api/v1/reports/documentations/export?format=xlsx')
                ->openUrlInNewTab(),
        ];
    }
}
