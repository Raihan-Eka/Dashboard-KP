<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TelkomCare Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-gray-300 font-sans pb-14">
    <div class="main-wrapper">
        <div class="sidebar">
            <div class="sidebar-menu">
                <a href="#" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span class="menu-text">Home</span>
                </a>
            </div>
        </div>

        <header class="bg-gray-800 shadow-md p-4 flex justify-between items-center sticky top-0 z-40">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold whitespace-nowrap">TelkomCare</h1>
            </div>
            
            <div class="relative">
                <div id="userMenuButton" class="cursor-pointer flex items-center">
                    <span class="mr-3 font-semibold">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-user-circle text-2xl"></i>
                </div>
                
                <div id="userMenuDropdown" class="absolute top-full right-0 mt-2 w-56 bg-gray-700 rounded-md shadow-lg hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-600">
                        <p class="text-sm font-semibold text-white">Signed in as</p>
                        <p class="text-sm font-medium text-gray-400 truncate">{{ Auth::user()->username ?? 'admin' }}</p>
                    </div>
                    <div class="p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-red-600 hover:text-white rounded-md transition-colors duration-200">
                                <i class="fas fa-sign-out-alt w-6 text-center mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 lg:p-8">
            <div class="bg-gray-800 shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-white">Filter Data</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-400">Start Date:</label>
                        <input type="date" id="startDate" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-400">End Date:</label>
                        <input type="date" id="endDate" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="regionFilter" class="block text-sm font-medium text-gray-400">Regional:</label>
                        <select id="regionFilter" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">All Regions</option>
                        </select>
                    </div>
                    <div>
                        <label for="cityFilter" class="block text-sm font-medium text-gray-400">Kota:</label>
                        <select id="cityFilter" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">All Cities</option>
                        </select>
                    </div>
                    <div class="lg:col-span-1 xl:col-span-1">
                        <label for="categoryFilter" class="block text-sm font-medium text-gray-400">Kategori:</label>
                        <select id="categoryFilter" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">All</option>
                            <option value="K1">K1</option>
                            <option value="K2">K2</option>
                            <option value="K3">K3</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button id="clearFilters" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400">Clear</button>
                    <button id="applyFilters" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">SUBMIT</button>
                </div>
            </div>
            <div class="bg-gray-800 shadow-md rounded-lg">
                <div class="flex justify-between items-center p-6">
                    <h2 class="text-xl font-semibold text-white">DATIN</h2>
                    <button id="addDataBtn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Tambah Data</button>
                </div>
                <div class="overflow-x-auto">
                    <table id="dashboardTable" class="min-w-full text-xs">
                        <thead class="bg-gray-700">
                            <tr>
                                <th rowspan="2" class="py-2 px-2 text-center font-semibold">TREG</th>
                                <th colspan="7" class="py-2 px-2 text-center font-semibold border-l border-r border-gray-600">K1</th>
                                <th colspan="7" class="py-2 px-2 text-center font-semibold border-r border-gray-600">K2</th>
                                <th colspan="7" class="py-2 px-2 text-center font-semibold border-r border-gray-600">K3</th>
                                <th rowspan="2" class="py-2 px-2 text-center font-semibold border-r border-gray-600">Rata2 Ach</th>
                                <th rowspan="2" class="py-2 px-2 text-center font-semibold">TIKET</th>
                            </tr>
                            <tr>
                                <th class="py-2 px-1 text-center font-medium border-l border-gray-600">SID</th>
                                <th class="py-2 px-1 text-center font-medium">Comply</th>
                                <th class="py-2 px-1 text-center font-medium">Not Comp</th>
                                <th class="py-2 px-1 text-center font-medium">Total</th>
                                <th class="py-2 px-1 text-center font-medium">Target</th>
                                <th class="py-2 px-1 text-center font-medium">TTR Comp</th>
                                <th class="py-2 px-1 text-center font-medium border-r border-gray-600">Ach</th>
                                <th class="py-2 px-1 text-center font-medium">SID</th>
                                <th class="py-2 px-1 text-center font-medium">Comply</th>
                                <th class="py-2 px-1 text-center font-medium">Not Comp</th>
                                <th class="py-2 px-1 text-center font-medium">Total</th>
                                <th class="py-2 px-1 text-center font-medium">Target</th>
                                <th class="py-2 px-1 text-center font-medium">TTR Comp</th>
                                <th class="py-2 px-1 text-center font-medium border-r border-gray-600">Ach</th>
                                <th class="py-2 px-1 text-center font-medium">SID</th>
                                <th class="py-2 px-1 text-center font-medium">Comply</th>
                                <th class="py-2 px-1 text-center font-medium">Not Comp</th>
                                <th class="py-2 px-1 text-center font-medium">Total</th>
                                <th class="py-2 px-1 text-center font-medium">Target</th>
                                <th class="py-2 px-1 text-center font-medium">TTR Comp</th>
                                <th class="py-2 px-1 text-center font-medium border-r border-gray-600">Ach</th>
                            </tr>
                        </thead>
                        <tbody id="dashboardTableBody" class="bg-gray-800">
                            <tr><td colspan="25" class="text-center py-4">No data available. Apply filters or add new data.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <footer class="fixed bottom-0 left-0 w-full bg-gray-800 text-center p-4 text-gray-500 text-sm z-10">
        &copy; 2025 TelkomCare. All Rights Reserved.
    </footer>

    <div id="addDataModal" class="fixed inset-0 bg-black bg-opacity-70 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-gray-800">
            <h3 class="text-lg font-semibold mb-4 text-white">Tambah Data Dashboard</h3>
            <form id="addDataForm">
                <div class="mb-4">
                    <label for="modalEntryDate" class="block text-sm font-medium text-gray-400">Tanggal Masuk:</label>
                    <input type="date" id="modalEntryDate" name="entry_date" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modalRegion" class="block text-sm font-medium text-gray-400">Regional:</label>
                        <select id="modalRegion" name="region_id" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" required>
                            <option value="">Pilih Regional</option>
                        </select>
                    </div>
                    <div>
                        <label for="modalCity" class="block text-sm font-medium text-gray-400">Kota:</label>
                        <select id="modalCity" name="city_id" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" required>
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="modalCategory" class="block text-sm font-medium text-gray-400">Kategori:</label>
                    <select id="modalCategory" name="category" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" required>
                        <option value="">Pilih Kategori</option>
                        <option value="K1">K1</option>
                        <option value="K2">K2</option>
                        <option value="K3">K3</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modalSID" class="block text-sm font-medium text-gray-400">SID:</label>
                        <input type="number" id="modalSID" name="sid" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" min="0" required>
                    </div>
                    <div>
                        <label for="modalComply" class="block text-sm font-medium text-gray-400">Comply:</label>
                        <input type="number" id="modalComply" name="comply" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" min="0" required>
                    </div>
                    <div>
                        <label for="modalNotComply" class="block text-sm font-medium text-gray-400">Not Comply:</label>
                        <input type="number" id="modalNotComply" name="not_comply" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" min="0" required>
                    </div>
                    <div>
                        <label for="modalTarget" class="block text-sm font-medium text-gray-400">Target (%):</label>
                        <input type="number" id="modalTarget" name="target" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" min="0" max="100" step="0.01" required>
                    </div>
                    <div>
                        <label for="modalTTRComply" class="block text-sm font-medium text-gray-400">TTR Comply:</label>
                        <input type="number" id="modalTTRComply" name="ttr_comply" class="mt-1 block w-full bg-gray-700 text-white rounded-md border-gray-600 shadow-sm" min="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ticket Count:</label>
                        <input type="number" value="1" disabled class="mt-1 block w-full bg-gray-900 text-gray-400 rounded-md border-gray-700 shadow-sm cursor-not-allowed">
                    </div>
                </div>
                <div id="modal-errors" class="text-red-400 text-sm mb-4"></div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Memuat kembali file JavaScript eksternal --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
