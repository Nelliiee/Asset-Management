<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClassroomSchemaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_classrooms_table_has_required_fields(): void
    {
        $this->assertTrue(Schema::hasTable('classrooms'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'building_id'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'room_number'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'code'));
    }

    public function test_classroom_code_keeps_building_code_prefix_and_becomes_unique_when_needed(): void
    {
        $building = Building::factory()->create([
            'code' => 'IK',
        ]);

        Classroom::factory()->create([
            'building_id' => $building->id,
            'code' => 'IK',
            'room_number' => '501A',
            'floor' => 5,
        ]);

        $classroom = Classroom::create([
            'building_id' => $building->id,
            'room_number' => '503B',
            'code' => 'IK',
            'floor' => 5,
            'is_active' => true,
        ]);

        $this->assertSame('IK-1', $classroom->code);
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'code' => 'IK-1']);
    }
}
