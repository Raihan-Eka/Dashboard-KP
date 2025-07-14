<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RegionCitySeeder::class);
        $this->call(DashboardDataSeeder::class); // <-- Tambahkan baris ini jika Anda menggunakan DashboardDataSeeder
    }
}