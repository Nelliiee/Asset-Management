<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Filament\Exports\ReportDocxExporter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Asset')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('asset.asset_code')
                    ->label('Asset Code')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('reporter.name')
                    ->label('Reporter')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title())
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                    ]),
                SelectFilter::make('asset')
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('reporter')
                    ->relationship('reporter', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('exportDocx')
                    ->label('Export DOCX')
                    ->color('info')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (HasTable $livewire, ReportDocxExporter $exporter) => $exporter->download(
                        $livewire->getTableQueryForExport(),
                    )),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->extraAttributes([
                        'onclick' => 'window.print(); return false;',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
