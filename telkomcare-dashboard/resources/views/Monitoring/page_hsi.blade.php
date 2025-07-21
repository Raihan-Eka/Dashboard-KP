@extends('layouts.app')

@push('styles')
{{-- Menambahkan style khusus untuk halaman ini --}}
<style>
    /*
      Memaksa browser untuk merender UI bawaan 
      seperti kalender date-picker dalam mode terang (putih).
    */
    input[type="date"] {
        color-scheme: light;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl font-bold mb-4 text-white">Resume TTR - HSI (INDIBIZ)</h1>

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
        <form method="GET" action="{{ route('monitoring.hsi') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div>
                <label for="start_date" class="text-sm font-medium text-gray-300">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}" class="mt-1 block w-full bg-gray-700 border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white p-2">
            </div>
            <div>
                <label for="end_date" class="text-sm font-medium text-gray-300">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}" class="mt-1 block w-full bg-gray-700 border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white p-2">
            </div>
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md mt-5 sm:mt-0">
                SUBMIT
            </button>
        </form>
    </div>

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600 align-middle">TREG</th>
                    <th colspan="5" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">4H</th>
                    <th colspan="5" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">24H</th>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600 align-middle">TIKET</th>
                </tr>
                <tr class="bg-gray-700">
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Comply</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Not Comply</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Target</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Real</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Ach</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Comply</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Not Comply</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Target</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Real</th>
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600">Ach</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse ($dataHsi as $data)
                     <tr class="text-center hover:bg-gray-700">
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ $data->treg }}</td>
                        {{-- Kolom 4H --}}
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h4_comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h4_not_comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h4_target, 2) }}%</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h4_real, 2) }}%</td>
                        <td class="p-3 text-sm whitespace-nowrap font-bold border border-gray-600 {{ $data->h4_ach >= 100 ? 'text-green-400' : 'text-red-400' }}">
                            {{ number_format($data->h4_ach, 2) }}%
                        </td>
                        {{-- Kolom 24H --}}
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h24_comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h24_not_comply) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h24_target, 2) }}%</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->h24_real, 2) }}%</td>
                        <td class="p-3 text-sm whitespace-nowrap font-bold border border-gray-600 {{ $data->h24_ach >= 100 ? 'text-green-400' : 'text-red-400' }}">
                            {{ number_format($data->h24_ach, 2) }}%
                        </td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600">{{ number_format($data->total_tiket) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center p-4 border border-gray-600">Tidak ada data untuk filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection