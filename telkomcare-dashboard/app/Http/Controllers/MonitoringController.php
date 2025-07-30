<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\WifiTicket;
use App\Models\HsiTicket;
use Carbon\Carbon;
use Illuminate\Support\Collection; // <-- Pastikan ini ditambahkan

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

     public function getWifiRegionalData()
    {
        $summaryData = DB::table('wifi_summary_complex')->get();
        $regions = $summaryData->where('level', 1);
        $dataRegions = $regions->map(function ($region) {
            // Mengembalikan summary lengkap, bukan hanya achv_percentage
            return [ 'name' => $region->regional, 'summary' => $this->formatComplianceSummary($region) ];
        });
        $allRegionNames = ['REG-1', 'REG-2', 'REG-3', 'REG-4', 'REG-5', 'REG-6', 'REG-7'];
        return collect($allRegionNames)->map(function ($regionName) use ($dataRegions) {
            $regionData = $dataRegions->firstWhere('name', $regionName);
            if ($regionData) return $regionData;
            return [ 'name' => $regionName, 'summary' => $this->formatComplianceSummary(new \stdClass()) ];
        });
    }
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
    
    // --- MULAI BLOK KODE BARU UNTUK PAGE HSI ---

    /**
     * Helper function baru untuk menghitung summary data HSI.
     */
     
    private $hsiTargets = [
        '4H' => [ 'REG-1' => 69, 'REG-2' => 61, 'REG-3' => 61, 'REG-4' => 68, 'REG-5' => 68, 'REG-6' => 58, 'REG-7' => 58, ],
        '24H' => [ 'REG-1' => 95, 'REG-2' => 97, 'REG-3' => 97, 'REG-4' => 96, 'REG-5' => 96, 'REG-6' => 92, 'REG-7' => 92, ],
    ];

    public function getHsiRegionalData()
    {
        $notComplyString = 'NOT_COMPLY'; // <-- UBAH DI SINI
        $allData = HsiTicket::query()->get();
        $allData = $allData->map(function ($item) {
            if (isset($item->WITEL) && $item->WITEL === 'BANDUNGBRT') { $item->REGIONAL = 'REG-3'; }
            if (isset($item->WITEL) && $item->WITEL === 'BANTENHTTPS://OS') { $item->WITEL = 'BANTEN'; }
            return $item;
        });
        return $allData->groupBy('REGIONAL')->map(function (Collection $regionalItems, string $regionalName) use ($notComplyString) {
            return (object)[
                'name' => $regionalName,
                'summary' => $this->calculateSummaryHsi($regionalItems, $regionalName, $notComplyString),
            ];
        })->sortKeys()->values();
    }

    private function calculateSummaryHsi(Collection $items, ?string $regionalName = null, string $notComplyString = 'NOT COMPLY'): object
    {
        $h4_comply = $items->where('COMPLY_4_E2E', 'COMPLY')->count();
        $h4_not_comply = $items->where('COMPLY_4_E2E', $notComplyString)->count();
        $h4_total = $h4_comply + $h4_not_comply;
        $h4_target = ($regionalName && isset($this->hsiTargets['4H'][$regionalName])) ? $this->hsiTargets['4H'][$regionalName] : 0;
        $h4_real = ($h4_total > 0) ? ($h4_comply / $h4_total) * 100 : 0;
        $h4_ach = ($h4_target > 0) ? ($h4_real / $h4_target) * 100 : 0;
        $itemsFor24H = $items->where('COMPLY_4_E2E', $notComplyString);
        $h24_comply = $itemsFor24H->where('COMPLY_24_E2E', 'COMPLY')->count();
        $h24_not_comply = $itemsFor24H->where('COMPLY_24_E2E', $notComplyString)->count();
        $h24_total = $h24_comply + $h24_not_comply;
        $h24_target = ($regionalName && isset($this->hsiTargets['24H'][$regionalName])) ? $this->hsiTargets['24H'][$regionalName] : 0;
        $h24_real = ($h24_total > 0) ? ($h24_comply / $h24_total) * 100 : 0;
        $h24_ach = ($h24_target > 0) ? ($h24_real / $h24_target) * 100 : 0;
        return (object)[
            'h4_ach' => $h4_ach, 'h24_ach' => $h24_ach, 'h4_comply' => $h4_comply, 'h4_not_comply' => $h4_not_comply, 'h4_target' => $h4_target, 'h4_real' => $h4_real,
            'h24_comply' => $h24_comply, 'h24_not_comply' => $h24_not_comply, 'h24_target' => $h24_target, 'h24_real' => $h24_real,
            'total_tiket' => $items->count()
        ];
    }
    
    private function processRegionalChildren(Collection $regionalItems, string $regionalName, string $notComplyString): Collection
    {
        return $regionalItems->whereNotNull('WITEL')->groupBy('WITEL')->map(function ($witelItems, $witelName) use ($regionalName, $notComplyString) {
            $children = collect();
            if ($regionalName === 'REG-3') {
                $children = $witelItems->whereNotNull('HSA')->groupBy('HSA')->map(function ($hsaItems, $hsaName) use ($regionalName, $notComplyString) {
                    $workzones = $hsaItems->whereNotNull('WORKZONE')->groupBy('WORKZONE')->map(function ($workzoneItems, $workzoneName) use ($regionalName, $notComplyString) {
                        return (object)[ 'name' => $workzoneName, 'summary' => $this->calculateSummaryHsi($workzoneItems, $regionalName, $notComplyString) ];
                    });
                    return (object)[ 'name' => $hsaName, 'summary' => $this->calculateSummaryHsi($hsaItems, $regionalName, $notComplyString), 'workzones' => $workzones->sortKeys()->values() ];
                });
            } else {
                $children = $witelItems->whereNotNull('WORKZONE')->groupBy('WORKZONE')->map(function ($workzoneItems, $workzoneName) use ($regionalName, $notComplyString) {
                    return (object)[ 'name' => $workzoneName, 'summary' => $this->calculateSummaryHsi($workzoneItems, $regionalName, $notComplyString) ];
                });
            }
            return (object)[ 'name' => $witelName, 'summary' => $this->calculateSummaryHsi($witelItems, $regionalName, $notComplyString), 'children' => $children->sortKeys()->values() ];
        });
    }

    public function pageHsi(Request $request)
    {
        $filters = [ 'start_date' => $request->input('start_date'), 'end_date' => $request->input('end_date'), ];
        $query = HsiTicket::query();
        if ($filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('REPORTED_DATE', [$filters['start_date'], $filters['end_date']]);
        }
        $allData = $query->get();

        $notComplyString = 'NOT_COMPLY'; // <-- UBAH DI SINI
        
        $allData = $allData->map(function ($item) {
            if (isset($item->WITEL) && $item->WITEL === 'BANDUNGBRT') { $item->REGIONAL = 'REG-3'; }
            if (isset($item->WITEL) && $item->WITEL === 'BANTENHTTPS://OS') { $item->WITEL = 'BANTEN'; }
            return $item;
        });

        $dataRegions = $allData->groupBy('REGIONAL')->map(function (Collection $regionalItems, string $regionalName) use ($notComplyString) {
            $witels = $this->processRegionalChildren($regionalItems, $regionalName, $notComplyString);
            return (object)[
                'name' => $regionalName,
                'summary' => $this->calculateSummaryHsi($regionalItems, $regionalName, $notComplyString),
                'witels' => $witels->sortKeys()->values()
            ];
        })->sortKeys()->values();

        $dataNasional = $this->calculateSummaryHsi($allData, null, $notComplyString);

        $avgTarget4H = count($this->hsiTargets['4H']) > 0 ? array_sum($this->hsiTargets['4H']) / count($this->hsiTargets['4H']) : 0;
        $avgTarget24H = count($this->hsiTargets['24H']) > 0 ? array_sum($this->hsiTargets['24H']) / count($this->hsiTargets['24H']) : 0;
        $dataNasional->h4_target = $avgTarget4H;
        $dataNasional->h24_target = $avgTarget24H;
        $dataNasional->h4_ach = $avgTarget4H > 0 ? ($dataNasional->h4_real / $avgTarget4H) * 100 : 0;
        $dataNasional->h24_ach = $avgTarget24H > 0 ? ($dataNasional->h24_real / $avgTarget24H) * 100 : 0;
        
        return view('monitoring.hsi', compact('dataRegions', 'filters', 'dataNasional'));
    }
}