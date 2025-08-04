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
    .tab-active {
        background-color: #4A5568; /* bg-gray-700 */
        border-color: #6366F1; /* border-indigo-500 */
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">Dashboard Datin</h1>
        <button id="openModalBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
            + Tambah Data
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-600 border border-green-700 text-white p-4 rounded-lg mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-600 border border-red-700 text-white p-4 rounded-lg mb-6" role="alert">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-8">
        <div class="flex items-center space-x-4">
            <form action="{{ route('datin') }}" method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <span class="text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">Apply</button>
                <a href="{{ route('datin') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">Clear</a>
            </form>
            <a href="{{ route('datin.download', ['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center ml-auto">
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
                    <th class="header-group">SID</th><th class="header-group">Comply</th><th class="header-group">Not Comply</th><th class="header-group">Total</th><th class="header-group">Target K1</th><th class="header-group">TTR Comply K1</th><th class="header-group">Ach</th>
                    <th class="header-group">SID</th><th class="header-group">Comply</th><th class="header-group">Not Comply</th><th class="header-group">Total</th><th class="header-group">Target K2</th><th class="header-group">TTR Comply K2</th><th class="header-group">Ach</th>
                    <th class="header-group">SID</th><th class="header-group">Comply</th><th class="header-group">Not Comply</th><th class="header-group">Total</th><th class="header-group">Target K3</th><th class="header-group">TTR Comply K3</th><th class="header-group">Ach</th>
                </tr>
            </thead>
            <tbody id="datin-table-body">
                @forelse ($dataRegions as $region)
                    {{-- REGIONAL ROW --}}
                    <tr class="font-bold bg-gray-700 @if(!$region['witels']->isEmpty()) cursor-pointer hover:bg-gray-600 expandable @endif" data-id="region-{{ $loop->index }}" data-level="1">
                        <td class="table-cell text-left-indent">@if(!$region['witels']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $region['name'] }}</td>
                        {{-- K1 --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k1']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k1_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k1_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k1_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k1_target'] }}%</td>
                        <td class="table-cell font-bold @if($region['summary']['k1_total'] > 0 && $region['summary']['k1_ttr_comply'] < $region['summary']['k1_target']) text-red-500 @endif">{{ $region['summary']['k1_total'] > 0 ? number_format($region['summary']['k1_ttr_comply'], 2).'%' : '-' }}</td>
                        <td class="table-cell font-bold @if($region['summary']['k1_total'] > 0) {{ $region['summary']['k1_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $region['summary']['k1_total'] > 0 ? number_format($region['summary']['k1_ach'], 2).'%' : '-' }}</td>
                        {{-- K2 --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k2']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k2_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k2_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k2_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k2_target'] }}%</td>
                        <td class="table-cell font-bold @if($region['summary']['k2_total'] > 0 && $region['summary']['k2_ttr_comply'] < $region['summary']['k2_target']) text-red-500 @endif">{{ $region['summary']['k2_total'] > 0 ? number_format($region['summary']['k2_ttr_comply'], 2).'%' : '-' }}</td>
                        <td class="table-cell font-bold @if($region['summary']['k2_total'] > 0) {{ $region['summary']['k2_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $region['summary']['k2_total'] > 0 ? number_format($region['summary']['k2_ach'], 2).'%' : '-' }}</td>
                        {{-- K3 --}}
                        <td class="table-cell">{{ number_format($region['summary']['sid_k3']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k3_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['k3_not_comply']) }}</td>
                        <td class="table-cell font-semibold">{{ number_format($region['summary']['k3_total']) }}</td>
                        <td class="table-cell">{{ $region['summary']['k3_target'] }}%</td>
                        <td class="table-cell font-bold @if($region['summary']['k3_total'] > 0 && $region['summary']['k3_ttr_comply'] < $region['summary']['k3_target']) text-red-500 @endif">{{ $region['summary']['k3_total'] > 0 ? number_format($region['summary']['k3_ttr_comply'], 2).'%' : '-' }}</td>
                        <td class="table-cell font-bold @if($region['summary']['k3_total'] > 0) {{ $region['summary']['k3_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $region['summary']['k3_total'] > 0 ? number_format($region['summary']['k3_ach'], 2).'%' : '-' }}</td>
                        {{-- Rata2 & Total --}}
                        <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($region['summary']['rata2_ach'], 2) }}%</td>
                        <td class="table-cell bg-gray-600 font-bold">{{ number_format($region['summary']['total_tickets']) }}</td>
                    </tr>
                    @foreach ($region['witels'] as $witel)
                        @php
                            $isReg3 = ($region['name'] === 'REG-3');
                            $hasChildren = $isReg3 ? !$witel['hsas']->isEmpty() : !$witel['workzones_direct']->isEmpty();
                        @endphp
                        {{-- WITEL ROW --}}
                        <tr class="hidden font-semibold bg-gray-700 bg-opacity-50 @if($hasChildren) cursor-pointer hover:bg-gray-600 expandable @endif" data-parent="region-{{ $loop->parent->index }}" data-id="witel-{{ $loop->parent->index }}-{{ $loop->index }}" data-level="2">
                            <td class="table-cell text-left-indent pl-10">@if($hasChildren)<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $witel['name'] }}</td>
                             {{-- K1 --}}
                            <td class="table-cell">{{ number_format($witel['summary']['sid_k1']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k1_comply']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k1_not_comply']) }}</td>
                            <td class="table-cell font-semibold">{{ number_format($witel['summary']['k1_total']) }}</td>
                            <td class="table-cell">{{ $witel['summary']['k1_target'] }}%</td>
                            <td class="table-cell font-bold @if($witel['summary']['k1_total'] > 0 && $witel['summary']['k1_ttr_comply'] < $witel['summary']['k1_target']) text-red-500 @endif">{{ $witel['summary']['k1_total'] > 0 ? number_format($witel['summary']['k1_ttr_comply'], 2).'%' : '-' }}</td>
                            <td class="table-cell font-bold @if($witel['summary']['k1_total'] > 0) {{ $witel['summary']['k1_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $witel['summary']['k1_total'] > 0 ? number_format($witel['summary']['k1_ach'], 2).'%' : '-' }}</td>
                            {{-- K2 --}}
                            <td class="table-cell">{{ number_format($witel['summary']['sid_k2']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k2_comply']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k2_not_comply']) }}</td>
                            <td class="table-cell font-semibold">{{ number_format($witel['summary']['k2_total']) }}</td>
                            <td class="table-cell">{{ $witel['summary']['k2_target'] }}%</td>
                            <td class="table-cell font-bold @if($witel['summary']['k2_total'] > 0 && $witel['summary']['k2_ttr_comply'] < $witel['summary']['k2_target']) text-red-500 @endif">{{ $witel['summary']['k2_total'] > 0 ? number_format($witel['summary']['k2_ttr_comply'], 2).'%' : '-' }}</td>
                            <td class="table-cell font-bold @if($witel['summary']['k2_total'] > 0) {{ $witel['summary']['k2_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $witel['summary']['k2_total'] > 0 ? number_format($witel['summary']['k2_ach'], 2).'%' : '-' }}</td>
                            {{-- K3 --}}
                            <td class="table-cell">{{ number_format($witel['summary']['sid_k3']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k3_comply']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['k3_not_comply']) }}</td>
                            <td class="table-cell font-semibold">{{ number_format($witel['summary']['k3_total']) }}</td>
                            <td class="table-cell">{{ $witel['summary']['k3_target'] }}%</td>
                            <td class="table-cell font-bold @if($witel['summary']['k3_total'] > 0 && $witel['summary']['k3_ttr_comply'] < $witel['summary']['k3_target']) text-red-500 @endif">{{ $witel['summary']['k3_total'] > 0 ? number_format($witel['summary']['k3_ttr_comply'], 2).'%' : '-' }}</td>
                            <td class="table-cell font-bold @if($witel['summary']['k3_total'] > 0) {{ $witel['summary']['k3_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $witel['summary']['k3_total'] > 0 ? number_format($witel['summary']['k3_ach'], 2).'%' : '-' }}</td>
                            {{-- Rata2 & Total --}}
                            <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($witel['summary']['rata2_ach'], 2) }}%</td>
                            <td class="table-cell bg-gray-600 font-bold">{{ number_format($witel['summary']['total_tickets']) }}</td>
                        </tr>
                        @if ($isReg3)
                            @foreach ($witel['hsas'] as $hsa)
                                {{-- HSA ROW --}}
                                <tr class="hidden font-medium bg-gray-800 @if(!$hsa['workzones']->isEmpty()) cursor-pointer hover:bg-gray-700 expandable @endif" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-id="hsa-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}" data-level="3">
                                    <td class="table-cell text-left-indent pl-20">@if(!$hsa['workzones']->isEmpty())<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif{{ $hsa['name'] }}</td>
                                     {{-- K1 --}}
                                    <td class="table-cell">{{ number_format($hsa['summary']['sid_k1']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k1_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k1_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($hsa['summary']['k1_total']) }}</td>
                                    <td class="table-cell">{{ $hsa['summary']['k1_target'] }}%</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k1_total'] > 0 && $hsa['summary']['k1_ttr_comply'] < $hsa['summary']['k1_target']) text-red-500 @endif">{{ $hsa['summary']['k1_total'] > 0 ? number_format($hsa['summary']['k1_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k1_total'] > 0) {{ $hsa['summary']['k1_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $hsa['summary']['k1_total'] > 0 ? number_format($hsa['summary']['k1_ach'], 2).'%' : '-' }}</td>
                                    {{-- K2 --}}
                                    <td class="table-cell">{{ number_format($hsa['summary']['sid_k2']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k2_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k2_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($hsa['summary']['k2_total']) }}</td>
                                    <td class="table-cell">{{ $hsa['summary']['k2_target'] }}%</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k2_total'] > 0 && $hsa['summary']['k2_ttr_comply'] < $hsa['summary']['k2_target']) text-red-500 @endif">{{ $hsa['summary']['k2_total'] > 0 ? number_format($hsa['summary']['k2_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k2_total'] > 0) {{ $hsa['summary']['k2_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $hsa['summary']['k2_total'] > 0 ? number_format($hsa['summary']['k2_ach'], 2).'%' : '-' }}</td>
                                    {{-- K3 --}}
                                    <td class="table-cell">{{ number_format($hsa['summary']['sid_k3']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k3_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['k3_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($hsa['summary']['k3_total']) }}</td>
                                    <td class="table-cell">{{ $hsa['summary']['k3_target'] }}%</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k3_total'] > 0 && $hsa['summary']['k3_ttr_comply'] < $hsa['summary']['k3_target']) text-red-500 @endif">{{ $hsa['summary']['k3_total'] > 0 ? number_format($hsa['summary']['k3_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($hsa['summary']['k3_total'] > 0) {{ $hsa['summary']['k3_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $hsa['summary']['k3_total'] > 0 ? number_format($hsa['summary']['k3_ach'], 2).'%' : '-' }}</td>
                                    {{-- Rata2 & Total --}}
                                    <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($hsa['summary']['rata2_ach'], 2) }}%</td>
                                    <td class="table-cell bg-gray-600 font-bold">{{ number_format($hsa['summary']['total_tickets']) }}</td>
                                </tr>
                                @foreach ($hsa['workzones'] as $workzone)
                                    {{-- WORKZONE ROW under HSA --}}
                                    <tr class="hidden" data-parent="hsa-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                        <td class="table-cell text-left-indent pl-28">{{ $workzone['name'] }}</td>
                                        {{-- K1 --}}
                                        <td class="table-cell">{{ number_format($workzone['summary']['sid_k1']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k1_comply']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k1_not_comply']) }}</td>
                                        <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k1_total']) }}</td>
                                        <td class="table-cell">{{ $workzone['summary']['k1_target'] }}%</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k1_total'] > 0 && $workzone['summary']['k1_ttr_comply'] < $workzone['summary']['k1_target']) text-red-500 @endif">{{ $workzone['summary']['k1_total'] > 0 ? number_format($workzone['summary']['k1_ttr_comply'], 2).'%' : '-' }}</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k1_total'] > 0) {{ $workzone['summary']['k1_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k1_total'] > 0 ? number_format($workzone['summary']['k1_ach'], 2).'%' : '-' }}</td>
                                        {{-- K2 --}}
                                        <td class="table-cell">{{ number_format($workzone['summary']['sid_k2']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k2_comply']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k2_not_comply']) }}</td>
                                        <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k2_total']) }}</td>
                                        <td class="table-cell">{{ $workzone['summary']['k2_target'] }}%</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k2_total'] > 0 && $workzone['summary']['k2_ttr_comply'] < $workzone['summary']['k2_target']) text-red-500 @endif">{{ $workzone['summary']['k2_total'] > 0 ? number_format($workzone['summary']['k2_ttr_comply'], 2).'%' : '-' }}</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k2_total'] > 0) {{ $workzone['summary']['k2_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k2_total'] > 0 ? number_format($workzone['summary']['k2_ach'], 2).'%' : '-' }}</td>
                                        {{-- K3 --}}
                                        <td class="table-cell">{{ number_format($workzone['summary']['sid_k3']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k3_comply']) }}</td>
                                        <td class="table-cell">{{ number_format($workzone['summary']['k3_not_comply']) }}</td>
                                        <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k3_total']) }}</td>
                                        <td class="table-cell">{{ $workzone['summary']['k3_target'] }}%</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k3_total'] > 0 && $workzone['summary']['k3_ttr_comply'] < $workzone['summary']['k3_target']) text-red-500 @endif">{{ $workzone['summary']['k3_total'] > 0 ? number_format($workzone['summary']['k3_ttr_comply'], 2).'%' : '-' }}</td>
                                        <td class="table-cell font-bold @if($workzone['summary']['k3_total'] > 0) {{ $workzone['summary']['k3_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k3_total'] > 0 ? number_format($workzone['summary']['k3_ach'], 2).'%' : '-' }}</td>
                                        {{-- Rata2 & Total --}}
                                        <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($workzone['summary']['rata2_ach'], 2) }}%</td>
                                        <td class="table-cell bg-gray-600 font-bold">{{ number_format($workzone['summary']['total_tickets']) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            @foreach ($witel['workzones_direct'] as $workzone)
                                {{-- WORKZONE ROW under Witel --}}
                                <tr class="hidden" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                    <td class="table-cell text-left-indent pl-20">{{ $workzone['name'] }}</td>
                                    {{-- K1 --}}
                                    <td class="table-cell">{{ number_format($workzone['summary']['sid_k1']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k1_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k1_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k1_total']) }}</td>
                                    <td class="table-cell">{{ $workzone['summary']['k1_target'] }}%</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k1_total'] > 0 && $workzone['summary']['k1_ttr_comply'] < $workzone['summary']['k1_target']) text-red-500 @endif">{{ $workzone['summary']['k1_total'] > 0 ? number_format($workzone['summary']['k1_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k1_total'] > 0) {{ $workzone['summary']['k1_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k1_total'] > 0 ? number_format($workzone['summary']['k1_ach'], 2).'%' : '-' }}</td>
                                    {{-- K2 --}}
                                    <td class="table-cell">{{ number_format($workzone['summary']['sid_k2']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k2_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k2_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k2_total']) }}</td>
                                    <td class="table-cell">{{ $workzone['summary']['k2_target'] }}%</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k2_total'] > 0 && $workzone['summary']['k2_ttr_comply'] < $workzone['summary']['k2_target']) text-red-500 @endif">{{ $workzone['summary']['k2_total'] > 0 ? number_format($workzone['summary']['k2_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k2_total'] > 0) {{ $workzone['summary']['k2_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k2_total'] > 0 ? number_format($workzone['summary']['k2_ach'], 2).'%' : '-' }}</td>
                                    {{-- K3 --}}
                                    <td class="table-cell">{{ number_format($workzone['summary']['sid_k3']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k3_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($workzone['summary']['k3_not_comply']) }}</td>
                                    <td class="table-cell font-semibold">{{ number_format($workzone['summary']['k3_total']) }}</td>
                                    <td class="table-cell">{{ $workzone['summary']['k3_target'] }}%</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k3_total'] > 0 && $workzone['summary']['k3_ttr_comply'] < $workzone['summary']['k3_target']) text-red-500 @endif">{{ $workzone['summary']['k3_total'] > 0 ? number_format($workzone['summary']['k3_ttr_comply'], 2).'%' : '-' }}</td>
                                    <td class="table-cell font-bold @if($workzone['summary']['k3_total'] > 0) {{ $workzone['summary']['k3_ach'] >= 100 ? 'text-green-500' : 'text-red-500' }} @endif">{{ $workzone['summary']['k3_total'] > 0 ? number_format($workzone['summary']['k3_ach'], 2).'%' : '-' }}</td>
                                    {{-- Rata2 & Total --}}
                                    <td class="table-cell font-bold" style="background-color: #6b21a8;">{{ number_format($workzone['summary']['rata2_ach'], 2) }}%</td>
                                    <td class="table-cell bg-gray-600 font-bold">{{ number_format($workzone['summary']['total_tickets']) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @empty
                    <tr><td colspan="23" class="text-center p-4">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL --}}
<div id="addDataModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl">
        <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">Tambah Data Datin</h3>
            <button id="closeModalBtn" class="text-gray-400 hover:text-white text-3xl">&times;</button>
        </div>
        <div class="px-6 py-2 border-b border-gray-700">
            <nav class="flex space-x-4">
                <button data-tab="excel" class="tab-button text-white py-2 px-4 border-b-2 font-medium tab-active">Upload Massal (Excel)</button>
            </nav>
        </div>
        <div class="p-6">
            <div id="excelContent" class="tab-content">
                <form action="{{ route('datin.upload.excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="datin_excel" class="block text-sm font-medium text-gray-300 mb-2">Pilih File Excel (.xlsx, .xls)</label>
                        <input type="file" name="datin_excel" id="datin_excel" required class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Upload & Proses File</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk tabel drill-down dan modal digabungkan --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Logika untuk Tabel Drill-Down ---
    const tableBody = document.getElementById('datin-table-body');
    if (tableBody) {
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
    }

    // --- Logika untuk Modal/Pop-up ---
    const modal = document.getElementById('addDataModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const tabs = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    if (modal && openModalBtn && closeModalBtn) {
        openModalBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        const closeModal = () => modal.classList.add('hidden');
        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(item => {
                    item.classList.remove('tab-active', 'text-white');
                    item.classList.add('text-gray-400');
                });
                tabContents.forEach(content => content.classList.add('hidden'));

                tab.classList.add('tab-active', 'text-white');
                tab.classList.remove('text-gray-400');
                document.getElementById(tab.dataset.tab + 'Content').classList.remove('hidden');
            });
        });
    }
});
</script>
@endpush