<?php

namespace Tests\Feature;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Classroom;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_classroom_select_uses_a_non_null_label(): void
    {
        $building = Building::factory()->create();
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'room_number' => 'A-101',
            'code' => 'a-101',
            'floor' => 1,
        ]);

        $schema = AssetResource::form(Schema::make()->model(Asset::class));
        $component = collect($schema->getComponents())
            ->first(fn ($component) => $component instanceof Select && $component->getName() === 'classroom_id');

        $this->assertNotNull($component);
        $this->assertSame('A-101', $component->getOptions()[$classroom->getKey()]);
    }

    public function test_asset_code_is_generated_and_unique(): void
    {
        $firstAsset = Asset::factory()->create();
        $secondAsset = Asset::factory()->create();

        $this->assertMatchesRegularExpression('/^AST-[0-9]{3}$/', $firstAsset->asset_code);
        $this->assertMatchesRegularExpression('/^AST-[0-9]{3}$/', $secondAsset->asset_code);
        $this->assertNotSame($firstAsset->asset_code, $secondAsset->asset_code);
    }
}
