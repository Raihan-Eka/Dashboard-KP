<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\WifiTicket;
use App\Models\HsiTicket;
use Carbon\Carbon;

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
        // DIHAPUS: Variabel target yang hardcoded tidak lagi diperlukan di sini.
        // $targetCompliance = 98.50;

        // Mengambil data dari tabel summary
        $summaryData = DB::table('wifi_summary_complex')->get();

        $regions = $summaryData->where('level', 1);
        $witelsByReg = $summaryData->where('level', 2)->groupBy('regional');
        $hsasByWitel = $summaryData->where('level', 3)->groupBy('witel');
        $stosByHsa = $summaryData->where('level', 4)->whereNotNull('hsa')->where('hsa', '!=', '')->groupBy('hsa');
        $stosByWitel = $summaryData->where('level', 4)->where(function ($item) {
            return is_null($item->hsa) || $item->hsa === '';
        })->groupBy('witel');

        // PERUBAHAN: Menghapus $targetCompliance dari parameter use()
        $dataRegions = $regions->map(function ($region) use ($witelsByReg, $hsasByWitel, $stosByHsa, $stosByWitel) {
            $witelsData = $witelsByReg->get($region->regional, collect())->map(function ($witel) use ($hsasByWitel, $stosByHsa, $stosByWitel) {
                
                $hsasData = $hsasByWitel->get($witel->witel, collect())->map(function ($hsa) use ($stosByHsa) {
                    $stoData = $stosByHsa->get($hsa->hsa, collect())->map(function($sto) {
                        // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
                        return [ 'name' => $sto->sto, 'summary' => $this->formatComplianceSummary($sto) ];
                    });
                    return [
                        'name' => $hsa->hsa,
                        // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
                        'summary' => $this->formatComplianceSummary($hsa),
                        'stos' => $stoData->sortBy('name')->values(),
                    ];
                });

                $stosWithoutHsa = $stosByWitel->get($witel->witel, collect())->map(function($sto) {
                    // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
                    return [ 'name' => $sto->sto, 'summary' => $this->formatComplianceSummary($sto) ];
                });

                return [
                    'name' => $witel->witel,
                    // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
                    'summary' => $this->formatComplianceSummary($witel),
                    'hsas' => $hsasData->sortBy('name')->values(),
                    'stos_direct' => $stosWithoutHsa->sortBy('name')->values()
                ];
            });

            return [
                'name' => $region->regional,
                // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
                'summary' => $this->formatComplianceSummary($region),
                'witels' => $witelsData->sortBy('name')->values(),
            ];
        });
        
        $allRegionNames = ['REG-1', 'REG-2', 'REG-3', 'REG-4', 'REG-5', 'REG-6', 'REG-7'];
        // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
        $emptySummary = $this->formatComplianceSummary((object)[]);

        $finalDataRegions = collect($allRegionNames)->map(function ($regionName) use ($dataRegions, $emptySummary) {
            $regionData = $dataRegions->firstWhere('name', $regionName);
            if ($regionData) {
                return $regionData;
            }
            return [ 'name' => $regionName, 'summary' => $emptySummary, 'witels' => collect() ];
        });

        $nasionalItems = $regions->reduce(function ($carry, $item) {
            $carry['comply'] = ($carry['comply'] ?? 0) + $item->comply;
            $carry['not_comply'] = ($carry['not_comply'] ?? 0) + $item->not_comply;
            $carry['total'] = ($carry['total'] ?? 0) + $item->total;
            // Baris ini penting jika Anda ingin target nasional berdasarkan rata-rata
            $carry['target'][] = $item->target ?? 98.50;
            return $carry;
        }, []);
        
        // Menghitung target nasional sebagai rata-rata dari target regional
        if (!empty($nasionalItems['target'])) {
            $nasionalItems['target'] = array_sum($nasionalItems['target']) / count($nasionalItems['target']);
        }

        // PERUBAHAN: Memanggil formatComplianceSummary tanpa target hardcoded
        $dataNasional = $this->formatComplianceSummary((object)$nasionalItems);

        return view('monitoring.wifi', [
            'dataRegions' => $finalDataRegions,
            'dataNasional' => $dataNasional,
            'filters' => $filters
        ]);
    }

    /**
     * PERUBAHAN: Fungsi ini sekarang hanya menerima item data,
     * dan secara otomatis mencari properti 'target' dari item tersebut.
     */
    private function formatComplianceSummary($item)
    {
        // Ambil target dari properti item jika ada, jika tidak, gunakan 98.50 sebagai default.
        $target = $item->target ?? 98.50;

        $comply = $item->comply ?? 0;
        $notComply = $item->not_comply ?? 0;
        $total = $item->total ?? 0;
        
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

    public function downloadWifiRawData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $fileName = 'raw_wifi_data';
        if ($startDate && $endDate) {
            $fileName = "raw_wifi_data_from_{$startDate}_to_{$endDate}.csv";
        }

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response()->stream(function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            $columns = DB::getSchemaBuilder()->getColumnListing('wifi_tickets_raw');
            fputcsv($file, $columns, ';');
            
            $query = WifiTicket::query();
            if ($startDate && $endDate) {
                $query->whereBetween('Reported_Date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            foreach ($query->orderBy('id')->cursor() as $row) {
                fputcsv($file, (array) $row->getAttributes(), ';');
            }
            fclose($file);
        }, 200, $headers);
    }
