<?php

namespace App\Filament\Imports;

use App\Models\Report;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ReportImporter extends Importer
{
    protected static ?string $model = Report::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('asset')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('reporter')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Report
    {
        return new Report;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your report import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
