<?php

namespace App\Http\Controllers;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
// Asumsikan Anda memiliki model untuk data tiket, jika belum ada, Anda perlu membuatnya.
// Contoh: php artisan make:model WifiTicket
// Contoh: php artisan make:model HsiTicket
use App\Models\WifiTicket; 
use App\Models\HsiTicket;

class MonitoringController extends Controller
{
    /**
     * Menampilkan halaman pemantauan TTR Compliance Wifi.
     */
    public function pageWifi(Request $request)
    {
        // GANTI DENGAN LOGIKA DATABASE ANDA YANG SEBENARNYA
        // Ini adalah contoh query untuk mengambil dan mengagregasi data Wifi
        // Sesuaikan nama model, kolom, dan logika sesuai kebutuhan.
        $dataWifi = WifiTicket::select(
                'regional',
                DB::raw('AVG(target_percentage) as target'), // Asumsi target adalah rata-rata
                DB::raw('SUM(CASE WHEN status = "Comply" THEN 1 ELSE 0 END) as comply'),
                DB::raw('SUM(CASE WHEN status = "Not Comply" THEN 1 ELSE 0 END) as not_comply'),
                DB::raw('COUNT(id) as total')
            )
            ->groupBy('regional')
            ->orderBy('regional')
            ->get()
            ->map(function ($item) {
                // Kalkulasi tambahan di level collection
                $item->compliance = ($item->total > 0) ? ($item->comply / $item->total) * 100 : 0;
                $item->achv = ($item->target > 0) ? ($item->compliance / $item->target) * 100 : 0;
                return $item;
            });
        
        // Anda juga bisa menambahkan baris 'NASIONAL' secara manual setelah query
        // dengan menjumlahkan semua hasil dari $dataWifi.

        return view('monitoring.page_wifi', ['dataWifi' => $dataWifi]);
    }

    /**
     * Menampilkan halaman pemantauan Resume TTR INDIBIZ (HSI).
     */
    public function pageHsi(Request $request)
    {
        // Ambil filter dari request, sama seperti di DashboardController Anda
        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->input('end_date', now()->endOfMonth()->format('Y-m-d')),
        ];

        // GANTI DENGAN LOGIKA DATABASE ANDA YANG SEBENARNYA
        // Ini adalah contoh query dinamis berdasarkan filter tanggal.
        $queryHsi = HsiTicket::query()
            ->whereBetween('ticket_date', [$filters['start_date'], $filters['end_date']]);

        $dataHsi = $queryHsi->select(
                'treg', // Asumsi nama kolom adalah 'treg'
                // Logika untuk 4H
                DB::raw('SUM(CASE WHEN category = "4H" AND status = "Comply" THEN 1 ELSE 0 END) as h4_comply'),
                DB::raw('SUM(CASE WHEN category = "4H" AND status = "Not Comply" THEN 1 ELSE 0 END) as h4_not_comply'),
                DB::raw('AVG(CASE WHEN category = "4H" THEN target_percentage ELSE NULL END) as h4_target'),
                // Logika untuk 24H
                DB::raw('SUM(CASE WHEN category = "24H" AND status = "Comply" THEN 1 ELSE 0 END) as h24_comply'),
                DB::raw('SUM(CASE WHEN category = "24H" AND status = "Not Comply" THEN 1 ELSE 0 END) as h24_not_comply'),
                DB::raw('AVG(CASE WHEN category = "24H" THEN target_percentage ELSE NULL END) as h24_target'),
                DB::raw('COUNT(id) as total_tiket')
            )
            ->groupBy('treg')
            ->orderBy('treg')
            ->get()
            ->map(function ($item) {
                // Proses kalkulasi akhir untuk setiap baris
                $h4_total = $item->h4_comply + $item->h4_not_comply;
                $item->h4_real = ($h4_total > 0) ? ($item->h4_comply / $h4_total) * 100 : 0;
                $item->h4_ach = ($item->h4_target > 0) ? ($item->h4_real / $item->h4_target) * 100 : 0;
                
                $h24_total = $item->h24_comply + $item->h24_not_comply;
                $item->h24_real = ($h24_total > 0) ? ($item->h24_comply / $h24_total) * 100 : 0;
                $item->h24_ach = ($item->h24_target > 0) ? ($item->h24_real / $item->h24_target) * 100 : 0;
                
                return $item;
            });


        return view('monitoring.page_hsi', [
            'dataHsi' => $dataHsi,
            'filters' => $filters
        ]);
    }
}