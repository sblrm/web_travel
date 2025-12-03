<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Background dengan pattern Indonesian -->
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left Side - Decorative Panel -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-cyan-50 via-blue-50 to-cyan-100 p-12 items-center justify-center relative overflow-hidden">
                <!-- Decorative Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 left-10 text-9xl">🏛️</div>
                    <div class="absolute bottom-20 right-20 text-8xl">🗿</div>
                    <div class="absolute top-1/3 right-10 text-7xl">🎭</div>
                    <div class="absolute bottom-1/3 left-20 text-6xl">🏯</div>
                </div>
                
                <div class="relative z-10 text-center text-gray-900">
                    <div class="text-8xl mb-8">🏛️</div>
                    <h1 class="text-5xl font-bold mb-6 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 bg-clip-text text-transparent">CulturalTrip</h1>
                    <p class="text-2xl font-semibold leading-relaxed text-gray-800">
                        Jelajahi Keindahan Budaya Indonesia<br>
                        dari Sabang sampai Merauke
                    </p>
                    <div class="mt-12 flex items-center justify-center gap-6 text-xl">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-cyan-600">100+</div>
                            <div class="font-medium text-gray-700">Destinasi</div>
                        </div>
                        <div class="text-5xl text-gray-400">•</div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-cyan-600">27</div>
                            <div class="font-medium text-gray-700">Provinsi</div>
                        </div>
                        <div class="text-5xl text-gray-400">•</div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-cyan-600">7</div>
                            <div class="font-medium text-gray-700">Kategori</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form Panel -->
            <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
                <div class="w-full max-w-md">
                    <!-- Logo for Mobile -->
                    <div class="lg:hidden text-center mb-8">
                        <a href="/" class="inline-flex items-center gap-3 mb-4">
                            <span class="text-5xl">🏛️</span>
                            <span class="text-3xl font-bold bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 bg-clip-text text-transparent">
                                CulturalTrip
                            </span>
                        </a>
                        <p class="text-gray-600 text-lg font-medium">Jelajahi Budaya Indonesia</p>
                    </div>

                    <!-- Form Card -->
                    <div class="bg-white rounded-2xl shadow-2xl p-8 sm:p-10 border-t-4 border-cyan-500">
                        {{ $slot }}
                    </div>

                    <!-- Back to Home Link -->
                    <div class="text-center mt-6">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-cyan-600 transition font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
