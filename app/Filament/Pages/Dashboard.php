<?php

namespace App\Filament\Pages;

use App\Livewire\BlogPostsChart;
use App\Livewire\StatsOverview;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            BlogPostsChart::class,
        ];
    }
}
