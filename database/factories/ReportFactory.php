<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => \App\Models\Asset::factory(),
            'reporter_id' => \App\Models\User::factory(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'resolved']),
        ];
    }
}
