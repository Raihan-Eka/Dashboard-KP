@extends('layouts.app')

@push('styles')
<style>
    /* Mengadopsi style dari wifi.blade.php */
    .parent-row { cursor: pointer; }
    .parent-row:hover { background-color: #4A5568; }
    .parent-row.expanded .fa-chevron-right { transform: rotate(90deg); }

    /* Indentasi untuk level anak */
    .level-1 { font-weight: bold; background-color: #374151; } /* BG Sedikit lebih gelap untuk Regional */
    .level-2 td:first-child { padding-left: 2.5rem !important; }
    .level-3 td:first-child { padding-left: 4.5rem !important; }
    .level-4 td:first-child { padding-left: 6.5rem !important; }

    /* Style untuk ikon expand/collapse */
    .toggle-icon {
        display: inline-block;
        width: 14px; /* Sesuaikan lebar ikon */
        transition: transform 0.2s ease-in-out;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-4 text-white">Resume TTR - HSI (INDIBIZ)</h1>

    {{-- Filter Tanggal --}}
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
        <form method="GET" action="{{ route('monitoring.hsi') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div>
                <label for="start_date" class="text-sm font-medium text-gray-300">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 block w-full bg-gray-700 border-gray-700 rounded-md shadow-sm text-white p-2">
            </div>
            <div>
                <label for="end_date" class="text-sm font-medium text-gray-300">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 block w-full bg-gray-700 border-gray-700 rounded-md shadow-sm text-white p-2">
            </div>
            <div class="flex items-end gap-2 mt-5 sm:mt-0">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">SUBMIT</button>
                <a href="{{ route('monitoring.hsi') }}" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md text-center">Clear</a>
            </div>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-gray-800 p-4 rounded-lg shadow-lg overflow-x-auto">
        @php
        // Fungsi pembantu untuk mencetak baris <td> agar tidak duplikasi kode
        function print_hsi_tds($summary) {
            $h4_ach_class = $summary->h4_ach >= 100 ? 'text-green-400' : 'text-red-400';
            $h24_ach_class = $summary->h24_ach >= 100 ? 'text-green-400' : 'text-red-400';
            
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_comply) . "</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_not_comply) . "</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_target, 0) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h4_real, 1) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap font-bold border border-gray-600 text-center $h4_ach_class'>" . number_format($summary->h4_ach, 1) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_comply) . "</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_not_comply) . "</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_target, 0) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->h24_real, 1) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap font-bold border border-gray-600 text-center $h24_ach_class'>" . number_format($summary->h24_ach, 1) . "%</td>";
            echo "<td class='p-3 text-sm whitespace-nowrap border border-gray-600 text-center'>" . number_format($summary->total_tiket) . "</td>";
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
            <tbody id="hsi-table-body" class="bg-gray-800 divide-y divide-gray-700">
                @forelse ($dataRegions as $region)
                    @php $hasWitels = !$region->witels->isEmpty(); @endphp
                    <tr class="level-1 @if($hasWitels) parent-row @endif" data-id="region-{{ $loop->index }}">
                        <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">
                            @if($hasWitels)<i class="fas fa-chevron-right toggle-icon"></i>@endif{{ $region->name }}
                        </td>
                        @php print_hsi_tds($region->summary) @endphp
                    </tr>

                    @foreach ($region->witels as $witel)
                        @php 
                            $isReg3 = ($region->name === 'REG-3');
                            $hasChildren = !$witel->children->isEmpty();
                        @endphp
                        <tr class="level-2 hidden @if($hasChildren) parent-row @endif" data-parent="region-{{ $loop->parent->index }}" data-id="witel-{{ $loop->parent->index }}-{{ $loop->index }}">
                            <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">
                                @if($hasChildren)<i class="fas fa-chevron-right toggle-icon"></i>@endif{{ $witel->name }}
                            </td>
                            @php print_hsi_tds($witel->summary) @endphp
                        </tr>

                        @foreach ($witel->children as $child)
                            @if ($isReg3)
                                {{-- Anak dari Witel di REG-3 adalah HSA --}}
                                @php $hasWorkzones = !$child->workzones->isEmpty(); @endphp
                                <tr class="level-3 hidden @if($hasWorkzones) parent-row @endif" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}" data-id="hsa-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">
                                        @if($hasWorkzones)<i class="fas fa-chevron-right toggle-icon"></i>@endif{{ $child->name }}
                                    </td>
                                    @php print_hsi_tds($child->summary) @endphp
                                </tr>
                                @foreach($child->workzones as $workzone)
                                    <tr class="level-4 hidden" data-parent="hsa-{{ $loop->parent->parent->parent->index }}-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                         <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">{{ $workzone->name }}</td>
                                         @php print_hsi_tds($workzone->summary) @endphp
                                    </tr>
                                @endforeach
                            @else
                                {{-- Anak dari Witel di regional lain adalah Workzone --}}
                                <tr class="level-3 hidden" data-parent="witel-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}">
                                    <td class="p-3 text-sm whitespace-nowrap border border-gray-600 text-left">{{ $child->name }}</td>
                                     @php print_hsi_tds($child->summary) @endphp
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @empty
                    <tr><td colspan="12" class="text-center p-4">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            ...
            </tbody>

            {{-- TAMBAHKAN BLOK FOOTER INI --}}
            <tfoot class="bg-gray-600 font-bold">
                <tr>
                    <td class="p-3 text-sm whitespace-nowrap border border-gray-500 text-left">NASIONAL</td>
                    @php print_hsi_tds($dataNasional) @endphp
                </tr>
            </tfoot>
            
        </table>
        ...
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('hsi-table-body');
    if (tableBody) {
        tableBody.addEventListener('click', function(event) {
            // Hanya trigger jika baris yang bisa di-expand yang diklik
            const targetRow = event.target.closest('tr.parent-row');
            if (!targetRow) return;

            const id = targetRow.dataset.id;
            const children = tableBody.querySelectorAll(`tr[data-parent="${id}"]`);
            targetRow.classList.toggle('expanded');
            
            // Toggle visibility anak-anaknya
            children.forEach(child => {
                child.classList.toggle('hidden');
                // Jika parent ditutup, tutup juga semua cucu/cicitnya
                if (child.classList.contains('hidden') && child.classList.contains('parent-row')) {
                    child.classList.remove('expanded');
                    const grandChildren = tableBody.querySelectorAll(`tr[data-parent="${child.dataset.id}"]`);
                    grandChildren.forEach(grandChild => grandChild.classList.add('hidden'));
                }
            });
        });
    }
});
</script>
@endpush