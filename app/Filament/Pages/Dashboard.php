<?php

namespace App\Filament\Pages;

use App\Livewire\StatsOverview;
use App\Livewire\BlogPostsChart;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            BlogPostsChart::class,
        ];
    }
}
