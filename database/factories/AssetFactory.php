<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => fake()->sentence(3),
            'serial_number' => fake()->unique()->bothify('SN-########'),
            'asset_code' => fake()->unique()->bothify('ASSET-####'),
            'expiry_date' => now()->addMonths(fake()->numberBetween(1, 24)),
            'status' => fake()->randomElement(['available', 'assigned', 'maintenance']),
        ];
    }
}