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
    </style>
</head>
<body class="flex h-screen">

    <!-- Sidebar dengan efek hover -->
    <aside class="group w-20 hover:w-64 bg-gray-800 text-white p-4 flex flex-col justify-between transition-all duration-300 ease-in-out">
        <div>
            {{-- Logo TelkomCare --}}
            <a href="{{ route('home') }}" class="flex items-center mb-10">
                <img src="{{ asset('images/logo.svg') }}" alt="TelkomCare Logo" class="h-10 w-auto flex-shrink-0">
                {{-- Tulisan "TelkomCare" dihilangkan sesuai permintaan --}}
            </a>
            
            <nav>
                <ul>
                    <!-- Menu Home -->
                    <li class="mb-4">
                        <a href="{{ route('home') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-home fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">Home</span>
                        </a>
                    </li>
                    
                    <!-- Menu Datin (Hanya tampil jika sudah login) -->
                    @auth
                    <li>
                        <a href="{{ route('datin') }}" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-chart-bar fa-fw fa-lg w-8 text-center"></i>
                            <span class="ml-2 hidden group-hover:inline">Datin</span>
                        </a>
                    </li>
                    @endauth
                </ul>
            </nav>
        </div>

        <!-- Bagian User & Logout di bawah -->
        <div>
            @auth
                <!-- Jika sudah login -->
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
                <!-- Jika belum login -->
                <a href="{{ route('login') }}" class="w-full flex items-center p-2 rounded hover:bg-blue-700 bg-blue-600">
                    <i class="fas fa-sign-in-alt fa-fw fa-lg w-8 text-center"></i>
                    <span class="ml-2 hidden group-hover:inline">Login</span>
                </a>
            @endauth
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-y-auto">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
