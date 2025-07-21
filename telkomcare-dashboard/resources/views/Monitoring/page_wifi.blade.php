@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl font-bold mb-4 text-white">TTR Compliance - Wifi</h1>

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Regional</th>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Target</th>
                    <th colspan="3" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Jumlah Tiket</th>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Compliance (%)</th>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Achv (%)</th>
                </tr>
                <tr class="bg-gray-700">
                    <th class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Comply</th>
                    <th class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Not Comply</th>
                    <th class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">Total</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse ($dataWifi as $data)
                    <tr class="text-center hover:bg-gray-700">
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ $data->regional }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->target, 2) }} %</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->not_comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->total) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->compliance, 2) }} %</td>
                        <td class="p-3 text-sm whitespace-nowrap font-bold border border-gray-600 {{ $data->achv >= 100 ? 'text-green-400' : 'text-red-400' }}">
                            {{ number_format($data->achv, 2) }} %
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-4 border border-gray-600">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection