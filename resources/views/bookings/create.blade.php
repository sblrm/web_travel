@extends('layouts.main')

@php
    $title = 'Booking Tiket - ' . $destination->name;
@endphp

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('destinations.show', $destination->slug) }}" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Destinasi
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                Booking Tiket
            </h1>
            <p class="text-gray-600 mt-2">
                Lengkapi data booking untuk {{ $destination->name }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 md:p-8">
                    <form action="{{ route('bookings.store', $destination->slug) }}" method="POST">
                        @csrf
                        
                        <!-- Visit Date -->
                        <div class="mb-6">
                            <label for="visit_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Kunjungan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="visit_date"
                                name="visit_date"
                                value="{{ old('visit_date') }}"
                                min="{{ date('Y-m-d') }}"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('visit_date') border-red-500 @enderror"
                            >
                            @error('visit_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jumlah Pengunjung <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                max="50"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('quantity') border-red-500 @enderror"
                            >
                            @error('quantity')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Maksimal 50 pengunjung per booking</p>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                @foreach($paymentMethods as $method)
                                    <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-cyan-500 transition @error('payment_method_id') border-red-500 @enderror">
                                        <input
                                            type="radio"
                                            name="payment_method_id"
                                            value="{{ $method->id }}"
                                            {{ old('payment_method_id') == $method->id ? 'checked' : '' }}
                                            required
                                            class="mt-1 text-cyan-600 focus:ring-cyan-500"
                                        >
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">{{ $method->name }}</div>
                                            @if($method->account_number)
                                                <div class="text-sm text-gray-600">
                                                    {{ $method->account_number }} - {{ $method->account_name }}
                                                </div>
                                            @endif
                                            @if($method->instructions)
                                                <div class="text-xs text-gray-500 mt-1 whitespace-pre-line">{{ $method->instructions }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-8">

                        <!-- Visitor Information -->
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pengunjung</h3>

                        <!-- Visitor Name -->
                        <div class="mb-6">
                            <label for="visitor_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="visitor_name"
                                name="visitor_name"
                                value="{{ old('visitor_name', auth()->user()->name) }}"
                                required
                                maxlength="255"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('visitor_name') border-red-500 @enderror"
                            >
                            @error('visitor_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visitor Email -->
                        <div class="mb-6">
                            <label for="visitor_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="visitor_email"
                                name="visitor_email"
                                value="{{ old('visitor_email', auth()->user()->email) }}"
                                required
                                maxlength="255"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('visitor_email') border-red-500 @enderror"
                            >
                            @error('visitor_email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visitor Phone -->
                        <div class="mb-6">
                            <label for="visitor_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="tel"
                                id="visitor_phone"
                                name="visitor_phone"
                                value="{{ old('visitor_phone') }}"
                                required
                                maxlength="20"
                                placeholder="08123456789"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('visitor_phone') border-red-500 @enderror"
                            >
                            @error('visitor_phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                maxlength="500"
                                placeholder="Permintaan khusus atau informasi tambahan..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('notes') border-red-500 @enderror"
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('destinations.show', $destination->slug) }}" class="text-gray-600 hover:text-gray-900">
                                Batalkan
                            </a>
                            <button
                                type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                Buat Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 sticky top-20">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Booking</h3>
                    
                    <!-- Destination Info -->
                    <div class="mb-4 pb-4 border-b">
                        @if($destination->images && count($destination->images) > 0)
                            <img
                                src="{{ asset('storage/' . $destination->images[0]) }}"
                                alt="{{ $destination->name }}"
                                class="w-full h-32 object-cover rounded-lg mb-3"
                            >
                        @endif
                        <h4 class="font-semibold text-gray-900">{{ $destination->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $destination->city }}, {{ $destination->province->name }}</p>
                    </div>

                    <!-- Price Calculation -->
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga per tiket</span>
                            <span class="font-semibold" id="unit-price">{{ $destination->formatted_price }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah pengunjung</span>
                            <span class="font-semibold" id="quantity-display">1 orang</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-cyan-600" id="total-price">{{ $destination->formatted_price }}</span>
                        </div>
                    </div>

                    <!-- Important Info -->
                    <div class="mt-6 p-4 bg-amber-50 rounded-lg border-2 border-amber-200">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-xs text-amber-800">
                                <p class="font-semibold mb-1">Perhatian:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Booking berlaku 24 jam</li>
                                    <li>Upload bukti pembayaran setelah transfer</li>
                                    <li>Tiket berlaku setelah verifikasi admin</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Real-time price calculation
    const quantityInput = document.getElementById('quantity');
    const quantityDisplay = document.getElementById('quantity-display');
    const totalPriceDisplay = document.getElementById('total-price');
    const unitPrice = {{ $destination->ticket_price }};
    
    quantityInput.addEventListener('input', function() {
        const quantity = parseInt(this.value) || 1;
        const total = quantity * unitPrice;
        
        quantityDisplay.textContent = quantity + ' orang';
        totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
    });
</script>
@endpush
@endsection
