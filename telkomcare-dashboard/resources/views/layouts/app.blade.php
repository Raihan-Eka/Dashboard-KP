<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TelkomCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #111827; color: #f9fafb; }
        /* Style untuk menu aktif di sidebar */
        .sidebar-active {
            background-color: #374151; /* bg-gray-700 */
            color: #ffffff;
        }
    </style>
</head>
<body class="flex h-screen">

    <aside class="group w-20 hover:w-64 bg-gray-800 text-white p-4 flex flex-col justify-between transition-all duration-300 ease-in-out">
        <div>
            {{-- Logo TelkomCare --}}
            <a href="{{ route('home') }}" class="flex items-center mb-10">
                <img src="{{ asset('images/logo.svg') }}" alt="TelkomCare Logo" class="h-10 w-auto flex-shrink-0">
            </a>
            
            <nav>
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

                    {{-- ===== MENU BARU DITAMBAHKAN DI SINI ===== --}}
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
                    {{-- ========================================== --}}

                    @endauth
                </ul>
            </nav>
        </div>

        <div>
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

    <main class="flex-1 p-6 overflow-y-auto">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>