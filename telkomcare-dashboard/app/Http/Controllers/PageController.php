<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PageController extends Controller
{
    public function showHome(): View { return view('home'); }

    public function showDatin()
    {
        $targets = [ 'K1' => 100.0, 'K2' => 81.0, 'K3' => 95.0, ];
        $summaryData = DB::table('datin_summary_complex')->get();

        $regions = $summaryData->where('level', 1);
        $witelsByReg = $summaryData->where('level', 2)->groupBy('reg');
        $datelsByWitel = $summaryData->where('level', 3)->groupBy('witel');
        $stosByDatel = $summaryData->where('level', 4)->groupBy('datel');

        $existingData = $regions->map(function ($region) use ($witelsByReg, $datelsByWitel, $stosByDatel, $targets) {
            $witelsData = $witelsByReg->get($region->reg, collect())->map(function ($witel) use ($datelsByWitel, $stosByDatel, $targets) {
                $datelsData = $datelsByWitel->get($witel->witel, collect())->map(function ($datel) use ($stosByDatel, $targets) {
                    $stoData = $stosByDatel->get($datel->datel, collect())->map(function($sto) use ($targets){
                        return [ 'name' => $sto->sto, 'summary' => $this->formatSummary($sto, $targets) ];
                    });
                    return [
                        'name' => $datel->datel,
                        'summary' => $this->formatSummary($datel, $targets),
                        'stos' => $stoData->sortBy('name')->values(),
                    ];
                });
                return [
                    'name' => $witel->witel,
                    'summary' => $this->formatSummary($witel, $targets),
                    'datels' => $datelsData->sortBy('name')->values(),
                ];
            });
            return [
                'name' => $region->reg,
                'summary' => $this->formatSummary($region, $targets),
                'witels' => $witelsData->sortBy('name')->values(),
            ];
        });

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

        return view('datin', ['dataRegions' => $finalData]);
    }

    private function formatSummary($item, $targets) {
        // Calculate Achievement percentages
        $k1_ach_percent = ($targets['K1'] > 0) ? ($item->k1_ttr_comply / $targets['K1']) * 100 : 0;
        $k2_ach_percent = ($targets['K2'] > 0) ? ($item->k2_ttr_comply / $targets['K2']) * 100 : 0;
        $k3_ach_percent = ($targets['K3'] > 0) ? ($item->k3_ttr_comply / $targets['K3']) * 100 : 0;

        $summary = [
            'sid_k1' => $item->sid_k1, 'k1_comply' => $item->k1_comply, 'k1_not_comply' => $item->k1_not_comply, 'k1_total' => $item->k1_total, 'k1_target' => $targets['K1'].'%', 'k1_ttr_comply' => $item->k1_ttr_comply, 'k1_ach' => $k1_ach_percent,
            'sid_k2' => $item->sid_k2, 'k2_comply' => $item->k2_comply, 'k2_not_comply' => $item->k2_not_comply, 'k2_total' => $item->k2_total, 'k2_target' => $targets['K2'].'%', 'k2_ttr_comply' => $item->k2_ttr_comply, 'k2_ach' => $k2_ach_percent,
            'sid_k3' => $item->sid_k3, 'k3_comply' => $item->k3_comply, 'k3_not_comply' => $item->k3_not_comply, 'k3_total' => $item->k3_total, 'k3_target' => $targets['K3'].'%', 'k3_ttr_comply' => $item->k3_ttr_comply, 'k3_ach' => $k3_ach_percent,
            'total_tickets' => $item->total_tickets,
        ];
        
        $ttrValues = [];
        if ($summary['k1_total'] > 0) $ttrValues[] = $summary['k1_ttr_comply'];
        if ($summary['k2_total'] > 0) $ttrValues[] = $summary['k2_ttr_comply'];
        if ($summary['k3_total'] > 0) $ttrValues[] = $summary['k3_ttr_comply'];

        $summary['rata2_ach'] = count($ttrValues) > 0 ? array_sum($ttrValues) / count($ttrValues) : 0;

        return $summary;
    }
}