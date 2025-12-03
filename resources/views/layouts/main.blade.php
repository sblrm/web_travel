<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="antialiased bg-white">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50 border-b-4 border-cyan-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-10">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <span class="text-4xl">🏛️</span>
                        <span class="text-2xl font-bold bg-gradient-to-r from-cyan-600 to-cyan-500 bg-clip-text text-transparent">
                            CulturalTrip
                        </span>
                    </a>
                    
                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Beranda
                        </a>
                        <a href="{{ route('destinations.index') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Jelajahi
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <!-- Notifications Dropdown -->
                        <div x-data="{ open: false, unreadCount: {{ auth()->user()->unreadNotifications->count() }} }" class="relative">
                            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-cyan-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"></span>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold text-gray-900">Notifikasi</h3>
                                        @if(auth()->user()->unreadNotifications->count() > 0)
                                            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">
                                                    Tandai semua dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    @forelse(auth()->user()->notifications->take(10) as $notification)
                                        <a href="{{ route('notifications.mark-read', $notification->id) }}" class="block p-4 hover:bg-gray-50 transition {{ $notification->read_at ? 'bg-white' : 'bg-cyan-50' }}">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-1">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center text-white">
                                                        🎉
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 line-clamp-2">
                                                        {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-8 text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-gray-500">Belum ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Dashboard
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Profil
                        </a>
                        <a href="{{ route('analytics.index') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Analytics
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-bold shadow-lg">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-lg font-semibold text-gray-900 hover:text-cyan-600 transition">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-bold shadow-lg">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 mx-auto max-w-7xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 mx-auto max-w-7xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 border-t-4 border-cyan-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-4xl">🏛️</span>
                        <span class="text-3xl font-bold bg-gradient-to-r from-cyan-400 to-cyan-500 bg-clip-text text-transparent">
                            CulturalTrip
                        </span>
                    </div>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Temukan keindahan budaya Indonesia di ujung jari Anda. Eksplorasi lebih dari 100 destinasi wisata budaya dari Sabang sampai Merauke.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-white text-xl mb-6">Navigasi</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-lg hover:text-cyan-400 transition font-medium">Beranda</a></li>
                        <li><a href="{{ route('destinations.index') }}" class="text-lg hover:text-cyan-400 transition font-medium">Jelajahi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-white text-xl mb-6">Kontak</h3>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        <strong class="text-white">Email:</strong> info@culturaltrip.com<br>
                        <strong class="text-white">© {{ date('Y') }}</strong> CulturalTrip
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
