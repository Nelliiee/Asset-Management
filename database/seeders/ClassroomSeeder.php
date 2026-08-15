<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Building;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();
        $now = Carbon::now();

        $classrooms = [
            ['building_id' => $buildings[0]->id, 'floor' => 1, 'room_number' => 'Classroom 1', 'code' => Str::slug('Classroom 1'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['building_id' => $buildings[0]->id, 'floor' => 2, 'room_number' => 'Classroom 2', 'code' => Str::slug('Classroom 2'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['building_id' => $buildings[1]->id, 'floor' => 1, 'room_number' => 'Classroom 3', 'code' => Str::slug('Classroom 3'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['building_id' => $buildings[1]->id, 'floor' => 2, 'room_number' => 'Classroom 4', 'code' => Str::slug('Classroom 4'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['building_id' => $buildings[2]->id, 'floor' => 1, 'room_number' => 'Classroom 5', 'code' => Str::slug('Classroom 5'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];


        DB::table('classrooms')->insert($classrooms);
    }
}