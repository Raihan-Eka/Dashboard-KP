<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\DashboardData;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        $cities = City::all(); // Ambil semua kota

        if ($cities->isEmpty()) {
            $this->command->info('Tidak ada kota di database. Jalankan RegionCitySeeder terlebih dahulu.');
            return;
        }

        $sampleCities = $cities->take(5); // Ambil beberapa kota untuk contoh data

        foreach ($sampleCities as $city) {
            foreach (['K1', 'K2', 'K3'] as $category) {
                $today = Carbon::today();
                $yesterday = Carbon::yesterday();

                // Data untuk hari ini
                DashboardData::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'category' => $category,
                        'entry_date' => $today,
                    ],
                    [
                        'sid' => rand(50, 200),
                        'comply' => rand(40, 180),
                        'not_comply' => rand(5, 20),
                        'total' => 0, // Akan dihitung di controller
                        'target' => 95.00,
                        'ttr_comply' => rand(8000, 15000),
                        'achievement' => 0, // Akan dihitung
                        'ticket_count' => 1, // Setiap submit dianggap 1 tiket
                    ]
                );

                // Data untuk kemarin
                DashboardData::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'category' => $category,
                        'entry_date' => $yesterday,
                    ],
                    [
                        'sid' => rand(30, 150),
                        'comply' => rand(25, 130),
                        'not_comply' => rand(3, 15),
                        'total' => 0,
                        'target' => 92.50,
                        'ttr_comply' => rand(7000, 12000),
                        'achievement' => 0,
                        'ticket_count' => 1, // Setiap submit dianggap 1 tiket
                    ]
                );
            }
        }
        $this->command->info('Data dummy DashboardData berhasil ditambahkan!');
    }
}