@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">Dashboard Datin</h1>

        {{-- Filter Section --}}
        <div class="bg-gray-800 p-4 rounded-lg space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
            <div class="flex flex-wrap items-center gap-4 flex-grow">
                <input type="date" id="startDate" class="bg-gray-700 text-white border-gray-600 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <input type="date" id="endDate" class="bg-gray-700 text-white border-gray-600 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <select id="regionFilter" class="bg-gray-700 text-white border-gray-600 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Regions</option>
                </select>
                <select id="cityFilter" class="bg-gray-700 text-white border-gray-600 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Cities</option>
                </select>
                <select id="categoryFilter" class="bg-gray-700 text-white border-gray-600 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Categories</option>
                    <option value="k1">K1</option>
                    <option value="k2">K2</option>
                    <option value="k3">K3</option>
                </select>
            </div>
            <div class="flex items-center space-x-2 flex-shrink-0">
                <button id="applyFilters" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Apply</button>
                <button id="clearFilters" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-md">Clear</button>
                <button id="addDataBtn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">Add Data</button>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto bg-gray-900 rounded-lg">
            <table class="w-full text-xs text-gray-300 table-fixed">
                <colgroup>
                    {{-- Memberi lebar spesifik pada kolom pertama (Wilayah) --}}
                    <col style="width: 14%;">
                    {{-- Memberi lebar pada 21 kolom data --}}
                    <col span="21" style="width: 3.5%;">
                    {{-- Memberi lebar pada 2 kolom terakhir --}}
                    <col span="2" style="width: 5%;">
                </colgroup>
                <thead class="bg-gray-800 text-xs text-gray-400 uppercase tracking-wider align-middle">
                    <tr>
                        <th rowspan="2" class="px-2 py-3 text-left">Wilayah</th>
                        <th colspan="7" class="px-1 py-2 text-center border-l border-r border-gray-700">K1</th>
                        <th colspan="7" class="px-1 py-2 text-center border-r border-gray-700">K2</th>
                        <th colspan="7" class="px-1 py-2 text-center border-r border-gray-700">K3</th>
                        {{-- Menambahkan break-words agar teks bisa turun --}}
                        <th rowspan="2" class="px-1 py-2 text-center border-l border-gray-700 break-words">Avg AC</th>
                        <th rowspan="2" class="px-1 py-2 text-center break-words">Total Tickets</th>
                    </tr>
                    <tr>
                        {{-- Mempersingkat teks header dan mengatur padding --}}
                        <th class="px-1 py-2 text-center border-l border-gray-700">SID</th><th class="px-1 py-2 text-center">Comply</th><th class="px-1 py-2 text-center">NC</th><th class="px-1 py-2 text-center">Total</th><th class="px-1 py-2 text-center">Target</th><th class="px-1 py-2 text-center">TTR</th><th class="px-1 py-2 text-center border-r border-gray-700">AC</th>
                        <th class="px-1 py-2 text-center">SID</th><th class="px-1 py-2 text-center">Comply</th><th class="px-1 py-2 text-center">NC</th><th class="px-1 py-2 text-center">Total</th><th class="px-1 py-2 text-center">Target</th><th class="px-1 py-2 text-center">TTR</th><th class="px-1 py-2 text-center border-r border-gray-700">AC</th>
                        <th class="px-1 py-2 text-center">SID</th><th class="px-1 py-2 text-center">Comply</th><th class="px-1 py-2 text-center">NC</th><th class="px-1 py-2 text-center">Total</th><th class="px-1 py-2 text-center">Target</th><th class="px-1 py-2 text-center">TTR</th><th class="px-1 py-2 text-center border-r border-gray-700">AC</th>
                    </tr>
                </thead>
                <tbody id="dashboardTableBody" class="divide-y divide-gray-700">
                    {{-- Data akan dimuat di sini oleh JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal "Add Data" -->
    <div id="addDataModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-gray-800 p-8 rounded-lg w-full max-w-lg">
            <h2 class="text-xl font-bold mb-6">Add New Data</h2>
            <form id="addDataForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="modalEntryDate" class="block text-sm font-medium text-gray-300">Entry Date</label>
                        <input type="date" id="modalEntryDate" name="entry_date" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white">
                    </div>
                    <div>
                        <label for="modalCategory" class="block text-sm font-medium text-gray-300">Category</label>
                        <select id="modalCategory" name="category" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white">
                            <option value="K1">K1</option>
                            <option value="K2">K2</option>
                            <option value="K3">K3</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="modalRegion" class="block text-sm font-medium text-gray-300">Regional</label>
                        <select id="modalRegion" name="region_id" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white">
                            <option value="">Pilih Regional</option>
                        </select>
                    </div>
                    <div>
                        <label for="modalCity" class="block text-sm font-medium text-gray-300">City</label>
                        <select id="modalCity" name="city_id" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white">
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label for="modalSid" class="block text-sm">SID</label><input type="number" id="modalSid" name="sid" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white"></div>
                    <div><label for="modalComply" class="block text-sm">Comply</label><input type="number" id="modalComply" name="comply" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white"></div>
                    <div><label for="modalNotComply" class="block text-sm">Not Comply</label><input type="number" id="modalNotComply" name="not_comply" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label for="modalTarget" class="block text-sm">Target (%)</label><input type="number" step="0.01" id="modalTarget" name="target" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white"></div>
                    <div><label for="modalTtrComply" class="block text-sm">TTR Comply</label><input type="number" id="modalTtrComply" name="ttr_comply" class="mt-1 w-full bg-gray-700 rounded-md p-2 text-white"></div>
                </div>
                
                <div id="modal-errors" class="text-red-500 text-sm pt-2"></div>
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" id="closeModalBtn" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-md">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Save Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="userMenuDropdown" class="hidden"></div>
    <button id="userMenuButton" class="hidden"></button> 
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
