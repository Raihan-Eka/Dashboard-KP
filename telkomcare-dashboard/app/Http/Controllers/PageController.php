<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\DatinRawData;
use App\Imports\DatinImport;
use App\Models\HsiTicket;
use Illuminate\Support\Collection;
use App\Http\Controllers\MonitoringController;
use Maatwebsite\Excel\Facades\Excel;

class PageController extends Controller
{
    /**
     * Menampilkan halaman beranda (home) dengan TIGA grafik.
     */
    public function showHome(MonitoringController $monitoringController): View
    {
        // 1. Ambil data collection yang lengkap dari setiap sumber
        $datinData = $this->getDatinRegionalData();
        $wifiData = $monitoringController->getWifiRegionalData();
        $hsiData = $monitoringController->getHsiRegionalData();

        // 2. Kirim data collection lengkap tersebut ke view
        return view('home', compact('datinData', 'wifiData', 'hsiData'));
    }

    /**
     * Menampilkan halaman Datin.
     */
    public function showDatin(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dataRegions = $this->getDatinRegionalData();

        return view('datin', [
            'dataRegions' => $dataRegions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function getDatinRegionalData()
    {
        $targets = [
            'REG-1' => ['K1' => 100.0, 'K2' => 78.0, 'K3' => 94.0], 'REG-2' => ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0],
            'REG-3' => ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0], 'REG-4' => ['K1' => 100.0, 'K2' => 83.0, 'K3' => 96.0],
            'REG-5' => ['K1' => 100.0, 'K2' => 83.0, 'K3' => 96.0], 'REG-6' => ['K1' => 100.0, 'K2' => 66.0, 'K3' => 86.0],
            'REG-7' => ['K1' => 100.0, 'K2' => 66.0, 'K3' => 86.0],
        ];

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
                    return [ 'name' => $hsa->hsa, 'summary' => $this->formatSummary($hsa, $targets), 'workzones' => $workzonesData->sortBy('name')->values() ];
                });
                $workzonesWithoutHsa = $workzonesByWitel->get($witel->witel, collect())->map(function($workzone) use ($targets) {
                    return [ 'name' => $workzone->workzone, 'summary' => $this->formatSummary($workzone, $targets) ];
                });
                return [ 'name' => $witel->witel, 'summary' => $this->formatSummary($witel, $targets), 'hsas' => $hsasData->sortBy('name')->values(), 'workzones_direct' => $workzonesWithoutHsa->sortBy('name')->values() ];
            });
            return [ 'name' => $region->reg, 'summary' => $this->formatSummary($region, $targets), 'witels' => $witelsData->sortBy('name')->values() ];
        });
        
        $allRegionNames = ['REG-1', 'REG-2', 'REG-3', 'REG-4', 'REG-5', 'REG-6', 'REG-7'];
        
        return collect($allRegionNames)->map(function ($regionName) use ($dataRegions, $targets) {
            $regionData = $dataRegions->firstWhere('name', $regionName);
            if ($regionData) return $regionData;

            $emptySummary = $this->formatSummary((object)['reg' => $regionName], $targets);
            return [ 'name' => $regionName, 'summary' => $emptySummary, 'witels' => collect() ];
        });
    }

    private function formatSummary($item, $targets)
    {
        $regionTargets = $targets[$item->reg] ?? ['K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0];
        
        $k1_ttr_comply = $item->k1_ttr_comply ?? 0;
        $k2_ttr_comply = $item->k2_ttr_comply ?? 0;
        $k3_ttr_comply = $item->k3_ttr_comply ?? 0;

        $k1_ach_percent = ($regionTargets['K1'] > 0 && $k1_ttr_comply > 0) ? ($k1_ttr_comply / $regionTargets['K1']) * 100 : 0;
        $k2_ach_percent = ($regionTargets['K2'] > 0 && $k2_ttr_comply > 0) ? ($k2_ttr_comply / $regionTargets['K2']) * 100 : 0;
        $k3_ach_percent = ($regionTargets['K3'] > 0 && $k3_ttr_comply > 0) ? ($k3_ttr_comply / $regionTargets['K3']) * 100 : 0;
        
        $summary = [
            'sid_k1' => $item->sid_k1 ?? 0, 'k1_comply' => $item->k1_comply ?? 0, 'k1_not_comply' => $item->k1_not_comply ?? 0, 'k1_total' => $item->k1_total ?? 0, 'k1_target' => $regionTargets['K1'], 'k1_ttr_comply' => $k1_ttr_comply, 'k1_ach' => $k1_ach_percent,
            'sid_k2' => $item->sid_k2 ?? 0, 'k2_comply' => $item->k2_comply ?? 0, 'k2_not_comply' => $item->k2_not_comply ?? 0, 'k2_total' => $item->k2_total ?? 0, 'k2_target' => $regionTargets['K2'], 'k2_ttr_comply' => $k2_ttr_comply, 'k2_ach' => $k2_ach_percent,
            'sid_k3' => $item->sid_k3 ?? 0, 'k3_comply' => $item->k3_comply ?? 0, 'k3_not_comply' => $item->k3_not_comply ?? 0, 'k3_total' => $item->k3_total ?? 0, 'k3_target' => $regionTargets['K3'], 'k3_ttr_comply' => $k3_ttr_comply, 'k3_ach' => $k3_ach_percent,
            'total_tickets' => $item->total_tickets ?? 0,
        ];
        
        $achValues = [];
        if (($summary['k1_total']) > 0) $achValues[] = $summary['k1_ach'];
        if (($summary['k2_total']) > 0) $achValues[] = $summary['k2_ach'];
        if (($summary['k3_total']) > 0) $achValues[] = $summary['k3_ach'];
        $summary['rata2_ach'] = count($achValues) > 0 ? array_sum($achValues) / count($achValues) : 0;
        
        return $summary;
    }

    public function uploadDatinExcel(Request $request)
{
    $request->validate([
        'datin_excel' => 'required|mimes:xlsx,xls',
    ]);

    try {
        Excel::import(new DatinImport, $request->file('datin_excel'));
        return redirect()->route('datin')->with('success', 'Data Excel berhasil di-upload dan diproses!');
    } catch (\Exception $e) {
        return redirect()->route('datin')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
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