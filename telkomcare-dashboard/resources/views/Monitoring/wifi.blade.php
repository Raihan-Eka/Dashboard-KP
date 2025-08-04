@extends('layouts.app')

@push('styles')
<style>
    .cursor-pointer .fa-chevron-right { transition: transform 0.2s ease-in-out; }
    .expanded .fa-chevron-right { transform: rotate(90deg); }
    .table-cell { padding: 0.75rem; text-align: center; white-space: nowrap; }
    .text-left-indent { text-align: left; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">TTR Compliance - Wifi</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-600 border border-green-700 text-white p-4 rounded-lg mb-6" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-600 border border-red-700 text-white p-4 rounded-lg mb-6" role="alert">
            <p class="font-bold">Error!</p><p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('monitoring.wifi') }}" method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <span class="text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">Apply</button>
                <a href="{{ route('monitoring.wifi') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">Clear</a>
            </form>
            <div class="flex items-center space-x-2">
                 <a href="{{ route('monitoring.wifi.download', ['start_date' => $filters['start_date'] ?? null, 'end_date' => $filters['end_date'] ?? null]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center">
                    <i class="fas fa-download mr-2"></i> Download
                </a>
                <button id="openUploadModalBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-sm">
                    Upload Data
                </button>
            </div>
        </div>
    </div>
    
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-white text-sm">
            <thead class="bg-gray-700">
                <tr>
                    <th class="table-cell text-left-indent font-semibold px-4">Wilayah</th>
                    <th class="table-cell font-semibold">Target</th>
                    <th class="table-cell font-semibold">Comply</th>
                    <th class="table-cell font-semibold">Not Comply</th>
                    <th class="table-cell font-semibold">Total</th>
                    <th class="table-cell font-semibold">Compliance (%)</th>
                    <th class="table-cell font-semibold">Achv (%)</th>
                </tr>
            </thead>
            <tbody id="wifi-table-body" class="divide-y divide-gray-700">
                @foreach ($dataRegions as $region)
                    @php $hasWitels = !$region['witels']->isEmpty(); @endphp
                    <tr class="@if($hasWitels) expandable cursor-pointer hover:bg-gray-700 @endif @if($loop->even) bg-gray-900 bg-opacity-50 @endif" data-id="region-{{ $loop->index }}">
                        <td class="table-cell text-left-indent font-bold px-4">
                            @if($hasWitels)<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                            {{ $region['name'] }}
                        </td>
                        <td class="table-cell">{{ number_format($region['summary']['target'], 2) }}%</td>
                        <td class="table-cell">{{ number_format($region['summary']['comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['not_comply']) }}</td>
                        <td class="table-cell">{{ number_format($region['summary']['total']) }}</td>
                        <td class="table-cell font-semibold {{ $region['summary']['compliance_percentage'] < $region['summary']['target'] ? 'text-red-400' : '' }}">{{ number_format($region['summary']['compliance_percentage'], 2) }}%</td>
                        <td class="table-cell font-bold {{ $region['summary']['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400' }}">{{ number_format($region['summary']['achv_percentage'], 2) }}%</td>
                    </tr>

                    @foreach ($region['witels'] as $witel)
                        @php
                            $isReg3 = ($region['name'] === 'REG-3');
                            $hasChildren = $isReg3 ? !$witel['hsas']->isEmpty() : !$witel['stos_direct']->isEmpty();
                        @endphp
                        <tr class="hidden @if($hasChildren) expandable cursor-pointer hover:bg-gray-700 @endif @if($loop->even) bg-gray-900 bg-opacity-50 @endif" data-parent="region-{{ $loop->parent->index }}" data-id="witel-{{ $loop->parent->index }}-{{ $loop->index }}">
                            <td class="table-cell text-left-indent pl-10">
                                @if($hasChildren)<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                                {{ $witel['name'] }}
                            </td>
                            <td class="table-cell">{{ number_format($witel['summary']['target'], 2) }}%</td>
                            <td class="table-cell">{{ number_format($witel['summary']['comply']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['not_comply']) }}</td>
                            <td class="table-cell">{{ number_format($witel['summary']['total']) }}</td>
                            <td class="table-cell font-semibold {{ $witel['summary']['compliance_percentage'] < $witel['summary']['target'] ? 'text-red-400' : '' }}">{{ number_format($witel['summary']['compliance_percentage'], 2) }}%</td>
                            <td class="table-cell font-bold {{ $witel['summary']['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400' }}">{{ number_format($witel['summary']['achv_percentage'], 2) }}%</td>
                        </tr>

                        @if ($isReg3)
                            @foreach ($witel['hsas'] as $hsa)
                                @php $hasStos = !$hsa['stos']->isEmpty(); @endphp
                                <tr class="hidden @if($hasStos) expandable cursor-pointer hover:bg-gray-700 @endif @if($loop->even) bg-gray-900 bg-opacity-50 @endif" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-id="hsa-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    <td class="table-cell text-left-indent pl-16">
                                        @if($hasStos)<i class="fas fa-chevron-right fa-fw mr-2"></i>@endif
                                        {{ $hsa['name'] }}
                                    </td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['target'], 2) }}%</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['comply']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['not_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($hsa['summary']['total']) }}</td>
                                    <td class="table-cell font-semibold {{ $hsa['summary']['compliance_percentage'] < $hsa['summary']['target'] ? 'text-red-400' : '' }}">{{ number_format($hsa['summary']['compliance_percentage'], 2) }}%</td>
                                    <td class="table-cell font-bold {{ $hsa['summary']['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400' }}">{{ number_format($hsa['summary']['achv_percentage'], 2) }}%</td>
                                </tr>
                                @foreach ($hsa['stos'] as $sto)
                                    <tr class="hidden @if($loop->even) bg-gray-900 bg-opacity-50 @endif" data-parent="hsa-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                        <td class="table-cell text-left-indent pl-24">{{ $sto['name'] }}</td>
                                        <td class="table-cell">{{ number_format($sto['summary']['target'], 2) }}%</td>
                                        <td class="table-cell">{{ number_format($sto['summary']['comply']) }}</td>
                                        <td class="table-cell">{{ number_format($sto['summary']['not_comply']) }}</td>
                                        <td class="table-cell">{{ number_format($sto['summary']['total']) }}</td>
                                        <td class="table-cell font-semibold {{ $sto['summary']['compliance_percentage'] < $sto['summary']['target'] ? 'text-red-400' : '' }}">{{ number_format($sto['summary']['compliance_percentage'], 2) }}%</td>
                                        <td class="table-cell font-bold {{ $sto['summary']['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400' }}">{{ number_format($sto['summary']['achv_percentage'], 2) }}%</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            @foreach ($witel['stos_direct'] as $sto)
                                <tr class="hidden @if($loop->even) bg-gray-900 bg-opacity-50 @endif" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                    <td class="table-cell text-left-indent pl-16">{{ $sto['name'] }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['target'], 2) }}%</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['comply']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['not_comply']) }}</td>
                                    <td class="table-cell">{{ number_format($sto['summary']['total']) }}</td>
                                    <td class="table-cell font-semibold {{ $sto['summary']['compliance_percentage'] < $sto['summary']['target'] ? 'text-red-400' : '' }}">{{ number_format($sto['summary']['compliance_percentage'], 2) }}%</td>
                                    <td class="table-cell font-bold {{ $sto['summary']['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400' }}">{{ number_format($sto['summary']['achv_percentage'], 2) }}%</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </tbody>
            <tfoot class="font-bold bg-gray-900">
                <tr>
                    <td class="table-cell text-left-indent pl-4">NASIONAL</td>
                    <td class="table-cell">{{ number_format($dataNasional['target'], 2) }}%</td>
                    <td class="table-cell">{{ number_format($dataNasional['comply']) }}</td>
                    <td class="table-cell">{{ number_format($dataNasional['not_comply']) }}</td>
                    <td class="table-cell">{{ number_format($dataNasional['total']) }}</td>
                    @php
                        $complianceColor = $dataNasional['compliance_percentage'] < $dataNasional['target'] ? 'text-red-400' : '';
                        $achvColor = $dataNasional['achv_percentage'] < 100 ? 'text-red-400' : 'text-green-400';
                    @endphp
                    <td class="table-cell font-semibold {{ $complianceColor }}">{{ number_format($dataNasional['compliance_percentage'], 2) }}%</td>
                    <td class="table-cell font-bold {{ $achvColor }}">{{ number_format($dataNasional['achv_percentage'], 2) }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- POP-UP (MODAL) UNTUK UPLOAD FILE --}}
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">
        <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">Upload Data WiFi (Excel)</h3>
            <button id="closeUploadModalBtn" class="text-gray-400 hover:text-white text-3xl">&times;</button>
        </div>
        <div class="p-6">
            <form action="{{ route('wifi.upload.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="wifi_excel" class="block text-sm font-medium text-gray-300 mb-2">Pilih File Excel (.xlsx, .xls)</label>
                <input type="file" name="wifi_excel" id="wifi_excel" required
                       class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md
                              file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white
                              hover:file:bg-gray-600">
                <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
                    Upload & Proses File
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Logika untuk Tabel Drill-Down ---
    const tableBody = document.getElementById('wifi-table-body');
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
                    const grandChildren = tableBody.querySelectorAll(`tr[data-parent="${child.dataset.id}"]`);
                    grandChildren.forEach(grandChild => grandChild.classList.add('hidden'));
                }
            });
        });
    }

    // --- Logika untuk Modal Upload ---
    const modal = document.getElementById('uploadModal');
    const openBtn = document.getElementById('openUploadModalBtn');
    const closeBtn = document.getElementById('closeUploadModalBtn');

    if (modal && openBtn && closeBtn) {
        const closeModal = () => modal.classList.add('hidden');
        
        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
});
</script>
@endpush