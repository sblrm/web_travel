@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-cyan-50 via-blue-50 to-cyan-100 overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1596422846543-75c6fc197f07?q=80&w=2128')] bg-cover bg-center opacity-5"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="text-center max-w-5xl mx-auto">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-8 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 bg-clip-text text-transparent leading-tight" style="line-height: 1.2;">
                Temukan Keindahan Budaya Indonesia di Ujung Jari Kamu
            </h1>
            <p class="text-xl md:text-2xl text-gray-900 font-medium mb-10 leading-relaxed">
                Jelajahi lebih dari {{ $totalDestinations }} destinasi wisata budaya dari {{ $totalProvinces }} provinsi di Indonesia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('destinations.index') }}" class="px-8 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Mulai Jelajahi
                </a>
                <a href="#fitur" class="px-8 py-4 bg-white dark:bg-gray-800 text-cyan-600 dark:text-cyan-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold text-lg border-2 border-cyan-600 dark:border-cyan-500">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Section -->
<section id="fitur" class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Fitur Unggulan
            </h2>
            <p class="text-xl text-gray-800 font-medium">
                Rencanakan perjalanan budaya Anda dengan mudah
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- AI Trip Assistant -->
            <div class="bg-white border-2 border-cyan-200 rounded-2xl p-10 shadow-xl hover:shadow-2xl transition transform hover:-translate-y-2 hover:border-cyan-400">
                <div class="text-6xl mb-6">🤖</div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    AI Trip Assistant
                </h3>
                <p class="text-lg text-gray-800 mb-6 leading-relaxed">
                    Asisten perjalanan AI yang membantu Anda merencanakan itinerary ideal berdasarkan preferensi dan budget.
                </p>
                <ul class="text-base text-gray-700 space-y-3">
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Rekomendasi destinasi personal</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Itinerary otomatis 1-5 hari</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Estimasi biaya & waktu</span>
                    </li>
                </ul>
            </div>

            <!-- Database Budaya -->
            <div class="bg-white border-2 border-blue-200 rounded-2xl p-10 shadow-xl hover:shadow-2xl transition transform hover:-translate-y-2 hover:border-blue-400">
                <div class="text-6xl mb-6">🏛️</div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    Database Budaya Indonesia
                </h3>
                <p class="text-lg text-gray-800 mb-6 leading-relaxed">
                    Koleksi lengkap {{ $totalDestinations }}+ destinasi budaya Indonesia dengan informasi detail dan terkini.
                </p>
                <ul class="text-base text-gray-700 space-y-3">
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>{{ $totalCategories }} kategori budaya</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Harga tiket & jam operasional</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Lokasi & peta interaktif</span>
                    </li>
                </ul>
            </div>

            <!-- Peta Interaktif -->
            <div class="bg-white border-2 border-green-200 rounded-2xl p-10 shadow-xl hover:shadow-2xl transition transform hover:-translate-y-2 hover:border-green-400">
                <div class="text-6xl mb-6">🗺️</div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    Peta Interaktif
                </h3>
                <p class="text-lg text-gray-800 mb-6 leading-relaxed">
                    Visualisasi peta dengan tracking real-time dan navigasi ke destinasi budaya favorit Anda.
                </p>
                <ul class="text-base text-gray-700 space-y-3">
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Integrasi OpenStreetMap</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Tracking lokasi real-time</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-600 font-bold">✓</span>
                        <span>Estimasi jarak & waktu tempuh</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Galeri Destinasi Populer -->
<section class="py-16 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Destinasi Budaya Populer
            </h2>
            <p class="text-xl text-gray-800 font-medium">
                Jelajahi destinasi wisata budaya terbaik di Indonesia
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredDestinations as $destination)
                <a href="{{ route('destinations.show', $destination->slug) }}" class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                    <div class="aspect-[4/3] bg-gradient-to-br from-cyan-100 to-blue-100 relative overflow-hidden">
                        @if($destination->images && count($destination->images) > 0)
                            <img
                                src="{{ asset('storage/' . $destination->images[0]) }}"
                                alt="{{ $destination->name }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl">
                                🏛️
                            </div>
                        @endif
                        <div class="absolute top-4 right-4 flex items-center gap-1 bg-white/90 backdrop-blur-sm px-3 py-2 rounded-lg shadow-lg">
                            <svg class="w-5 h-5 text-cyan-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold text-lg text-gray-900">{{ $destination->review_count > 0 ? $destination->average_rating : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="mb-3">
                            <h3 class="text-2xl font-bold text-gray-900 group-hover:text-cyan-600 transition leading-tight">
                                {{ $destination->name }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-2 text-base text-gray-800 mb-4">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">{{ $destination->city }}, {{ $destination->province->name }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-sm font-semibold px-4 py-2 bg-cyan-100 text-cyan-900 rounded-lg">
                                {{ $destination->category->name }}
                            </span>
                            <span class="font-bold text-xl text-cyan-600">
                                {{ $destination->formatted_price }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('destinations.index') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold shadow-lg">
                Lihat Semua Destinasi
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 md:py-24 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">
            Siap Menjelajahi Budaya Indonesia?
        </h2>
        <p class="text-xl mb-8 opacity-90">
            Temukan destinasi wisata budaya impian Anda dan rencanakan perjalanan yang tak terlupakan
        </p>
        <a href="{{ route('destinations.index') }}" class="inline-block px-8 py-4 bg-white text-cyan-600 rounded-lg hover:bg-gray-100 transition font-semibold text-lg shadow-lg">
            Mulai Petualangan Anda
        </a>
    </div>
</section>
@endsection