public function pageHsi(Request $request)
{
    // ====== BLOK BARU UNTUK TANGGAL DEFAULT ======
    // Jika tidak ada tanggal yang dipilih, atur default ke bulan ini
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

    $filters = [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];
    // ===============================================

    // Query sekarang akan selalu menggunakan filter tanggal
    $query = \App\Models\HsiTicket::query();
               // ->whereBetween('REPORTED_DATE', [$filters['start_date'], $filters['end_date']]);
    
    $allData = $query->get();

    $dataHsi = $allData->groupBy('REGIONAL')->map(function ($items, $tregName) {
        $summary = [];
        $categories = ['4H', '24H'];

        foreach ($categories as $cat) {
            $catItems = $items->where('CATEGORY', $cat);
            $comply = $catItems->where('STATUS', 'Comply')->count();
            $notComply = $catItems->where('STATUS', 'Not Comply')->count();
            $total = $comply + $notComply;
            $target = $catItems->first()->TARGET_PERCENTAGE ?? 0;
            $real = ($total > 0) ? ($comply / $total) * 100 : 0;
            $ach = ($target > 0) ? ($real / $target) * 100 : 0;
            
            $key = 'h' . str_replace('H', '', $cat) . '_';
            
            $summary[$key.'comply'] = $comply;
            $summary[$key.'not_comply'] = $notComply;
            $summary[$key.'total'] = $total;
            $summary[$key.'target'] = $target;
            $summary[$key.'real'] = $real;
            $summary[$key.'ach'] = $ach;
        }
        $summary['treg'] = $tregName;
        $summary['total_tiket'] = $items->count();
        return (object)$summary;
    });
    
    for ($i = 1; $i <= 7; $i++) {
        $tregName = 'REG-' . $i;
        if (!$dataHsi->has($tregName)) {
            $dataHsi->put($tregName, (object)[
                'treg' => $tregName,
                'h4_comply' => 0, 'h4_not_comply' => 0, 'h4_total' => 0, 'h4_target' => 0, 'h4_real' => 0, 'h4_ach' => 0,
                'h24_comply' => 0, 'h24_not_comply' => 0, 'h24_total' => 0, 'h24_target' => 0, 'h24_real' => 0, 'h24_ach' => 0,
                'total_tiket' => 0
            ]);
        }
    }
    
    $dataHsi = $dataHsi->sortBy('treg')->values();

    return view('monitoring.hsi', compact('dataHsi', 'filters'));
}
}