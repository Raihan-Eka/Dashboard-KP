<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PageController extends Controller
{
    public function showHome(): View { return view('home'); }

    public function showDatin(Request $request)
    {
        // Mengambil tanggal dari input form, akan bernilai null jika tidak ada
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $targets = [ 'K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0, ];

        // 1. Ambil data mentah, filter berdasarkan tanggal jika ada
        $query = DB::table('datin_raw_data');

        if ($startDate && $endDate) {
            // Asumsi kolom tanggal adalah 'trouble_opentime'
            $query->whereBetween('trouble_opentime', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $flatData = $query->get();

        // 2. Olah data mentah menjadi data summary (kalkulasi on-the-fly)
        $regions = $flatData->groupBy('reg');
        $existingData = $regions->map(function ($regionItems, $reg) use ($targets) {
            $witels = $regionItems->groupBy('witel')->map(function ($witelItems, $witel) use ($targets) {
                $datels = $witelItems->groupBy('datel')->map(function ($datelItems, $datel) use ($targets) {
                    $stos = $datelItems->groupBy('sto')->map(function ($stoItems, $sto) use ($targets) {
                        return [
                            'name' => $sto,
                            'summary' => $this->calculateSummary($stoItems, $targets)
                        ];
                    });
                    return [
                        'name' => trim(str_replace('KANTOR DATEL ', '', $datel)),
                        'summary' => $this->calculateSummary($datelItems, $targets),
                        'stos' => $stos->sortBy('name')->values(),
                    ];
                });
                return [
                    'name' => trim($witel),
                    'summary' => $this->calculateSummary($witelItems, $targets),
                    'datels' => $datels->sortBy('name')->values(),
                ];
            });
            return [
                'name' => 'REG-' . $reg,
                'summary' => $this->calculateSummary($regionItems, $targets),
                'witels' => $witels->sortBy('name')->values(),
            ];
        });

        // 3. Siapkan kerangka untuk semua regional agar yang kosong tetap tampil
        $allRegionNames = ['REG-1', 'REG-2', 'REG-3', 'REG-4', 'REG-5', 'REG-6', 'REG-7'];
        $emptySummary = [
            'sid_k1' => 0, 'k1_comply' => 0, 'k1_not_comply' => 0, 'k1_total' => 0, 'k1_target' => '100%', 'k1_ttr_comply' => 0, 'k1_ach' => 0,
            'sid_k2' => 0, 'k2_comply' => 0, 'k2_not_comply' => 0, 'k2_total' => 0, 'k2_target' => '81%', 'k2_ttr_comply' => 0, 'k2_ach' => 0,
            'sid_k3' => 0, 'k3_comply' => 0, 'k3_not_comply' => 0, 'k3_total' => 0, 'k3_target' => '95%', 'k3_ttr_comply' => 0, 'k3_ach' => 0,
            'rata2_ach' => 0, 'total_tickets' => 0,
        ];
        
        $finalData = collect($allRegionNames)->map(function ($regionName) use ($existingData, $emptySummary) {
            $regionData = $existingData->firstWhere('name', $regionName);
            if ($regionData) return $regionData;
            return [ 'name' => $regionName, 'summary' => $emptySummary, 'witels' => collect() ];
        });

        // 4. Kirim semua variabel yang dibutuhkan ke view
        return view('datin', [
            'dataRegions' => $finalData,
            'startDate' => $startDate, // Variabel ini yang menyebabkan error
            'endDate' => $endDate,     // Variabel ini juga penting
        ]);
    }

    private function calculateSummary($items, $targets) {
        $summary = [];
        foreach (['K1', 'K2', 'K3'] as $k) {
            $kItems = $items->where('flag_k', $k);
            $total = $kItems->count();
            $comply = $kItems->where('is_ttr_customer_comply', 1)->count();
            $ttrComply = ($total > 0) ? ($comply / $total) * 100 : 0;
            $targetVal = $targets[$k];
            $achPercent = ($targetVal > 0) ? ($ttrComply / $targetVal) * 100 : 0;

            $summary['sid_' . strtolower($k)] = $kItems->pluck('sid')->unique()->count();
            $summary[strtolower($k) . '_comply'] = $comply;
            $summary[strtolower($k) . '_not_comply'] = $total - $comply;
            $summary[strtolower($k) . '_total'] = $total;
            $summary[strtolower($k) . '_target'] = $targetVal . '%';
            $summary[strtolower($k) . '_ttr_comply'] = $ttrComply;
            $summary[strtolower($k) . '_ach'] = $achPercent;
        }
        $summary['total_tickets'] = $items->count();
        
        $ttrValues = [];
        if ($summary['k1_total'] > 0) $ttrValues[] = $summary['k1_ttr_comply'];
        if ($summary['k2_total'] > 0) $ttrValues[] = $summary['k2_ttr_comply'];
        if ($summary['k3_total'] > 0) $ttrValues[] = $summary['k3_ttr_comply'];

        $summary['rata2_ach'] = count($ttrValues) > 0 ? array_sum($ttrValues) / count($ttrValues) : 0;

        return $summary;
    }

    public function downloadDatinRaw(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $fileName = 'datin_raw_data';
        if ($startDate && $endDate) {
            $fileName .= "_from_{$startDate}_to_{$endDate}";
        }
        $fileName .= '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            $columns = DB::getSchemaBuilder()->getColumnListing('datin_raw_data');
            fputcsv($file, $columns);

            $query = DB::table('datin_raw_data');
            if ($startDate && $endDate) {
                $query->whereBetween('trouble_opentime', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            
            $query->orderBy('trouble_opentime')->chunk(1000, function ($data) use ($file) {
                foreach ($data as $row) {
                    fputcsv($file, (array)$row);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}