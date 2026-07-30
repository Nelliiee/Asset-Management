<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Livewire\StatsOverview;
use ReflectionMethod;
use Tests\TestCase;

class DashboardStatsOverviewTest extends TestCase
{
    public function test_dashboard_registers_stats_overview_widget(): void
    {
        $dashboard = new Dashboard();
        $method = new ReflectionMethod(Dashboard::class, 'getHeaderWidgets');
        $method->setAccessible(true);

        $widgets = $method->invoke($dashboard);

        $this->assertSame([StatsOverview::class], $widgets);
    }
}
