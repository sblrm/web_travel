@extends('layouts.main')

@php
    $title = 'Invoice - ' . $booking->booking_code;
@endphp

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- PDF Instructions -->
            <div class="pdf-instructions no-print">
                <p class="font-bold text-lg mb-2">💡 Cara Download Invoice sebagai PDF:</p>
                <p>1. Klik tombol "Print / Save as PDF" di bawah</p>
                <p>2. Pilih "Save as PDF" atau "Microsoft Print to PDF" sebagai printer</p>
                <p>3. Klik "Save" dan pilih lokasi penyimpanan</p>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">INVOICE</h1>
                            <p class="text-gray-600">{{ $booking->booking_code }}</p>
                        </div>
                        <div class="text-right">
                            <h2 class="text-xl font-bold text-primary-600 mb-1">CulturalTrip</h2>
                            <p class="text-sm text-gray-600">Platform Wisata Budaya Indonesia</p>
                        </div>
                    </div>

                                <!-- Invoice Info -->
                    <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-200">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Booking</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Tanggal Booking:</dt>
                        <dd class="font-medium text-gray-900">{{ $booking->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Tanggal Kunjungan:</dt>
                        <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Status:</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terverifikasi
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Pengunjung</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Nama:</dt>
                        <dd class="font-medium text-gray-900">{{ $booking->visitor_name }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Email:</dt>
                        <dd class="font-medium text-gray-900">{{ $booking->visitor_email }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">Telepon:</dt>
                        <dd class="font-medium text-gray-900">{{ $booking->visitor_phone }}</dd>
                    </div>
                </dl>
                    </div>
                    </div>

                    <!-- Destination Details -->
                    <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Destinasi</h3>
            <div class="bg-gray-50 rounded-lg p-6">
                    @if($booking->destination->images && count($booking->destination->images) > 0)
                    <img src="{{ Storage::disk('public')->url($booking->destination->images[0]) }}"
                         alt="{{ $booking->destination->name }}"
                         class="w-full h-48 object-cover rounded-lg mb-4">
                    @endif
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $booking->destination->name }}</h4>
                        <p class="text-gray-600 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $booking->destination->city }}, {{ $booking->destination->province->name }}
                        </p>
                        <p class="text-sm text-gray-500">Kategori: {{ $booking->destination->category->name }}</p>
                    </div>
                    </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Pembayaran</h3>
            <div class="bg-gray-50 rounded-lg p-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-sm font-semibold text-gray-700">Item</th>
                            <th class="text-center py-2 text-sm font-semibold text-gray-700">Jumlah</th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-700">Harga Satuan</th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3 text-sm text-gray-900">Tiket Masuk</td>
                            <td class="py-3 text-sm text-gray-900 text-center">{{ $booking->quantity }} orang</td>
                            <td class="py-3 text-sm text-gray-900 text-right">Rp {{ number_format($booking->unit_price, 0, ',', '.') }}</td>
                            <td class="py-3 text-sm text-gray-900 text-right font-medium">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t-2 border-gray-300">
                        <tr>
                            <td colspan="3" class="py-3 text-right font-bold text-gray-900">Total Pembayaran:</td>
                            <td class="py-3 text-right font-bold text-xl text-primary-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                        </table>
                    </div>
                    </div>

                    <!-- Payment Method Info -->
                    @if($booking->payment)
        <div class="mb-8 pb-8 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Metode Pembayaran:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ $booking->payment->paymentMethod->name }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Kode Payment:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ $booking->payment->payment_code }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Tanggal Pembayaran:</span>
                    <span class="font-medium text-gray-900 ml-2">{{ $booking->payment->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Status Pembayaran:</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                        Terverifikasi
                    </span>
                </div>
                    </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($booking->notes)
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Catatan</h3>
                        <p class="text-sm text-gray-600">{{ $booking->notes }}</p>
                    </div>
                    @endif

                    <!-- Footer -->
                                <div class="text-center text-sm text-gray-500 pt-8 border-t border-gray-200">
                        <p>Invoice ini digenerate secara otomatis oleh sistem CulturalTrip</p>
                        <p class="mt-1">Simpan invoice ini sebagai bukti pembayaran yang sah</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center gap-4 mt-8 no-print">
            <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-lg transition-colors shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print / Save as PDF
            </button>
            <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-lg transition-colors shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                        Kembali ke Booking
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media print {
    /* Hide everything first */
    body * {
        visibility: hidden;
    }
    
    /* Show only invoice content */
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }
    
    /* Position invoice at top */
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    /* Hide buttons and navigation */
    .no-print {
        display: none !important;
    }
    
    /* Remove background colors for printing */
    section {
        background: white !important;
        padding: 0 !important;
    }
    
    /* Better page breaks */
    .bg-white {
        page-break-inside: avoid;
    }
    
    /* Hide footer and navbar */
    nav, footer, header {
        display: none !important;
    }
}

/* Instructions for saving as PDF */
.pdf-instructions {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    text-align: center;
}

.pdf-instructions p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
}

@media print {
    .pdf-instructions {
        display: none !important;
    }
}
</style>
@endsection

