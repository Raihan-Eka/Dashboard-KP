<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EbisCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @stack('styles')
    <style>
        body { background-color: #1f2937; color: #f9fafb; }
        .sidebar-active {
            background-color: #374151; /* bg-gray-700 */
            color: #ffffff;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="group w-20 hover:w-64 bg-gray-800 text-white p-4 flex flex-col transition-all duration-300 ease-in-out flex-shrink-0">
        
        {{-- Bagian atas: Logo dan Menu --}}
        <div class="flex-grow">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo.svg') }}" alt="TelkomCare Logo" class="h-10 w-auto flex-shrink-0">
            </a>
            
            <nav class="mt-16">
                <ul>
                    <li class="mb-4">
                        <a href="{{ route('home') }}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('home') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-home fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">Home</span>
                        </a>
                    </li>
                    @auth
                    <li class="mb-2">
                        <a href="{{ route('datin') }}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('datin') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-chart-bar fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">Datin</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('monitoring.wifi') }}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('monitoring.wifi') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-wifi fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">TTR Wifi</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('monitoring.hsi') }}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('monitoring.hsi') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-network-wired fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">TTR HSI</span>
                        </a>
                    </li>
                    @endauth
                </ul>
            </nav>
        </div>

        {{-- Bagian bawah: Logout (didorong ke bawah oleh mt-auto) --}}
        <div class="mt-auto">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-2 rounded hover:bg-red-700 bg-red-600">
                        <i class="fas fa-sign-out-alt fa-fw fa-lg w-8 text-center"></i> 
                        <span class="ml-2 hidden group-hover:inline">Logout</span>
                    </button>
                    <div class="mt-2 text-center overflow-hidden">
                        <span class="text-xs hidden group-hover:inline">{{ Auth::user()->name }}</span>
                    </div>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full flex items-center p-2 rounded hover:bg-blue-700 bg-blue-600">
                    <i class="fas fa-sign-in-alt fa-fw fa-lg w-8 text-center"></i>
                    <span class="ml-2 hidden group-hover:inline">Login</span>
                </a>
            @endauth
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-900">
        <div class="p-6 h-full">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>