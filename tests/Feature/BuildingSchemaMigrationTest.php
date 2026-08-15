<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BuildingSchemaMigrationTest extends TestCase
{
    public function test_buildings_table_has_required_code_column(): void
    {
        Artisan::call('migrate:fresh');

        $this->assertTrue(Schema::hasTable('buildings'));
        $this->assertTrue(Schema::hasColumn('buildings', 'name'));
        $this->assertTrue(Schema::hasColumn('buildings', 'code'));
    }
}
