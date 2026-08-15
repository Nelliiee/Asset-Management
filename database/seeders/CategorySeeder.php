<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();

        $categories = [
            ['name' => 'Hardware', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Software', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'License', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Components', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Accessories', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Computer set', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Others', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];

        DB::table('categories')->insert($categories);
    }
}
