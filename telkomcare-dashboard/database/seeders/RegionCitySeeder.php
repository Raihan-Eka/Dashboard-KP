<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\City;

class RegionCitySeeder extends Seeder
{
    public function run(): void
    {
        // Data TREG dan Kota
        $regions = [
            'TREG 1' => [
                'Banda Aceh', 'Lhokseumawe', 'Medan', 'Pematangsiantar', 'Padang', 'Bukittinggi',
                'Jambi', 'Pekanbaru', 'Dumai', 'Batam', 'Tanjung Pinang', 'Palembang',
                'Lubuklinggau', 'Bengkulu', 'Pangkal Pinang', 'Bandar Lampung'
            ],
            'TREG 2' => [
                'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur',
                'Bekasi', 'Depok', 'Bogor', 'Tangerang', 'Tangerang Selatan', 'Serang', 'Cilegon'
            ],
            'TREG 3' => [
                'Bandung', 'Cimahi', 'Sumedang', 'Subang', 'Sukabumi', 'Cianjur', 'Garut',
                'Tasikmalaya', 'Cirebon', 'Indramayu', 'Karawang', 'Purwakarta', 'Majalengka', 'Banjar'
            ],
            'TREG 4' => [
                'Semarang', 'Salatiga', 'Surakarta (Solo)', 'Yogyakarta', 'Magelang',
                'Purwokerto', 'Tegal', 'Pekalongan', 'Klaten', 'Kudus', 'Jepara', 'Cilacap', 'Banyumas', 'Kebumen'
            ],
            'TREG 5' => [
                'Surabaya', 'Sidoarjo', 'Malang', 'Batu', 'Pasuruan', 'Probolinggo', 'Jember',
                'Banyuwangi', 'Madiun', 'Kediri', 'Blitar', 'Denpasar', 'Singaraja',
                'Mataram (NTB)', 'Bima (NTB)', 'Kupang (NTT)', 'Maumere (NTT)'
            ],
            'TREG 6' => [
                'Pontianak (Kalbar)', 'Singkawang', 'Palangkaraya (Kalteng)', 'Banjarmasin (Kalsel)',
                'Banjarbaru', 'Samarinda (Kaltim)', 'Balikpapan', 'Tarakan (Kaltara)'
            ],
            'TREG 7' => [
                'Makassar', 'Parepare', 'Palu', 'Kendari', 'Gorontalo', 'Manado', 'Ambon',
                'Ternate', 'Jayapura', 'Sorong', 'Timika', 'Merauke'
            ],
        ];

        foreach ($regions as $regionName => $cities) {
            $region = Region::firstOrCreate(['name' => $regionName]);
            foreach ($cities as $cityName) {
                City::firstOrCreate(['name' => $cityName, 'region_id' => $region->id]);
            }
        }
        $this->command->info('Regions and Cities seeded successfully!');
    }
}