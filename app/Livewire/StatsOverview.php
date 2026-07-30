<?php

namespace App\Livewire;

use App\Models\Asset;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalAssets = Asset::query()->count();
        $availableAssets = Asset::query()->where('status', 'available')->count();
        $inUseAssets = Asset::query()->where('status', 'in_use')->count();
        $maintenanceAssets = Asset::query()->where('status', 'maintenance')->count();

        return [
            Stat::make('Total assets', $totalAssets)
                ->description('All tracked assets')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('info'),
            Stat::make('Available', $availableAssets)
                ->description('Ready for use')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('In use', $inUseAssets)
                ->description('Currently assigned')
                ->descriptionIcon(Heroicon::OutlinedArrowPath)
                ->color('warning'),
            Stat::make('Maintenance', $maintenanceAssets)
                ->description('Needs attention')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger'),
        ];
    }
}
