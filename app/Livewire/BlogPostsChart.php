<?php

namespace App\Livewire;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected ?string $heading = 'Asset Status Overview';

    protected array|string|int $columnSpan = 'full';

    protected function getData(): array
    {
        $totalAssets = Asset::query()->count();
        $availableAssets = Asset::query()->where('status', 'available')->count();
        $inUseAssets = Asset::query()->where('status', 'in_use')->count();
        $maintenanceAssets = Asset::query()->where('status', 'maintenance')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => [$totalAssets, $availableAssets, $inUseAssets, $maintenanceAssets],
                    'backgroundColor' => [
                        '#4F46E5',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                    ],
                    'borderColor' => [
                        '#4338CA',
                        '#059669',
                        '#D97706',
                        '#DC2626',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Total assets', 'Available', 'In use', 'Maintenance'],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
