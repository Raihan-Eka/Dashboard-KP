@extends('layouts.app')

@push('styles')
<style>
    /* Mengatur style untuk baris yang bisa di-klik */
    .parent-row { cursor: pointer; }
    .parent-row:hover { background-color: #4A5568; }

    /* Indentasi untuk level anak */
    .level-1 td:first-child { font-weight: bold; }
    .level-2 td:first-child { padding-left: 2.5rem !important; }
    .level-3 td:first-child { padding-left: 4.5rem !important; }
    .level-4 td:first-child { padding-left: 6.5rem !important; }

    /* Style untuk ikon expand/collapse */
    .toggle-icon {
        display: inline-block;
        width: 1em;
        transition: transform 0.2s;
        font-style: normal;
        font-weight: bold;
        margin-right: 8px;
    }
    .parent-row:not(.collapsed) .toggle-icon {
        transform: rotate(90deg);
    }
    /* Sembunyikan semua baris anak secara default dengan CSS */
    tr[class*="child-of-"] {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">Resume TTR - HSI (INDIBIZ)</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-600 border-green-700 text-white p-4 rounded-lg mb-6" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-600 border-red-700 text-white p-4 rounded-lg mb-6" role="alert">
            <p class="font-bold">Error!</p><p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('monitoring.hsi') }}" method="GET" class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <span class="text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="bg-gray-700 text-white rounded px-2 py-1 text-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">SUBMIT</button>
                <a href="{{ route('monitoring.hsi') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">Clear</a>
            </form>
            <button id="openUploadModalBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-sm">
                Upload Data
            </button>
        </div>
    </div>

    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        @php
            // Fungsi pembantu untuk mencetak baris <td> agar tidak duplikasi kode
            function print_hsi_tds($summary) {
                // Tentukan kelas warna berdasarkan kondisi
                $h4_real_class = ($summary->h4_real < $summary->h4_target) ? 'text-red-400' : '';
                $h4_ach_class = ($summary->h4_ach < 100) ? 'text-red-400' : 'text-green-400';
                $h24_real_class = ($summary->h24_real < $summary->h24_target) ? 'text-red-400' : '';
                $h24_ach_class = ($summary->h24_ach < 100) ? 'text-red-400' : 'text-green-400';
                
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_comply) . "</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_not_comply) . "</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_target, 0) . "%</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-semibold $h4_real_class'>" . number_format($summary->h4_real, 1) . "%</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap font-bold border border-gray-600 text-center $h4_ach_class'>" . number_format($summary->h4_ach, 1) . "%</td>";
                
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_comply) . "</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_not_comply) . "</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_target, 0) . "%</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center font-semibold $h24_real_class'>" . number_format($summary->h24_real, 1) . "%</td>";
                echo "<td class='p-3 text-sm whitespace-nowrap font-bold border border-gray-600 text-center $h24_ach_class'>" . number_format($summary->h24_ach, 1) . "%</td>";
                
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->total_tiket) . "</td>";
            }

            // Fungsi rekursif untuk mencetak baris tabel secara bertingkat
            function print_summary_row($item, $level = 1, $parentId = '', $isReg3 = false) {
                $summary = $item->summary;
                $rowId = \Illuminate\Support\Str::slug($parentId . '-' . $item->name, '-');
                $parentClass = !empty($parentId) ? 'child-of-'.\Illuminate\Support\Str::slug($parentId, '-') : '';
                $hasChildren = (isset($item->witels) && count($item->witels) > 0) || (isset($item->children) && count($item->children) > 0) || (isset($item->workzones) && count($item->workzones) > 0);

                echo "<tr class='level-$level $parentClass " . ($hasChildren ? 'parent-row collapsed' : '') . "' data-id='$rowId'>";
                echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-left'>";
                if ($hasChildren) { echo "<i class='toggle-icon'>></i>"; }
                echo e($item->name) . "</td>";
                print_hsi_tds($summary);
                echo "</tr>";

                if ($hasChildren) {
                    $nextLevel = $level + 1;
                    $children = $item->witels ?? $item->children ?? $item->workzones ?? [];
                    $isNowReg3 = ($level == 1 && $item->name == 'REG-3');
                    foreach($children as $child) {
                        print_summary_row($child, $nextLevel, $rowId, $isNowReg3);
                    }
                }
            }
        @endphp

        <table class="min-w-full text-white">
            <thead class="bg-gray-700">
                <tr>
                    <th class="p-3 text-sm font-semibold tracking-wide text-left border border-gray-600 w-1/4">ITEM</th>
                    <th colspan="5" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">4H</th>
                    <th colspan="5" class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">24H</th>
                    <th class="p-3 text-sm font-semibold tracking-wide text-center border border-gray-600">TIKET</th>
                </tr>
                 <tr class="bg-gray-700">
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600"></th>
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
                    <th class="p-2 text-xs font-semibold tracking-wide text-center border border-gray-600"></th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse ($dataRegions as $region)
                    @php print_summary_row($region, 1, '', ($region->name == 'REG-3')) @endphp
                @empty
                    <tr><td colspan="12" class="text-center p-4 border border-gray-600">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </tbody>
             <tfoot class="bg-gray-600 font-bold">
                <tr>
                    <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-left">NASIONAL</td>
                    @php if(isset($dataNasional)) print_hsi_tds($dataNasional); @endphp
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- POP-UP (MODAL) UNTUK UPLOAD FILE --}}
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">
        <div class="border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">Upload Data HSI (Excel)</h3>
            <button id="closeUploadModalBtn" class="text-gray-400 hover:text-white text-3xl">&times;</button>
        </div>
        <div class="p-6">
            <form action="{{ route('hsi.upload.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="hsi_excel" class="block text-sm font-medium text-gray-300 mb-2">Pilih File Excel (.xlsx, .xls)</label>
                <input type="file" name="hsi_excel" id="hsi_excel" required
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
document.addEventListener('DOMContentLoaded', function() {
    // --- Logika untuk Tabel Drill-Down ---
    const tableBody = document.querySelector('tbody');
    if (tableBody) {
        tableBody.addEventListener('click', function(event) {
            const targetRow = event.target.closest('tr.parent-row');
            if (!targetRow) return;
            
            targetRow.classList.toggle('collapsed');
            const id = targetRow.dataset.id;
            const children = document.querySelectorAll('.child-of-' + id);
            
            children.forEach(function(child) {
                if (targetRow.classList.contains('collapsed')) {
                    child.style.display = 'none';
                    if (child.classList.contains('parent-row')) {
                        child.classList.add('collapsed');
                    }
                } else {
                    child.style.display = 'table-row';
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