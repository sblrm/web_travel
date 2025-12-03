@extends('layouts.main')

@php
    $title = 'Daftar Booking Saya';
@endphp

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Daftar Booking Saya</h2>
                    <a href="{{ route('destinations.index') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                        🎫 Booking Baru
                    </a>
                </div>

                @if($bookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($bookings as $booking)
                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-cyan-300 transition">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $booking->destination->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $booking->destination->city }}, {{ $booking->destination->province->name }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $booking->status_color }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <span class="text-xs text-gray-600">Kode Booking</span>
                                        <p class="font-semibold text-gray-900">{{ $booking->booking_code }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600">Tanggal Kunjungan</span>
                                        <p class="font-semibold text-gray-900">{{ $booking->formatted_visit_date }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600">Pengunjung</span>
                                        <p class="font-semibold text-gray-900">{{ $booking->quantity }} orang</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600">Total</span>
                                        <p class="font-semibold text-cyan-600">{{ $booking->formatted_total_amount }}</p>
                                    </div>
                                </div>

                                @if($booking->payment)
                                    <div class="flex items-center gap-2 mb-4 text-sm">
                                        <span class="text-gray-600">Pembayaran:</span>
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $booking->payment->status_color }}">
                                            {{ $booking->payment->status_label }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex gap-2">
                                    <a href="{{ route('bookings.show', $booking) }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm font-semibold">
                                        Lihat Detail
                                    </a>
                                    @if($booking->canUploadPayment())
                                        <a href="{{ route('payments.show', $booking) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                                            Upload Bukti Bayar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🎫</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada booking</h3>
                        <p class="text-gray-600 mb-6">Mulai booking destinasi wisata budaya favoritmu!</p>
                        <a href="{{ route('destinations.index') }}" class="inline-block px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition font-semibold">
                            Jelajahi Destinasi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
