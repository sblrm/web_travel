@extends('layouts.main')

@php
    $title = 'Jelajahi Destinasi';
@endphp

@section('content')
<!-- Header Section -->
<section class="bg-gradient-to-r from-cyan-600 to-cyan-500 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-5xl font-bold mb-8">Jelajahi Destinasi Budaya</h1>
        
        <!-- Search Bar -->
        <form method="GET" class="max-w-3xl">
            <div class="flex gap-3">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari destinasi budaya..." 
                    class="flex-1 px-6 py-4 text-lg rounded-lg text-gray-900 font-medium focus:ring-4 focus:ring-amber-300 outline-none shadow-lg"
                >
                <button type="submit" class="px-8 py-4 bg-white text-cyan-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg shadow-lg">
                    Cari
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Filter & Grid Section -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <form method="GET" class="mb-12">
            <div class="bg-white rounded-xl p-8 shadow-xl border-2 border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Province Filter -->
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-3">
                            Provinsi
                        </label>
                        <select name="province" class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 text-gray-900 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ request('province') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-3">
                            Jenis Budaya
                        </label>
                        <select name="category" class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 text-gray-900 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none">
                            <option value="">Semua Jenis</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Min Price Filter -->
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-3">
                            Harga Minimum
                        </label>
                        <input 
                            type="number" 
                            name="min_price" 
                            value="{{ request('min_price') }}"
                            placeholder="Rp 0"
                            class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 text-gray-900 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none"
                        >
                    </div>

                    <!-- Max Price Filter -->
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-3">
                            Harga Maksimum
                        </label>
                        <input 
                            type="number" 
                            name="max_price" 
                            value="{{ request('max_price') }}"
                            placeholder="Rp 100.000"
                            class="w-full px-4 py-3 text-base font-medium rounded-lg border-2 border-gray-300 text-gray-900 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none"
                        >
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-bold text-lg shadow-lg">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('destinations.index') }}" class="px-8 py-3 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-bold text-lg shadow-lg">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Results Count -->
        <div class="mb-8">
            <p class="text-lg text-gray-900 font-semibold">
                Menampilkan <span class="text-cyan-600">{{ $destinations->count() }}</span> dari <span class="text-cyan-600">{{ $destinations->total() }}</span> destinasi
            </p>
        </div>

        <!-- Destination Grid -->
        @if($destinations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($destinations as $destination)
                    <a href="{{ route('destinations.show', $destination->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition transform hover:-translate-y-2 border-2 border-gray-200 hover:border-cyan-400">
                        <div class="aspect-[4/3] bg-gradient-to-br from-cyan-100 to-blue-100 relative overflow-hidden">
                            @if($destination->images && count($destination->images) > 0)
                                <img 
                                    src="{{ asset('storage/' . $destination->images[0]) }}" 
                                    alt="{{ $destination->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-7xl">
                                    🏛️
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 flex items-center gap-1 bg-white/90 backdrop-blur-sm px-3 py-2 rounded-lg shadow-lg">
                                <svg class="w-5 h-5 text-cyan-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="font-bold text-gray-900">{{ $destination->review_count > 0 ? $destination->average_rating : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="mb-3">
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-cyan-600 transition line-clamp-2 leading-tight">
                                    {{ $destination->name }}
                                </h3>
                            </div>
                            <div class="flex items-center gap-2 text-base text-gray-800 mb-4">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate font-medium">{{ $destination->city }}</span>
                            </div>
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-sm font-bold px-3 py-2 bg-cyan-100 text-cyan-900 rounded-lg truncate max-w-[60%]">
                                    {{ $destination->category->name }}
                                </span>
                                <span class="font-bold text-cyan-600 text-lg">
                                    {{ $destination->formatted_price }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $destinations->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl shadow-xl">
                <div class="text-8xl mb-6">🔍</div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Tidak ada destinasi ditemukan
                </h3>
                <p class="text-xl text-gray-800 mb-8">
                    Coba ubah kriteria pencarian Anda
                </p>
                <a href="{{ route('destinations.index') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-bold text-lg shadow-lg">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
