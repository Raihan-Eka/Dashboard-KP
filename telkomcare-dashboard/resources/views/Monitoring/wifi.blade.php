@extends('layouts.app')

@push('styles')
<style>
    .cursor-pointer .fa-chevron-right { transition: transform 0.2s ease-in-out; }
    .expanded .fa-chevron-right { transform: rotate(90deg); }
    input[type="date"] { color-scheme: light; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl font-bold mb-4 text-white">TTR Compliance - Wifi</h1>
    
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
        <form method="GET" action="{{ route('monitoring.wifi') }}" class="flex flex-col sm:flex-row items-end gap-4">
            <div>
                <label for="start_date" class="text-sm font-medium text-gray-300">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md shadow-sm text-white p-2">
            </div>
            <div>
                <label for="end_date" class="text-sm font-medium text-gray-300">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md shadow-sm text-white p-2">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Apply</button>
                <a href="{{ route('monitoring.wifi') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md text-center">Clear</a>
            </div>
            <div class="sm:ml-auto">
                <a href="{{ route('monitoring.wifi.download', ['start_date' => $filters['start_date'] ?? null, 'end_date' => $filters['end_date'] ?? null]) }}" 
                   class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                   <i class="fas fa-download"></i> Download
                </a>
            </div>
        </form>
    </div>
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th rowspan="2" class="p-3 text-sm font-semibold tracking-wide text-left border border-gray-600">Wilayah</th>
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
            <tbody id="wifi-table-body" class="bg-gray-800 divide-y divide-gray-700">
                @forelse ($dataRegions as $region)
                    <tr class="font-bold bg-gray-700 @if(!$region['witels']->isEmpty()) cursor-pointer hover:bg-gray-600 expandable @endif" data-id="region-{{ $loop->index }}">
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">@if(!$region['witels']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $region['name'] }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($region['summary']['target'], 2) }} %</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($region['summary']['comply']) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($region['summary']['not_comply']) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($region['summary']['total']) }}</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($region['summary']['compliance_percentage'], 2) }} %</td>
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-bold {{ $region['summary']['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($region['summary']['achv_percentage'], 2) }} %</td>
                    </tr>

                    @foreach ($region['witels'] as $witel)
                        @php
                            $isReg3 = ($region['name'] === 'REG-3');
                            $hasChildren = $isReg3 ? !$witel['hsas']->isEmpty() : !$witel['stos_direct']->isEmpty();
                        @endphp
                        <tr class="hidden font-semibold bg-gray-700 bg-opacity-50 @if($hasChildren) cursor-pointer hover:bg-gray-600 expandable @endif" data-parent="region-{{ $loop->parent->index }}" data-id="witel-{{ $loop->parent->index }}-{{ $loop->index }}">
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left pl-10">@if($hasChildren)<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $witel['name'] }}</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($witel['summary']['target'], 2) }} %</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($witel['summary']['comply']) }}</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($witel['summary']['not_comply']) }}</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($witel['summary']['total']) }}</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($witel['summary']['compliance_percentage'], 2) }} %</td>
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-bold {{ $witel['summary']['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($witel['summary']['achv_percentage'], 2) }} %</td>
                        </tr>
                        
                        @if ($isReg3)
                            @foreach ($witel['hsas'] as $hsa)
                                <tr class="hidden font-medium bg-gray-800 @if(!$hsa['stos']->isEmpty()) cursor-pointer hover:bg-gray-700 expandable @endif" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-id="hsa-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left pl-20">@if(!$hsa['stos']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $hsa['name'] }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($hsa['summary']['target'], 2) }} %</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($hsa['summary']['comply']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($hsa['summary']['not_comply']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($hsa['summary']['total']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($hsa['summary']['compliance_percentage'], 2) }} %</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-bold {{ $hsa['summary']['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($hsa['summary']['achv_percentage'], 2) }} %</td>
                                </tr>
                                @foreach ($hsa['stos'] as $sto)
                                    <tr class="hidden" data-parent="hsa-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left pl-28">{{ $sto['name'] }}</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['target'], 2) }} %</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['comply']) }}</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['not_comply']) }}</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['total']) }}</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['compliance_percentage'], 2) }} %</td>
                                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-bold {{ $sto['summary']['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($sto['summary']['achv_percentage'], 2) }} %</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            @foreach ($witel['stos_direct'] as $sto)
                                <tr class="hidden" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left pl-20">{{ $sto['name'] }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['target'], 2) }} %</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['comply']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['not_comply']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['total']) }}</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center">{{ number_format($sto['summary']['compliance_percentage'], 2) }} %</td>
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-bold {{ $sto['summary']['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($sto['summary']['achv_percentage'], 2) }} %</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @empty
                    <tr><td colspan="7" class="text-center p-4">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
                <tr class="font-bold bg-gray-600">
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-left">NASIONAL</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center">{{ number_format($dataNasional['target'], 2) }} %</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center">{{ number_format($dataNasional['comply']) }}</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center">{{ number_format($dataNasional['not_comply']) }}</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center">{{ number_format($dataNasional['total']) }}</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center">{{ number_format($dataNasional['compliance_percentage'], 2) }} %</td>
                     <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-center font-bold {{ $dataNasional['achv_percentage'] >= 100 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($dataNasional['achv_percentage'], 2) }} %</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('wifi-table-body');
    if (tableBody) {
        tableBody.addEventListener('click', function(event) {
            const targetRow = event.target.closest('tr.expandable');
            if (!targetRow) return;

            const id = targetRow.dataset.id;
            const icon = targetRow.querySelector('i.fa-chevron-right');
            const children = tableBody.querySelectorAll(`tr[data-parent="${id}"]`);
            targetRow.classList.toggle('expanded');
            
            if (icon) {
                icon.style.transform = targetRow.classList.contains('expanded') ? 'rotate(90deg)' : 'rotate(0deg)';
            }
            
            const isHidden = !targetRow.classList.contains('expanded');

            children.forEach(child => {
                child.classList.toggle('hidden', isHidden);
                if (isHidden && child.classList.contains('expandable')) {
                    child.classList.remove('expanded');
                    const childId = child.dataset.id;
                    const childIcon = child.querySelector('i.fa-chevron-right');
                    if(childIcon) childIcon.style.transform = 'rotate(0deg)';

                    const grandChildren = tableBody.querySelectorAll(`tr[data-parent^="${childId}"]`);
                    grandChildren.forEach(grandChild => {
                        grandChild.classList.add('hidden');
                        if(grandChild.classList.contains('expandable')) {
                             grandChild.classList.remove('expanded');
                             const grandChildIcon = grandChild.querySelector('i.fa-chevron-right');
                             if(grandChildIcon) grandChildIcon.style.transform = 'rotate(0deg)';
                        }
                    });
                }
            });
        });
    }
});
</script>
@endpush