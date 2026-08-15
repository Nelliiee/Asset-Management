<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $buildings = [
            ['name' => 'building 1', 'code' => Str::slug('building 1'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'building 2', 'code' => Str::slug('building 2'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'building 3', 'code' => Str::slug('building 3'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'building 4', 'code' => Str::slug('building 4'), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('buildings')->insert($buildings);
    }
}