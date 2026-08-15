<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();

        $brands = [
            ['name' => 'Asus', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Dell', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Lenovo', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Apple', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'HP', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Acer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'MSI', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Razer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Samsung', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'LG', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];

        DB::table('brands')->insert($brands);
    }
}
