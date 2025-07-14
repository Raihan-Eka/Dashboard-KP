<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\DashboardData;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function getRegionsAndCities()
    {
        $regions = Region::with('cities')->orderBy('id')->get();
        return response()->json($regions);
    }

    public function getDashboardData(Request $request)
    {
        // 1. Ambil semua region dan kota yang sesuai filter di awal
        $regionQuery = Region::with('cities');
        if ($request->filled('region_id')) {
            $regionQuery->where('id', $request->input('region_id'));
        }
        $allRegions = $regionQuery->orderBy('id')->get();

        // 2. Kumpulkan semua ID kota yang akan ditampilkan dari hasil query region
        $cityIds = [];
        foreach ($allRegions as $region) {
            foreach ($region->cities as $city) {
                // Hanya tambahkan kota jika tidak ada filter kota, ATAU jika kota ini cocok dengan filter
                if (!$request->filled('city_id') || $city->id == $request->input('city_id')) {
                    $cityIds[] = $city->id;
                }
            }
        }
        $cityIds = array_unique($cityIds);

        // 3. Ambil semua data agregat untuk kota-kota tersebut dalam satu query
        $aggregatedData = [];
        if (!empty($cityIds)) {
            $dataQuery = DashboardData::query()
                ->whereIn('city_id', $cityIds)
                ->select(
                    'city_id', 'category',
                    DB::raw('SUM(sid) as sid'), DB::raw('SUM(comply) as comply'),
                    DB::raw('SUM(not_comply) as not_comply'), DB::raw('SUM(total) as total'),
                    DB::raw('AVG(target) as target'), DB::raw('SUM(ttr_comply) as ttr_comply'),
                    DB::raw('AVG(achievement) as achievement'), DB::raw('SUM(ticket_count) as ticket_count')
                )->groupBy('city_id', 'category');

            // Terapkan sisa filter
            if ($request->filled('start_date')) {
                $dataQuery->whereDate('entry_date', '>=', $request->input('start_date'));
            }
            if ($request->filled('end_date')) {
                $dataQuery->whereDate('entry_date', '<=', $request->input('end_date'));
            }
            if ($request->filled('category')) {
                $dataQuery->where('category', $request->input('category'));
            }

            // Ubah hasil query menjadi format yang mudah diakses: [city_id][category] => data
            $dbData = $dataQuery->get();
            foreach ($dbData as $data) {
                $aggregatedData[$data->city_id][$data->category] = $this->formatCategoryData($data);
            }
        }

        // 4. Susun hasil akhir dengan menggabungkan semua region dan kota dengan data agregat
        $results = [];
        foreach ($allRegions as $region) {
            $regionData = [
                'id' => $region->id, 'name' => $region->name,
                'k1' => $this->getEmptyCategoryData(), 'k2' => $this->getEmptyCategoryData(), 'k3' => $this->getEmptyCategoryData(),
                'cities_detail' => []
            ];

            foreach ($region->cities as $city) {
                // Lewati kota ini jika tidak cocok dengan filter kota yang aktif
                if ($request->filled('city_id') && $city->id != $request->input('city_id')) {
                    continue;
                }

                $cityDetail = [
                    'id' => $city->id, 'name' => $city->name,
                    'k1' => $aggregatedData[$city->id]['K1'] ?? $this->getEmptyCategoryData(),
                    'k2' => $aggregatedData[$city->id]['K2'] ?? $this->getEmptyCategoryData(),
                    'k3' => $aggregatedData[$city->id]['K3'] ?? $this->getEmptyCategoryData(),
                ];
                
                // Akumulasi data kota ke total regional
                foreach (['k1', 'k2', 'k3'] as $cat) {
                    foreach($cityDetail[$cat] as $key => $value) {
                        $regionData[$cat][$key] += $value;
                    }
                }
                $regionData['cities_detail'][] = $cityDetail;
            }
            $results[] = $regionData;
        }

        return response()->json($results);
    }

    private function getEmptyCategoryData() {
        return ['sid' => 0, 'comply' => 0, 'not_comply' => 0, 'total' => 0, 'target' => 0, 'ttr_comply' => 0, 'achievement' => 0, 'ticket_count' => 0];
    }

    private function formatCategoryData($data) {
        return [
            'sid' => (int) $data->sid, 'comply' => (int) $data->comply, 'not_comply' => (int) $data->not_comply,
            'total' => (int) $data->total, 'target' => round($data->target ?? 0, 2), 'ttr_comply' => (int) $data->ttr_comply,
            'achievement' => round($data->achievement ?? 0, 2), 'ticket_count' => (int) $data->ticket_count,
        ];
    }

    public function storeData(Request $request) {
        try {
            $validatedData = $request->validate([
                'city_id' => 'required|exists:cities,id', 'category' => 'required|in:K1,K2,K3',
                'entry_date' => 'required|date', 'sid' => 'required|integer|min:0',
                'comply' => 'required|integer|min:0', 'not_comply' => 'required|integer|min:0',
                'target' => 'required|numeric|min:0|max:100', 'ttr_comply' => 'required|integer|min:0',
            ]);
            $validatedData['total'] = $validatedData['comply'] + $validatedData['not_comply'];
            $validatedData['achievement'] = ($validatedData['total'] > 0) ? round(($validatedData['comply'] / $validatedData['total']) * 100, 2) : 0;
            $validatedData['ticket_count'] = 1;
            DashboardData::updateOrCreate(
                ['city_id' => $validatedData['city_id'], 'category' => $validatedData['category'], 'entry_date' => $validatedData['entry_date']],
                $validatedData
            );
            return response()->json(['message' => 'Data berhasil disimpan!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        }
    }
}
