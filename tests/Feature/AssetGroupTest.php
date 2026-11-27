<?php

namespace Tests\Feature;

use App\Filament\Resources\Assets\Pages\ManageAssets;
use App\Models\Asset;
use App\Models\AssetGroup;
use App\Models\Brand;
use App\Models\Building;
use App\Models\Category;
use App\Models\Classroom;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssetGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_asset_set_creates_its_five_components_in_the_selected_classroom(): void
    {
        $this->actingAs(User::factory()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $building = Building::factory()->create();
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'floor' => 1,
        ]);
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $componentNames = [
            'monitor' => 'Monitor',
            'system_unit' => 'System Unit',
            'keyboard' => 'Keyboard',
            'mouse' => 'Mouse',
            'ups' => 'UPS',
        ];
        $components = [];

        foreach (array_keys($componentNames) as $index => $key) {
            $components[$key] = [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'serial_number' => "SET-001-{$index}",
            ];
        }

        $assetsPage = Livewire::test(ManageAssets::class)
            ->callAction('createAssetGroup', [
                'name' => 'Computer Set 1',
                'classroom_id' => $classroom->id,
                'components' => $components,
            ])
            ->assertHasNoActionErrors();

        $assetGroup = AssetGroup::query()->with('assets')->sole();

        $this->assertSame('Computer Set 1', $assetGroup->name);
        $this->assertMatchesRegularExpression('/^ASG-[0-9]{6}$/', $assetGroup->code);
        $this->assertCount(5, $assetGroup->assets);
        $assetsPage->assertCanSeeTableRecords($assetGroup->assets);

        foreach ($componentNames as $key => $name) {
            $asset = $assetGroup->assets->firstWhere('name', $name);

            $this->assertNotNull($asset);
            $this->assertSame($classroom->id, $asset->classroom_id);
            $this->assertSame($building->id, $asset->building_id);
            $this->assertSame($components[$key]['serial_number'], $asset->serial_number);
            $this->assertSame('available', $asset->status);
        }

        $this->assertSame(5, Asset::query()->where('asset_group_id', $assetGroup->id)->count());
    }
}
