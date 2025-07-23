@extends('layouts.app')

@push('styles')
<style>
    .cursor-pointer .fa-chevron-right { transition: transform 0.2s ease-in-out; }
    .expanded .fa-chevron-right { transform: rotate(90deg); }
    .table-cell { padding: 0.5rem; text-align: center; border: 1px solid #4a5568; white-space: nowrap; }
    .text-left-indent { text-align: left; }
    .header-group { padding: 0.5rem; text-align: center; border: 1px solid #4a5568; }
    .header-main { font-size: 1.1rem; }
    .k1-bg { background-color: rgba(74, 222, 128, 0.3); }
    .k2-bg { background-color: rgba(59, 130, 246, 0.3); }
    .k3-bg { background-color: rgba(244, 63, 94, 0.3); }
    .sub-header-bg { background-color: rgba(255, 255, 255, 0.05); }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">Dashboard Datin</h1>
        
        <div class="flex items-center space-x-4">
            <form action="{{ route('datin') }}" method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <span class="text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">Apply</button>
                <a href="{{ route('datin') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">Clear</a>
            </form>
            
            {{-- TOMBOL DOWNLOAD SEKARANG AKTIF --}}
            <a href="{{ route('datin.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center">
                <i class="fas fa-download mr-2"></i> Download
            </a>
        </div>
    </div>

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-white text-xs">
            <thead class="bg-gray-700 font-bold">
                <tr>
                    <th rowspan="2" class="header-group align-middle">REG</th>
                    <th colspan="7" class="header-group header-main k1-bg">K1</th>
                    <th colspan="7" class="header-group header-main k2-bg">K2</th>
                    <th colspan="7" class="header-group header-main k3-bg">K3</th>
                    <th rowspan="2" class="header-group align-middle" style="background-color: #6b21a8;">Rata2 Ach</th>
                    <th rowspan="2" class="header-group align-middle" style="background-color: #1e3a8a;">TIKET</th>
                </tr>
                <tr class="sub-header-bg">
                    {{-- K1 Sub-headers --}}
                    <th class="header-group">SID</th>
                    <th class="header-group">Comply</th>
                    <th class="header-group">Not Comply</th>
                    <th class="header-group">Total</th>
                    <th class="header-group">Target K1</th>
                    <th class="header-group">TTR Comply K1</th>
                    <th class="header-group">Ach</th>
                    {{-- K2 Sub-headers --}}
                    <th class="header-group">SID</th>
                    <th class="header-group">Comply</th>
                    <th class="header-group">Not Comply</th>
                    <th class="header-group">Total</th>
                    <th class="header-group">Target K2</th>
                    <th class="header-group">TTR Comply K2</th>
                    <th class="header-group">Ach</th>
                    {{-- K3 Sub-headers --}}
                    <th class="header-group">SID</th>
                    <th class="header-group">Comply</th>
                    <th class="header-group">Not Comply</th>
                    <th class="header-group">Total</th>
                    <th class="header-group">Target K3</th>
                    <th class="header-group">TTR Comply K3</th>
                    <th class="header-group">Ach</th>
                </tr>
            </thead>
            <tbody id="datin-table-body">
                 @forelse ($dataRegions as $region)
                    {{-- BARIS REGIONAL --}}
                    <tr class="font-bold bg-gray-700 @if(!$region['witels']->isEmpty()) cursor-pointer hover:bg-gray-600 expandable @endif" 
                        data-id="region-{{ $loop->index }}" data-level="1">
                        <td class="table-cell text-left-indent">
                            @if(!$region['witels']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                            {{ $region['name'] }}
                        </td>
                        {{-- K1 Data --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k1']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k1_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k1_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k1_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k1_target'] }}</td>
                        <td class="table-cell font-bold">{{ number_format($region['summary']['k1_ttr_comply'], 2) }}%</td>
                        <td class="table-cell font-bold" style="color: {{ $region['summary']['k1_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($region['summary']['k1_ach'], 2) }}%</td>
                        {{-- K2 Data --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k2']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k2_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k2_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k2_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k2_target'] }}</td>
                        <td class="table-cell font-bold">{{ number_format($region['summary']['k2_ttr_comply'], 2) }}%</td>
                        <td class="table-cell font-bold" style="color: {{ $region['summary']['k2_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($region['summary']['k2_ach'], 2) }}%</td>
                        {{-- K3 Data --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k3']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k3_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k3_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k3_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k3_target'] }}</td>
                        <td class="table-cell font-bold">{{ number_format($region['summary']['k3_ttr_comply'], 2) }}%</td>
                        <td class="table-cell font-bold" style="color: {{ $region['summary']['k3_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($region['summary']['k3_ach'], 2) }}%</td>
                        {{-- Rata2 & Total --}}
                        <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($region['summary']['rata2_ach'], 2) }}%</td>
                        <td class="table-cell bg-gray-600 font-bold">{{ number_format($region['summary']['total_tickets']) }}</td>
                    </tr>

                    @foreach ($region['witels'] as $witel)
                        {{-- BARIS WITEL --}}
                        <tr class="hidden font-semibold bg-gray-700 bg-opacity-50 @if(!$witel['datels']->isEmpty()) cursor-pointer hover:bg-gray-600 expandable @endif" 
                            data-parent="region-{{ $loop->parent->index }}" data-id="witel-{{ $loop->parent->index }}-{{ $loop->index }}" data-level="2">
                            <td class="table-cell text-left-indent pl-10">
                                @if(!$witel['datels']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                                {{ $witel['name'] }}
                            </td>
                           {{-- K1 Data --}}
                           <td class="table-cell">{{ number_format($witel['summary']['sid_k1']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k1_comply']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k1_not_comply']) }}</td>
                           <td class="table-cell font-semibold">{{ number_format($witel['summary']['k1_total']) }}</td>
                           <td class="table-cell">{{ $witel['summary']['k1_target'] }}</td>
                           <td class="table-cell font-bold">{{ number_format($witel['summary']['k1_ttr_comply'], 2) }}%</td>
                           <td class="table-cell font-bold" style="color: {{ $witel['summary']['k1_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($witel['summary']['k1_ach'], 2) }}%</td>
                           {{-- K2 Data --}}
                           <td class="table-cell">{{ number_format($witel['summary']['sid_k2']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k2_comply']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k2_not_comply']) }}</td>
                           <td class="table-cell font-semibold">{{ number_format($witel['summary']['k2_total']) }}</td>
                           <td class="table-cell">{{ $witel['summary']['k2_target'] }}</td>
                           <td class="table-cell font-bold">{{ number_format($witel['summary']['k2_ttr_comply'], 2) }}%</td>
                           <td class="table-cell font-bold" style="color: {{ $witel['summary']['k2_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($witel['summary']['k2_ach'], 2) }}%</td>
                           {{-- K3 Data --}}
                           <td class="table-cell">{{ number_format($witel['summary']['sid_k3']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k3_comply']) }}</td>
                           <td class="table-cell">{{ number_format($witel['summary']['k3_not_comply']) }}</td>
                           <td class="table-cell font-semibold">{{ number_format($witel['summary']['k3_total']) }}</td>
                           <td class="table-cell">{{ $witel['summary']['k3_target'] }}</td>
                           <td class="table-cell font-bold">{{ number_format($witel['summary']['k3_ttr_comply'], 2) }}%</td>
                           <td class="table-cell font-bold" style="color: {{ $witel['summary']['k3_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($witel['summary']['k3_ach'], 2) }}%</td>
                           {{-- Rata2 & Total --}}
                           <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($witel['summary']['rata2_ach'], 2) }}%</td>
                           <td class="table-cell bg-gray-600 font-bold">{{ number_format($witel['summary']['total_tickets']) }}</td>
                        </tr>
                        
                        @foreach ($witel['datels'] as $datel)
                            {{-- BARIS DATEL --}}
                            <tr class="hidden font-medium bg-gray-800 @if(!$datel['stos']->isEmpty()) cursor-pointer hover:bg-gray-700 expandable @endif" 
                                data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-id="datel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}" data-level="3">
                                <td class="table-cell text-left-indent pl-20">
                                    @if(!$datel['stos']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                                    {{ $datel['name'] }}
                                </td>
                                {{-- K1 Data --}}
                                <td class="table-cell">{{ number_format($datel['summary']['sid_k1']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k1_comply']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k1_not_comply']) }}</td>
                                <td class="table-cell font-semibold">{{ number_format($datel['summary']['k1_total']) }}</td>
                                <td class="table-cell">{{ $datel['summary']['k1_target'] }}</td>
                                <td class="table-cell font-bold">{{ number_format($datel['summary']['k1_ttr_comply'], 2) }}%</td>
                                <td class="table-cell font-bold" style="color: {{ $datel['summary']['k1_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($datel['summary']['k1_ach'], 2) }}%</td>
                                {{-- K2 Data --}}
                                <td class="table-cell">{{ number_format($datel['summary']['sid_k2']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k2_comply']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k2_not_comply']) }}</td>
                                <td class="table-cell font-semibold">{{ number_format($datel['summary']['k2_total']) }}</td>
                                <td class="table-cell">{{ $datel['summary']['k2_target'] }}</td>
                                <td class="table-cell font-bold">{{ number_format($datel['summary']['k2_ttr_comply'], 2) }}%</td>
                                <td class="table-cell font-bold" style="color: {{ $datel['summary']['k2_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($datel['summary']['k2_ach'], 2) }}%</td>
                                {{-- K3 Data --}}
                                <td class="table-cell">{{ number_format($datel['summary']['sid_k3']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k3_comply']) }}</td>
                                <td class="table-cell">{{ number_format($datel['summary']['k3_not_comply']) }}</td>
                                <td class="table-cell font-semibold">{{ number_format($datel['summary']['k3_total']) }}</td>
                                <td class="table-cell">{{ $datel['summary']['k3_target'] }}</td>
                                <td class="table-cell font-bold">{{ number_format($datel['summary']['k3_ttr_comply'], 2) }}%</td>
                                <td class="table-cell font-bold" style="color: {{ $datel['summary']['k3_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($datel['summary']['k3_ach'], 2) }}%</td>
                                {{-- Rata2 & Total --}}
                                <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($datel['summary']['rata2_ach'], 2) }}%</td>
                                <td class="table-cell bg-gray-600 font-bold">{{ number_format($datel['summary']['total_tickets']) }}</td>
                            </tr>

                            @foreach ($datel['stos'] as $sto)
                                {{-- BARIS STO --}}
                                <tr class="hidden" data-parent="datel-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-level="4">
                                    <td class="table-cell text-left-indent pl-28">{{ $sto['name'] }}</td>
                                    {{-- K1 Data --}}
                                    <td class="table-cell">{{ number_format($sto['summary']['sid_k1']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k1_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k1_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($sto['summary']['k1_total']) }}</td>
                                    <td class="table-cell">{{ $sto['summary']['k1_target'] }}</td>
                                    <td class="table-cell font-bold">{{ number_format($sto['summary']['k1_ttr_comply'], 2) }}%</td>
                                    <td class="table-cell font-bold" style="color: {{ $sto['summary']['k1_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($sto['summary']['k1_ach'], 2) }}%</td>
                                    {{-- K2 Data --}}
                                    <td class="table-cell">{{ number_format($sto['summary']['sid_k2']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k2_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k2_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($sto['summary']['k2_total']) }}</td>
                                    <td class="table-cell">{{ $sto['summary']['k2_target'] }}</td>
                                    <td class="table-cell font-bold">{{ number_format($sto['summary']['k2_ttr_comply'], 2) }}%</td>
                                    <td class="table-cell font-bold" style="color: {{ $sto['summary']['k2_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($sto['summary']['k2_ach'], 2) }}%</td>
                                    {{-- K3 Data --}}
                                    <td class="table-cell">{{ number_format($sto['summary']['sid_k3']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k3_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['k3_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($sto['summary']['k3_total']) }}</td>
                                    <td class="table-cell">{{ $sto['summary']['k3_target'] }}</td>
                                    <td class="table-cell font-bold">{{ number_format($sto['summary']['k3_ttr_comply'], 2) }}%</td>
                                    <td class="table-cell font-bold" style="color: {{ $sto['summary']['k3_ach'] >= 100 ? '#22c55e' : '#ef4444' }};">{{ number_format($sto['summary']['k3_ach'], 2) }}%</td>
                                    {{-- Rata2 & Total --}}
                                    <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($sto['summary']['rata2_ach'], 2) }}%</td>
                                    <td class="table-cell bg-gray-600 font-bold">{{ number_format($sto['summary']['total_tickets']) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @empty
                    <tr><td colspan="23" class="table-cell">Tidak ada data untuk ditampilkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
{{-- Javascript expand/collapse tidak perlu diubah --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('datin-table-body');
    tableBody.addEventListener('click', function(event) {
        const targetRow = event.target.closest('tr.expandable');
        if (!targetRow) return;

        const id = targetRow.dataset.id;
        const children = tableBody.querySelectorAll(`tr[data-parent="${id}"]`);
        targetRow.classList.toggle('expanded');
        
        const isHidden = !targetRow.classList.contains('expanded');

        children.forEach(child => {
            child.classList.toggle('hidden', isHidden);
            if (isHidden && child.classList.contains('expandable')) {
                child.classList.remove('expanded');
                const childId = child.dataset.id;
                const grandChildren = tableBody.querySelectorAll(`tr[data-parent^="${childId}"]`);
                grandChildren.forEach(grandChild => {
                    grandChild.classList.add('hidden');
                    if(grandChild.classList.contains('expandable')) {
                         grandChild.classList.remove('expanded');
                    }
                });
            }
        });
    });
});
</script>
@endpush