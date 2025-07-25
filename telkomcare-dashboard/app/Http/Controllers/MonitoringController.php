<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\WifiTicket; // <-- 1. Impor Model WifiTicket
use App\Models\HsiTicket;

class MonitoringController extends Controller
{
    /**
     * Menampilkan halaman TTR Wifi dengan data yang diolah.
     */
    public function pageWifi(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // --- PERUBAHAN BARU: AMBIL TARGET DARI DATABASE ---
        // 1. Ambil semua target dari tabel wifi_summary dan jadikan array
        // Hasilnya akan seperti: ['REG 1' => 94.00, 'REG 6' => 79.00, 'NASIONAL' => 89.00]
        $targets = DB::table('wifi_summary')->pluck('Target', 'Regional');

        $query = WifiTicket::query();
        
        if ($filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('Reported_Date', [$filters['start_date'], $filters['end_date']]);
        }

        $allData = $query->get();

        // 2. Ambil target Nasional dari array $targets, beri nilai default 89 jika tidak ada
        $nasionalTarget = $targets->get('NASIONAL', 89.00);
        $dataNasional = $this->calculateComplianceSummary($allData, $nasionalTarget);

        // --- Logika di bawah ini sekarang menggunakan $targets dari database ---
        $dataRegions = $allData->groupBy('Regional')->map(function($regionItems, $regionName) use ($targets) {
            
            // 3. Ambil target untuk region saat ini dari array, beri nilai default 89 jika tidak ada
            $targetForThisRegion = $targets->get($regionName, 89.00);

            $witels = $regionItems->groupBy('Witel')->map(function($witelItems, $witelName) use ($targetForThisRegion) {
                
                $workzones = $witelItems->groupBy('Workzone')->map(function($workzoneItems, $workzoneName) use ($targetForThisRegion) {
                    return [
                        'workzone' => $workzoneName,
                        'summary' => $this->calculateComplianceSummary($workzoneItems, $targetForThisRegion)
                    ];
                })->sortBy('workzone');

                return [
                    'name' => $witelName,
                    'summary' => $this->calculateComplianceSummary($witelItems, $targetForThisRegion),
                    'workzones' => $workzones
                ];
            });

            return [
                'name' => $regionName,
                'summary' => $this->calculateComplianceSummary($regionItems, $targetForThisRegion),
                'witels' => $witels->sortBy('name')
            ];
        })->sortBy('name');

        return view('monitoring.wifi', compact('dataRegions', 'dataNasional', 'filters'));
    }
    /**
     * Helper function untuk menghitung summary compliance.
     */
    private function calculateComplianceSummary($collection, $target)
    {
        $comply = $collection->where('Compliance', 'COMPLY')->count();
        $notComply = $collection->where('Compliance', 'NOT COMPLY')->count();
        $total = $comply + $notComply;
        $compliancePercentage = ($total > 0) ? ($comply / $total) * 100 : 0;
        $achvPercentage = ($target > 0) ? ($compliancePercentage / $target) * 100 : 0;

        return [
            'target' => $target,
            'comply' => $comply,
            'not_comply' => $notComply,
            'total' => $total,
            'compliance_percentage' => $compliancePercentage,
            'achv_percentage' => $achvPercentage,
        ];
    }

    /**
     * Menangani download data mentah Wifi ke CSV.
     */
public function downloadWifiRawData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'raw_wifi_data';
        if ($startDate && $endDate) {
            $fileName .= "_from_{$startDate}_to_{$endDate}";
        }
        $fileName .= '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response()->stream(function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\BF");
            $columns = DB::getSchemaBuilder()->getColumnListing('wifi_tickets_raw');
            fputcsv($file, $columns, ';');

            $query = WifiTicket::query();

            if ($startDate && $endDate) {
                $query->whereBetween('Reported_Date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // HAPUS ->orderBy('id') DARI SINI
            foreach ($query->cursor() as $row) {
                fputcsv($file, $row->toArray(), ';');
            }

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


        return view('monitoring.hsi', [
            'dataHsi' => $dataHsi,
            'filters' => $filters
        ]);
    }
}