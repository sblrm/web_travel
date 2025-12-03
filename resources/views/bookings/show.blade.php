@extends('layouts.main')

@php
    $title = 'Detail Booking - ' . $booking->booking_code;
@endphp

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Booking
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                        {{ $booking->booking_code }}
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Detail booking dan status pembayaran
                    </p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $booking->status_color }}">
                    {{ $booking->status_label }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg text-green-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg text-red-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Destination Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Destinasi</h2>
                    @if($booking->destination->images && count($booking->destination->images) > 0)
                        <img
                            src="{{ asset('storage/' . $booking->destination->images[0]) }}"
                            alt="{{ $booking->destination->name }}"
                            class="w-full h-48 object-cover rounded-lg mb-4"
                        >
                    @endif
                    <div>
                        <h3 class="font-bold text-lg text-gray-900">{{ $booking->destination->name }}</h3>
                        <p class="text-gray-600 text-sm mb-3">{{ $booking->destination->city }}, {{ $booking->destination->province->name }}</p>
                        <a href="{{ route('destinations.show', $booking->destination->slug) }}" class="text-cyan-600 hover:text-cyan-800 text-sm font-semibold">
                            Lihat Detail Destinasi →
                        </a>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Detail Booking</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Kode Booking</span>
                            <span class="font-semibold text-gray-900">{{ $booking->booking_code }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Tanggal Kunjungan</span>
                            <span class="font-semibold text-gray-900">{{ $booking->formatted_visit_date }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Jumlah Pengunjung</span>
                            <span class="font-semibold text-gray-900">{{ $booking->quantity }} orang</span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Harga per Tiket</span>
                            <span class="font-semibold text-gray-900">{{ $booking->formatted_unit_price }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Total Pembayaran</span>
                            <span class="font-bold text-xl text-cyan-600">{{ $booking->formatted_total_amount }}</span>
                        </div>
                        @if($booking->notes)
                            <div class="py-3">
                                <span class="text-gray-600 block mb-1">Catatan</span>
                                <p class="text-gray-900">{{ $booking->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Visitor Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pengunjung</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-600">Nama Lengkap</span>
                            <p class="font-semibold text-gray-900">{{ $booking->visitor_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Email</span>
                            <p class="font-semibold text-gray-900">{{ $booking->visitor_email }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Nomor Telepon</span>
                            <p class="font-semibold text-gray-900">{{ $booking->visitor_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($booking->payment)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pembayaran</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between py-3 border-b">
                                <span class="text-gray-600">Metode Pembayaran</span>
                                <span class="font-semibold text-gray-900">{{ $booking->payment->paymentMethod->name }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b">
                                <span class="text-gray-600">Status Pembayaran</span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $booking->payment->status_color }}">
                                    {{ $booking->payment->status_label }}
                                </span>
                            </div>
                            
                            @if($booking->payment->isPending() || $booking->payment->isRejected())
                                <!-- Payment Instructions -->
                                <div class="p-4 bg-cyan-50 rounded-lg border-2 border-cyan-200">
                                    <h3 class="font-semibold text-gray-900 mb-2">Instruksi Pembayaran</h3>
                                    @if($booking->payment->paymentMethod->account_number)
                                        <div class="text-sm space-y-1 mb-3">
                                            <p><span class="font-semibold">Nomor Rekening:</span> {{ $booking->payment->paymentMethod->account_number }}</p>
                                            <p><span class="font-semibold">Atas Nama:</span> {{ $booking->payment->paymentMethod->account_name }}</p>
                                            <p><span class="font-semibold">Jumlah:</span> <span class="text-cyan-600 font-bold">{{ $booking->formatted_total_amount }}</span></p>
                                        </div>
                                    @endif
                                    @if($booking->payment->paymentMethod->instructions)
                                        <div class="text-xs text-gray-700 whitespace-pre-line">{{ $booking->payment->paymentMethod->instructions }}</div>
                                    @endif>
                                </div>
                            @endif

                            @if($booking->payment->isUploaded())
                                <div class="p-4 bg-amber-50 rounded-lg border-2 border-amber-200">
                                    <div class="flex items-center gap-2 text-amber-800">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">Menunggu Verifikasi Admin</span>
                                    </div>
                                    <p class="text-sm text-amber-700 mt-2">Bukti pembayaran Anda sedang diverifikasi. Kami akan mengirim notifikasi setelah pembayaran diverifikasi.</p>
                                </div>
                            @endif

                            @if($booking->payment->isVerified())
                                <div class="p-4 bg-green-50 rounded-lg border-2 border-green-200">
                                    <div class="flex items-center gap-2 text-green-800">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">Pembayaran Terverifikasi</span>
                                    </div>
                                    <p class="text-sm text-green-700 mt-2">Pembayaran Anda telah diverifikasi pada {{ $booking->payment->verified_at?->format('d M Y, H:i') }}</p>
                                </div>
                            @endif

                            @if($booking->payment->isRejected() && $booking->payment->rejection_reason)
                                <div class="p-4 bg-red-50 rounded-lg border-2 border-red-200">
                                    <div class="flex items-center gap-2 text-red-800 mb-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">Pembayaran Ditolak</span>
                                    </div>
                                    <p class="text-sm text-red-700"><span class="font-semibold">Alasan:</span> {{ $booking->payment->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20 space-y-4">
                    <!-- Expiry Warning -->
                    @if($booking->isAwaitingPayment() && !$booking->isExpired())
                        <div class="p-4 bg-amber-50 rounded-lg border-2 border-amber-200">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-amber-600 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                <p class="font-semibold text-sm text-amber-900 mb-1">Booking akan kadaluarsa</p>
                                <p class="text-xs text-amber-800">{{ $booking->formatted_expires_at }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Payment Proof Button -->
                    @if($booking->canUploadPayment())
                        <a
                            href="{{ route('payments.show', $booking) }}"
                            class="block w-full px-6 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold text-center shadow-lg"
                        >
                            📤 Upload Bukti Pembayaran
                        </a>
                    @endif

                    <!-- View Invoice Button -->
                    @if($booking->isPaid() || $booking->isVerified() || $booking->isCompleted())
                        <a
                            href="{{ route('payments.invoice', $booking) }}"
                            target="_blank"
                            class="block w-full px-6 py-3 bg-gradient-to-r from-green-600 to-green-500 text-black rounded-lg hover:from-green-700 hover:to-green-600 transition font-semibold text-center shadow-lg"
                        >
                            📄 Lihat Invoice
                        </a>
                    @endif

                    <!-- Cancel Booking Button -->
                    @if($booking->canCancel())
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-center"
                            >
                                ❌ Batalkan Booking
                            </button>
                        </form>
                    @endif

                    <!-- Contact Support -->
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-700 font-semibold mb-2">Butuh bantuan?</p>
                        <a href="mailto:support@culturaltrip.com" class="text-sm text-cyan-600 hover:text-cyan-800 font-semibold">
                            📧 support@culturaltrip.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
