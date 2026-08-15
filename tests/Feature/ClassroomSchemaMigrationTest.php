<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClassroomSchemaMigrationTest extends TestCase
{
    public function test_classrooms_table_has_required_fields(): void
    {
        Artisan::call('migrate:fresh');

        $this->assertTrue(Schema::hasTable('classrooms'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'building_id'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'room_number'));
        $this->assertTrue(Schema::hasColumn('classrooms', 'code'));
    }
}
