<?php

namespace Tests\Feature;

use App\Filament\Resources\Assets\Pages\ManageAssets;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Building;
use App\Models\Category;
use App\Models\Classroom;
use App\Models\Report;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssetMaintenanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_asset_for_maintenance_creates_a_linked_report(): void
    {
        $reporter = User::factory()->create();
        $this->actingAs($reporter);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $building = Building::factory()->create();
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'floor' => 1,
        ]);
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        Livewire::test(ManageAssets::class)
            ->callAction('create', [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => 'Maintenance laptop',
                'serial_number' => 'MAINT-001',
                'classroom_id' => $classroom->id,
                'building_id' => $building->id,
                'status' => 'maintenance',
            ])
            ->assertHasNoActionErrors();

        $asset = Asset::query()->where('serial_number', 'MAINT-001')->firstOrFail();
        $report = $asset->reports()->sole();

        $this->assertSame($asset->id, $report->asset_id);
        $this->assertSame($reporter->id, $report->reporter_id);
        $this->assertSame('pending', $report->status);
        $this->assertSame(
            "Asset {$asset->asset_code} (Maintenance laptop) requires maintenance.",
            $report->description,
        );
    }

    public function test_creating_an_asset_not_for_maintenance_does_not_create_a_report(): void
    {
        $this->actingAs(User::factory()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $building = Building::factory()->create();
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'floor' => 1,
        ]);

        Livewire::test(ManageAssets::class)
            ->callAction('create', [
                'category_id' => Category::factory()->create()->id,
                'brand_id' => Brand::factory()->create()->id,
                'name' => 'Available laptop',
                'serial_number' => 'AVAILABLE-001',
                'classroom_id' => $classroom->id,
                'building_id' => $building->id,
                'status' => 'available',
            ])
            ->assertHasNoActionErrors();

        $asset = Asset::query()->where('serial_number', 'AVAILABLE-001')->firstOrFail();

        $this->assertSame(0, Report::query()->where('asset_id', $asset->id)->count());
    }
}
