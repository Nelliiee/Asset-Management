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
                        '#4e46e58a',
                        '#10b9818e',
                        '#f59f0b83',
                        '#ef444481',
                    ],
                    'borderColor' => [
                        '#3221e6',
                        '#06b47d',
                        '#D97706',
                        '#f01010',
                    ],
                    'borderWidth' => 2,
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
