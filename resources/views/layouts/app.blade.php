<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SI-AKIK') }}</title>
    <link rel="icon" type="image/png" href="https://siakik.kecbandungkab.com/images/logo-kab-bandung.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bedas: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a', // Primary Green Bedas
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">
    <!-- Global Loader -->
    <div id="global-loader"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-white bg-opacity-90 transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-bedas-600 mb-4"></div>
            <p class="text-bedas-800 font-semibold animate-pulse">Memuat...</p>
        </div>
    </div>
    <script>
        window.addEventListener('load', function () {
            const loader = document.getElementById('global-loader');
            loader.classList.add('opacity-0');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        });

        window.addEventListener('beforeunload', function () {
            const loader = document.getElementById('global-loader');
            loader.style.display = 'flex';
            setTimeout(() => {
                loader.classList.remove('opacity-0');
            }, 10);
        });

        // Handle Back/Forward Cache
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                const loader = document.getElementById('global-loader');
                loader.classList.add('opacity-0');
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300);
            }
        });
    </script>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-bedas-800 text-white transition-transform duration-300 transform lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Logo -->
            <div class="flex flex-col items-center justify-center py-8 bg-bedas-900 shadow-md">
                <img src="https://siakik.kecbandungkab.com/images/logo-kab-bandung.png" alt="Logo" class="w-16 h-auto mb-3">
                <h1 class="text-2xl font-bold tracking-wider">SI-AKIK<span class="text-bedas-400">.</span></h1>
            </div>

            <!-- Nav Links -->
            <nav class="mt-8 px-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-bedas-700 text-white shadow-lg' : 'text-bedas-100 hover:bg-bedas-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('bku.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('bku.*') ? 'bg-bedas-700 text-white shadow-lg' : 'text-bedas-100 hover:bg-bedas-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                    <span class="font-medium">Data BKU</span>
                </a>

                @if(auth()->user()->role !== 'pptk')
                    <a href="{{ route('anggaran.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('anggaran.*') ? 'bg-bedas-700 text-white shadow-lg' : 'text-bedas-100 hover:bg-bedas-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-medium">Data Anggaran</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-bedas-700 text-white shadow-lg' : 'text-bedas-100 hover:bg-bedas-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Manajemen Pengguna</span>
                    </a>
                @endif

                @if(!in_array(auth()->user()->role, ['camat', 'pptk']))
                    <a href="{{ route('settings.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('settings.*') ? 'bg-bedas-700 text-white shadow-lg' : 'text-bedas-100 hover:bg-bedas-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium">Pengaturan</span>
                    </a>
                @endif

                <!-- Add more links here later -->
            </nav>

            <!-- Bottom Section -->
            <div class="absolute bottom-0 w-full p-4 bg-bedas-900">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2 text-bedas-200 hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Mobile Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b lg:hidden">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center ml-4">
                        <img src="https://siakik.kecbandungkab.com/images/logo-kab-bandung.png" alt="Logo" class="w-8 h-auto mr-2">
                        <span class="text-xl font-bold text-gray-800">SI-AKIK</span>
                    </div>
                </div>
            </header>

            <!-- Overlay for mobile sidebar -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
                class="fixed inset-0 z-40 bg-black opacity-50 lg:hidden"
                x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-50" x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-50" x-transition:leave-end="opacity-0">
            </div>

            <!-- Top Bar (Desktop) -->
            <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white shadow-sm z-10">
                <h2 class="text-xl font-semibold text-gray-800">
                    @yield('title', 'Dashboard')
                </h2>
                <div class="flex items-center space-x-6">
                    @php
                        $unvalidatedCount = 0;
                        if (Auth::check() && Auth::user()->role === 'pptk') {
                            $unvalidatedCount = \App\Models\BkuTransaksi::where('pptk_id', Auth::user()->pejabat_id)
                                ->where('status_validasi', false)
                                ->count();
                        }
                    @endphp

                    @if($unvalidatedCount > 0)
                        <a href="{{ route('bku.index') }}"
                            class="relative text-gray-500 hover:text-bedas-600 transition p-2 rounded-full hover:bg-gray-100"
                            title="{{ $unvalidatedCount }} Kuitansi butuh validasi">
                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full shadow-sm">{{ $unvalidatedCount }}</span>
                        </a>
                    @endif

                    <div class="flex items-center space-x-3 border-l pl-6 border-gray-200">
                        <div class="bg-bedas-100 p-2 rounded-full">
                            <svg class="w-6 h-6 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-gray-700">
                                @php
                                    $displayName = Auth::user()->name;
                                    if (Auth::user()->role === 'admin') {
                                        $bendahara = \App\Models\Pejabat::where('jabatan', 'Bendahara')->first();
                                        if ($bendahara) {
                                            $displayName = $bendahara->nama;
                                        }
                                    }
                                @endphp
                                {{ $displayName }}
                            </p>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Global Delete Confirmation Modal -->
    <div x-data="{ 
        open: false, 
        action: '', 
        title: 'Hapus Data?', 
        message: 'Tindakan ini tidak dapat dibatalkan. Data akan dihapus secara permanen dari sistem.' 
    }" x-on:open-delete-modal.window="open = true; action = $event.detail.action; title = $event.detail.title || 'Hapus Data?'; message = $event.detail.message || 'Tindakan ini tidak dapat dibatalkan. Data akan dihapus secara permanen dari sistem.'"
        x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="open"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false">
        </div>

        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[110] overflow-hidden transform transition-all"
                x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="p-6 text-center">
                    <div
                        class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="title"></h3>
                    <p class="text-gray-500 mb-6" x-text="message"></p>

                    <div class="flex flex-col gap-3">
                        <form :action="action" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition transform hover:-translate-y-0.5 active:scale-95">
                                Ya, Hapus Sekarang
                            </button>
                        </form>
                        <button @click="open = false"
                            class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition active:scale-95">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>