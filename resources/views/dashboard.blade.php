@extends('layouts.main')

@php
    $title = 'Dashboard';
@endphp

@section('content')
<section class="py-12 bg-gradient-to-br from-cyan-50 via-blue-50 to-purple-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-xl text-gray-600">
                Kelola semua booking dan aktivitas wisata budaya Anda di sini
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Bookings -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-cyan-500 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center text-white text-2xl">
                        🎫
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ auth()->user()->bookings()->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-700 text-lg">Total Booking</h3>
                <p class="text-sm text-gray-500 mt-1">Semua booking Anda</p>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-green-500 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-white text-2xl">
                        ✅
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ auth()->user()->bookings()->where('status', 'completed')->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-700 text-lg">Selesai</h3>
                <p class="text-sm text-gray-500 mt-1">Kunjungan selesai</p>
            </div>

            <!-- Active/Pending -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-amber-500 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center text-white text-2xl">
                        ⏳
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ auth()->user()->bookings()->whereIn('status', ['pending', 'awaiting_payment', 'paid', 'verified'])->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-700 text-lg">Aktif</h3>
                <p class="text-sm text-gray-500 mt-1">Sedang berjalan</p>
            </div>

            <!-- Cancelled -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-red-500 transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center text-white text-2xl">
                        ❌
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ auth()->user()->bookings()->whereIn('status', ['cancelled', 'expired'])->count() }}</span>
                </div>
                <h3 class="font-semibold text-gray-700 text-lg">Dibatalkan</h3>
                <p class="text-sm text-gray-500 mt-1">Tidak jadi/expired</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <span class="text-3xl">⚡</span>
                Aksi Cepat
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('destinations.index') }}" class="group p-6 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl text-black hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl mb-3">🗺️</div>
                    <h3 class="font-bold text-xl mb-2">Jelajahi Destinasi</h3>
                    <p class="text-cyan-100">Temukan wisata budaya baru</p>
                </a>

                <a href="{{ route('bookings.index') }}" class="group p-6 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl text-black hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl mb-3">📋</div>
                    <h3 class="font-bold text-xl mb-2">Lihat Semua Booking</h3>
                    <p class="text-purple-100">Kelola semua booking Anda</p>
                </a>

                <a href="{{ route('analytics.index') }}" class="group p-6 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl text-black hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-4xl mb-3">📊</div>
                    <h3 class="font-bold text-xl mb-2">Analytics</h3>
                    <p class="text-green-100">Statistik wisata Anda</p>
                </a>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="text-3xl">📅</span>
                    Booking Terbaru
                </h2>
                <a href="{{ route('bookings.index') }}" class="text-cyan-600 hover:text-cyan-700 font-semibold flex items-center gap-2 group">
                    Lihat Semua
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @php
                $recentBookings = auth()->user()->bookings()
                    ->with(['destination', 'payment'])
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if($recentBookings->count() > 0)
                <div class="space-y-4">
                    @foreach($recentBookings as $booking)
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow duration-300 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Destination Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-4">
                                        @if($booking->destination->images && count($booking->destination->images) > 0)
                                            <img src="{{ Storage::disk('public')->url($booking->destination->images[0]) }}" 
                                                 alt="{{ $booking->destination->name }}"
                                                 class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                        @else
                                            <div class="w-20 h-20 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center text-white text-2xl flex-shrink-0">
                                                🏛️
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-lg text-gray-900 mb-1 truncate">
                                                {{ $booking->destination->name }}
                                            </h3>
                                            <p class="text-sm text-gray-600 mb-2">
                                                {{ $booking->booking_code }}
                                            </p>
                                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                                <span class="flex items-center gap-1">
                                                    📅 {{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    👥 {{ $booking->quantity }} orang
                                                </span>
                                                <span class="flex items-center gap-1 font-semibold text-cyan-600">
                                                    💰 Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Actions -->
                                <div class="flex flex-col items-end gap-3">
                                    <span class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap {{ $booking->status_color }}">
                                        {{ $booking->status_label }}
                                    </span>
                                    <a href="{{ route('bookings.show', $booking->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition font-semibold text-sm">
                                        Detail
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="text-8xl mb-6">🎫</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Booking</h3>
                    <p class="text-gray-600 mb-8 text-lg">Mulai jelajahi dan booking destinasi wisata budaya favorit Anda!</p>
                    <a href="{{ route('destinations.index') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl hover:from-cyan-700 hover:to-blue-700 transition font-bold text-lg shadow-xl">
                        <span class="text-2xl">🗺️</span>
                        Jelajahi Destinasi
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
