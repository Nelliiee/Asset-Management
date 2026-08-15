<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetTag;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetTag>
 */
class AssetTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'tag_id' => Tag::factory(),
        ];
    }
}
