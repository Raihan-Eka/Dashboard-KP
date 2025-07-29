<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\DatinRawData;

class PageController extends Controller
{
    public function showHome(): View { return view('home'); }

    public function showDatin(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // --- PERUBAHAN DIMULAI DI SINI ---
        // 1. Variabel $targets diubah menjadi array multi-dimensi
        $targets = [
            'REG-1' => ['K1' => 100.0, 'K2' => 78.0, 'K3' => 94.0],
            'REG-2' => ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0],
            'REG-3' => ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0],
            'REG-4' => ['K1' => 100.0, 'K2' => 83.0, 'K3' => 96.0],
            'REG-5' => ['K1' => 100.0, 'K2' => 83.0, 'K3' => 96.0],
            'REG-6' => ['K1' => 100.0, 'K2' => 66.0, 'K3' => 86.0],
            'REG-7' => ['K1' => 100.0, 'K2' => 66.0, 'K3' => 86.0],
        ];
        // --- PERUBAHAN SELESAI DI SINI ---

        $summaryData = DB::table('datin_summary_complex')->get();

        $regions = $summaryData->where('level', 1);
        $witelsByReg = $summaryData->where('level', 2)->groupBy('reg');
        $hsasByWitel = $summaryData->where('level', 3)->groupBy('witel');
        $workzonesByHsa = $summaryData->where('level', 4)->whereNotNull('hsa')->groupBy('hsa');
        $workzonesByWitel = $summaryData->where('level', 4)->where('hsa', '')->groupBy('witel');

        $dataRegions = $regions->map(function ($region) use ($witelsByReg, $hsasByWitel, $workzonesByHsa, $workzonesByWitel, $targets) {
            
            $witelsData = $witelsByReg->get($region->reg, collect())->map(function ($witel) use ($hsasByWitel, $workzonesByHsa, $workzonesByWitel, $targets) {
                
                $hsasData = $hsasByWitel->get($witel->witel, collect())->map(function ($hsa) use ($workzonesByHsa, $targets) {
                    $workzonesData = $workzonesByHsa->get($hsa->hsa, collect())->map(function($workzone) use ($targets){
                        return [ 'name' => $workzone->workzone, 'summary' => $this->formatSummary($workzone, $targets) ];
                    });
                    return [
                        'name' => $hsa->hsa,
                        'summary' => $this->formatSummary($hsa, $targets),
                        'workzones' => $workzonesData->sortBy('name')->values(),
                    ];
                });

                $workzonesWithoutHsa = $workzonesByWitel->get($witel->witel, collect())->map(function($workzone) use ($targets) {
                    return [ 'name' => $workzone->workzone, 'summary' => $this->formatSummary($workzone, $targets) ];
                });

                return [
                    'name' => $witel->witel,
                    'summary' => $this->formatSummary($witel, $targets),
                    'hsas' => $hsasData->sortBy('name')->values(),
                    'workzones_direct' => $workzonesWithoutHsa->sortBy('name')->values()
                ];
            });

            return [
                'name' => $region->reg,
                'summary' => $this->formatSummary($region, $targets),
                'witels' => $witelsData->sortBy('name')->values(),
            ];
        });
        
        $allRegionNames = ['REG-1', 'REG-2', 'REG-3', 'REG-4', 'REG-5', 'REG-6', 'REG-7'];
        
        $finalData = collect($allRegionNames)->map(function ($regionName) use ($dataRegions, $targets) {
            $regionData = $dataRegions->firstWhere('name', $regionName);
            if ($regionData) return $regionData;

            // Jika regional kosong, buat summary kosong dengan target yang benar
            $regionTargets = $targets[$regionName] ?? ['K1' => 100, 'K2' => 81, 'K3' => 95];
            $emptySummary = [
                'sid_k1' => 0, 'k1_comply' => 0, 'k1_not_comply' => 0, 'k1_total' => 0, 'k1_target' => $regionTargets['K1'].'%', 'k1_ttr_comply' => 0, 'k1_ach' => 0,
                'sid_k2' => 0, 'k2_comply' => 0, 'k2_not_comply' => 0, 'k2_total' => 0, 'k2_target' => $regionTargets['K2'].'%', 'k2_ttr_comply' => 0, 'k2_ach' => 0,
                'sid_k3' => 0, 'k3_comply' => 0, 'k3_not_comply' => 0, 'k3_total' => 0, 'k3_target' => $regionTargets['K3'].'%', 'k3_ttr_comply' => 0, 'k3_ach' => 0,
                'rata2_ach' => 0, 'total_tickets' => 0,
            ];
            return [ 'name' => $regionName, 'summary' => $emptySummary, 'witels' => collect() ];
        });

        return view('datin', [
            'dataRegions' => $finalData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function formatSummary($item, $targets)
    {
        // --- PERUBAHAN DIMULAI DI SINI ---
        // 2. Ambil target spesifik berdasarkan REG dari item
        $regionTargets = $targets[$item->reg] ?? ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0];
        
        $k1_ach_percent = ($regionTargets['K1'] > 0 && $item->k1_ttr_comply > 0) ? ($item->k1_ttr_comply / $regionTargets['K1']) * 100 : 0;
        $k2_ach_percent = ($regionTargets['K2'] > 0 && $item->k2_ttr_comply > 0) ? ($item->k2_ttr_comply / $regionTargets['K2']) * 100 : 0;
        $k3_ach_percent = ($regionTargets['K3'] > 0 && $item->k3_ttr_comply > 0) ? ($item->k3_ttr_comply / $regionTargets['K3']) * 100 : 0;
        
        $summary = [
            'sid_k1' => $item->sid_k1, 'k1_comply' => $item->k1_comply, 'k1_not_comply' => $item->k1_not_comply, 'k1_total' => $item->k1_total, 'k1_target' => $regionTargets['K1'].'%', 'k1_ttr_comply' => $item->k1_ttr_comply, 'k1_ach' => $k1_ach_percent,
            'sid_k2' => $item->sid_k2, 'k2_comply' => $item->k2_comply, 'k2_not_comply' => $item->k2_not_comply, 'k2_total' => $item->k2_total, 'k2_target' => $regionTargets['K2'].'%', 'k2_ttr_comply' => $item->k2_ttr_comply, 'k2_ach' => $k2_ach_percent,
            'sid_k3' => $item->sid_k3, 'k3_comply' => $item->k3_comply, 'k3_not_comply' => $item->k3_not_comply, 'k3_total' => $item->k3_total, 'k3_target' => $regionTargets['K3'].'%', 'k3_ttr_comply' => $item->k3_ttr_comply, 'k3_ach' => $k3_ach_percent,
            'total_tickets' => $item->total_tickets,
        ];
        // --- PERUBAHAN SELESAI DI SINI ---
        
        $achValues = [];
        if ($summary['k1_total'] > 0) $achValues[] = $summary['k1_ach'];
        if ($summary['k2_total'] > 0) $achValues[] = $summary['k2_ach'];
        if ($summary['k3_total'] > 0) $achValues[] = $summary['k3_ach'];
        $summary['rata2_ach'] = count($achValues) > 0 ? array_sum($achValues) / count($achValues) : 0;
        
        return $summary;
    }

    public function downloadDatinRaw(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $fileName = 'datin_raw_data.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            $columns = DB::getSchemaBuilder()->getColumnListing('datin_raw_data');
            fputcsv($file, $columns, ';');
            
            $query = DatinRawData::query();
            if ($startDate && $endDate) {
                // Asumsi kolom tanggal di tabel raw adalah 'TROUBLE_OPENTIME'
                $query->whereBetween('TROUBLE_OPENTIME', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            
            $query->orderBy('REGIONAL')->chunk(1000, function ($data) use ($file) {
                foreach ($data->toArray() as $row) {
                    fputcsv($file, (array)$row, ';');
                }
            });
            fclose($file);
        };
        return new StreamedResponse($callback, 200, $headers);
    }
}