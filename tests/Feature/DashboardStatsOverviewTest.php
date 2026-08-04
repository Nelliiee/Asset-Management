<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Livewire\BlogPostsChart;
use App\Livewire\StatsOverview;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class DashboardStatsOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_registers_stats_overview_widget(): void
    {
        $dashboard = new Dashboard();
        $method = new ReflectionMethod(Dashboard::class, 'getHeaderWidgets');
        $method->setAccessible(true);

        $widgets = $method->invoke($dashboard);

        $this->assertSame([StatsOverview::class, BlogPostsChart::class], $widgets);
    }

    public function test_blog_posts_chart_uses_real_asset_status_counts(): void
    {
        $building = Building::factory()->create([
            'slug' => 'building-1',
        ]);
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'slug' => 'classroom-1',
            'floor' => 1,
        ]);

        Asset::factory()->create([
            'status' => 'available',
            'classroom_id' => $classroom->id,
            'building_id' => $building->id,
        ]);
        Asset::factory()->create([
            'status' => 'available',
            'classroom_id' => $classroom->id,
            'building_id' => $building->id,
        ]);
        Asset::factory()->create([
            'status' => 'in_use',
            'classroom_id' => $classroom->id,
            'building_id' => $building->id,
        ]);
        Asset::factory()->create([
            'status' => 'maintenance',
            'classroom_id' => $classroom->id,
            'building_id' => $building->id,
        ]);

        $widget = new BlogPostsChart();
        $method = new ReflectionMethod(BlogPostsChart::class, 'getData');
        $method->setAccessible(true);

        $data = $method->invoke($widget);

        $this->assertSame(['Total assets', 'Available', 'In use', 'Maintenance'], $data['labels']);
        $this->assertSame([4, 2, 1, 1], $data['datasets'][0]['data']);
    }
}
