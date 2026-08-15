<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_number' => fake()->text(20),
            'code' => fake()->unique()->word(),
            'is_active' => fake()->boolean(),
            'building_id' => Building::factory(),
        ];
    }
}
