@extends('layouts.main')

@php
    $title = 'Upload Bukti Pembayaran - ' . $booking->booking_code;
@endphp

@section('content')
<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Booking
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                Upload Bukti Pembayaran
            </h1>
            <p class="text-gray-600 mt-2">
                Booking: {{ $booking->booking_code }}
            </p>
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
            <!-- Upload Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 md:p-8">
                    <!-- Payment Instructions -->
                    <div class="mb-6 p-4 bg-cyan-50 rounded-lg border-2 border-cyan-200">
                        <h3 class="font-bold text-gray-900 mb-3">Informasi Pembayaran</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Metode:</span>
                                <span class="font-semibold text-gray-900">{{ $booking->payment->paymentMethod->name }}</span>
                            </div>
                            @if($booking->payment->paymentMethod->account_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nomor Rekening:</span>
                                    <span class="font-semibold text-gray-900">{{ $booking->payment->paymentMethod->account_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Atas Nama:</span>
                                    <span class="font-semibold text-gray-900">{{ $booking->payment->paymentMethod->account_name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-600">Total Pembayaran:</span>
                                <span class="font-bold text-lg text-cyan-600">{{ $booking->formatted_total_amount }}</span>
                            </div>
                        </div>
                        
                        @if($booking->payment->paymentMethod->instructions)
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-xs text-gray-700 whitespace-pre-line">{{ $booking->payment->paymentMethod->instructions }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('payments.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Form Upload Bukti</h3>

                        <!-- Proof Image -->
                        <div class="mb-6">
                            <label for="proof_image" class="block text-sm font-semibold text-gray-700 mb-2">
                                Bukti Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="file"
                                id="proof_image"
                                name="proof_image"
                                accept="image/jpeg,image/jpg,image/png"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 @error('proof_image') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB</p>
                            @error('proof_image')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="mt-4 hidden">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Preview:</p>
                                <img id="preview-img" src="" alt="Preview" class="max-w-full h-auto rounded-lg border-2 border-gray-300">
                            </div>
                        </div>

                        <!-- Account Holder Name -->
                        <div class="mb-6">
                            <label for="account_holder_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Pemilik Rekening <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="account_holder_name"
                                name="account_holder_name"
                                value="{{ old('account_holder_name') }}"
                                required
                                maxlength="255"
                                placeholder="Nama sesuai rekening pengirim"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('account_holder_name') border-red-500 @enderror"
                            >
                            @error('account_holder_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transfer From -->
                        <div class="mb-6">
                            <label for="transfer_from" class="block text-sm font-semibold text-gray-700 mb-2">
                                Transfer Dari <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="transfer_from"
                                name="transfer_from"
                                value="{{ old('transfer_from') }}"
                                required
                                maxlength="255"
                                placeholder="Bank/E-wallet dan nomor rekening"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('transfer_from') border-red-500 @enderror"
                            >
                            <p class="text-xs text-gray-500 mt-1">Contoh: BCA 1234567890 atau GoPay 08123456789</p>
                            @error('transfer_from')
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
                                placeholder="Informasi tambahan..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none @error('notes') border-red-500 @enderror"
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('bookings.show', $booking) }}" class="text-gray-600 hover:text-gray-900">
                                Batalkan
                            </a>
                            <button
                                type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                Upload Bukti
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 sticky top-20 space-y-6">
                    <!-- Booking Summary -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-3">Ringkasan Booking</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kode:</span>
                                <span class="font-semibold">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Destinasi:</span>
                                <span class="font-semibold">{{ Str::limit($booking->destination->name, 20) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-semibold">{{ $booking->visit_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pengunjung:</span>
                                <span class="font-semibold">{{ $booking->quantity }} orang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Tips Upload Bukti
                        </h4>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>✓ Pastikan bukti transfer terlihat jelas</li>
                            <li>✓ Nominal harus sesuai dengan total booking</li>
                            <li>✓ Screenshot harus menampilkan tanggal & waktu</li>
                            <li>✓ Nama pengirim harus sesuai</li>
                        </ul>
                    </div>

                    <!-- Previous Upload -->
                    @if($booking->payment->proof_image)
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Bukti Sebelumnya:</h4>
                            <img src="{{ $booking->payment->proof_image_url }}" alt="Bukti sebelumnya" class="w-full rounded-lg border-2 border-gray-300">
                            <p class="text-xs text-gray-600 mt-2">Upload baru akan menggantikan bukti ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Image Preview
    const fileInput = document.getElementById('proof_image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                fileInput.value = '';
                preview.classList.add('hidden');
                return;
            }
            
            // Validate file type
            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                alert('Format file harus JPG, JPEG, atau PNG');
                fileInput.value = '';
                preview.classList.add('hidden');
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
