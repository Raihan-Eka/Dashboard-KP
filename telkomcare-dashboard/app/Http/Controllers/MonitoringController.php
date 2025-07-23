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
        // 1. Ambil filter tanggal dari request. Defaultnya null.
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // 2. Query data mentah dari wifi_tickets_raw
        $query = DB::table('wifi_tickets_raw');

        // 3. Terapkan filter tanggal HANYA JIKA ADA
        if ($filters['start_date'] && $filters['end_date']) {
            // Asumsi kolom tanggal di tabel mentah bernama 'Reported_Date'
            $query->whereBetween('Reported_Date', [$filters['start_date'], $filters['end_date']]);
        }

        // 4. Lakukan agregasi dan pengelompokan
        $flatData = $query->select(
                DB::raw("TRIM(Regional) as regional"),
                DB::raw("TRIM(Witel) as witel"),
                DB::raw("TRIM(Workzone) as workzone"),
                DB::raw("SUM(IF(Compliance = 'COMPLY', 1, 0)) as comply"),
                DB::raw("SUM(IF(Compliance = 'NOT COMPLY', 1, 0)) as not_comply")
            )
            ->whereNotNull('Regional')
            ->where('Regional', 'LIKE', 'REG-%')
            ->groupBy('regional', 'witel', 'workzone')
            ->get();

        // 5. Proses data menjadi struktur bertingkat
        $groupedByRegion = $flatData->groupBy('regional')->map(function ($regionItems, $regionName) {
            
            // Kelompokkan lagi data di dalam region berdasarkan witel
            // TAMBAHKAN 'use ($regionName)' DI SINI
            $witels = $regionItems->groupBy('witel')->map(function ($witelItems, $witelName) use ($regionName) {
                
                // Hitung total untuk witel ini
                $witelComply = $witelItems->sum('comply');
                $witelNotComply = $witelItems->sum('not_comply');
                $witelTotal = $witelComply + $witelNotComply;
                $witelTarget = ($regionName >= 'REG-6') ? 79.00 : 94.00; // Variabel $regionName sekarang bisa diakses
                $witelCompliance = ($witelTotal > 0) ? ($witelComply / $witelTotal) * 100 : 0;
                $witelAchv = ($witelTarget > 0) ? ($witelCompliance / $witelTarget) * 100 : 0;

                // Format data workzone
                $workzones = $witelItems->map(function ($item) use ($witelTarget) {
                    $item->total = $item->comply + $item->not_comply;
                    $item->target = $witelTarget;
                    $item->compliance_percentage = ($item->total > 0) ? ($item->comply / $item->total) * 100 : 0;
                    $item->achv_percentage = ($item->target > 0) ? ($item->compliance_percentage / $item->target) * 100 : 0;
                    return $item;
                });

                return [ 'name' => $witelName, 'summary' => [ 'target' => $witelTarget, 'comply' => $witelComply, 'not_comply' => $witelNotComply, 'total' => $witelTotal, 'compliance_percentage' => $witelCompliance, 'achv_percentage' => $witelAchv, ], 'workzones' => $workzones->values()];
            });

            $regionComply = $regionItems->sum('comply');
            $regionNotComply = $regionItems->sum('not_comply');
            $regionTotal = $regionComply + $regionNotComply;
            $regionTarget = ($regionName >= 'REG-6') ? 79.00 : 94.00;
            $regionCompliance = ($regionTotal > 0) ? ($regionComply / $regionTotal) * 100 : 0;
            $regionAchv = ($regionTarget > 0) ? ($regionCompliance / $regionTarget) * 100 : 0;

            return [ 'name' => $regionName, 'summary' => [ 'target' => $regionTarget, 'comply' => $regionComply, 'not_comply' => $regionNotComply, 'total' => $regionTotal, 'compliance_percentage' => $regionCompliance, 'achv_percentage' => $regionAchv, ], 'witels' => $witels->values()];
        });

        $nasionalComply = $flatData->sum('comply');
        $nasionalNotComply = $flatData->sum('not_comply');
        $nasionalTotal = $nasionalComply + $nasionalNotComply;
        $nasionalTarget = 89.00;
        $nasionalCompliance = ($nasionalTotal > 0) ? ($nasionalComply / $nasionalTotal) * 100 : 0;
        $nasionalAchv = ($nasionalTarget > 0) ? ($nasionalCompliance / $nasionalTarget) * 100 : 0;

        $nasionalSummary = [ 'target' => $nasionalTarget, 'comply' => $nasionalComply, 'not_comply' => $nasionalNotComply, 'total' => $nasionalTotal, 'compliance_percentage' => $nasionalCompliance, 'achv_percentage' => $nasionalAchv, ];

        return view('monitoring.page_wifi', [
            'dataRegions' => $groupedByRegion->sortBy('name')->values(),
            'dataNasional' => $nasionalSummary,
            'filters' => $filters,
        ]);
    }
    /**
     * Method baru untuk menangani download data mentah.
     */
    public function downloadWifiRawData(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $fileName = 'raw_wifi_data.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response()->stream(function () use ($filters) {
            $file = fopen('php://output', 'w');
            
            // Ambil nama kolom dari tabel secara dinamis
            $columns = DB::getSchemaBuilder()->getColumnListing('wifi_tickets_raw');
            fputcsv($file, $columns);
            
            $query = DB::table('wifi_tickets_raw');

            if ($filters['start_date'] && $filters['end_date']) {
                $query->whereBetween('Reported_Date', [$filters['start_date'], $filters['end_date']]);
            }

            // Ambil data per-bagian (chunk) agar tidak memberatkan memori
            $query->chunk(500, function($data) use ($file) {
                foreach ($data as $row) {
                    fputcsv($file, (array) $row);
                }
            });

            fclose($file);
        }, 200, $headers);
    }
    public function pageHsi(Request $request)
    {
        // Ambil filter dari request. Defaultnya sekarang null (tidak ada filter).
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // Mulai query dasar tanpa filter tanggal
        $queryHsi = HsiTicket::query();

        // HANYA terapkan filter tanggal JIKA kedua tanggal diisi oleh pengguna
        if ($filters['start_date'] && $filters['end_date']) {
            $queryHsi->whereBetween('ticket_date', [$filters['start_date'], $filters['end_date']]);
        }

        // Lanjutan query untuk select dan group by tidak berubah
        $dataHsi = $queryHsi->select(
                'treg',
                DB::raw('SUM(CASE WHEN category = "4H" AND status = "Comply" THEN 1 ELSE 0 END) as h4_comply'),
                DB::raw('SUM(CASE WHEN category = "4H" AND status = "Not Comply" THEN 1 ELSE 0 END) as h4_not_comply'),
                DB::raw('AVG(CASE WHEN category = "4H" THEN target_percentage ELSE NULL END) as h4_target'),
                DB::raw('SUM(CASE WHEN category = "24H" AND status = "Comply" THEN 1 ELSE 0 END) as h24_comply'),
                DB::raw('SUM(CASE WHEN category = "24H" AND status = "Not Comply" THEN 1 ELSE 0 END) as h24_not_comply'),
                DB::raw('AVG(CASE WHEN category = "24H" THEN target_percentage ELSE NULL END) as h24_target'),
                DB::raw('COUNT(id) as total_tiket')
            )
            ->groupBy('treg')
            ->orderBy('treg')
            ->get()
            ->map(function ($item) {
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