<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();

        $brands = [
            ['name' => 'Asus', 'slug' => 'asus', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Dell', 'slug' => 'dell', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Lenovo', 'slug' => 'lenovo', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Apple', 'slug' => 'apple', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'HP', 'slug' => 'hp', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Acer', 'slug' => 'acer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'MSI', 'slug' => 'msi', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Razer', 'slug' => 'razer', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Samsung', 'slug' => 'samsung', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'LG', 'slug' => 'lg', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];

        DB::table('brands')->insert($brands);
    }
}