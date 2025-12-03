<div class="space-y-4">
    @if($payment->proof_image)
        <div class="text-center">
            <img
                src="{{ Storage::disk('public')->url($payment->proof_image) }}"
                alt="Bukti Pembayaran {{ $payment->payment_code }}"
                class="mx-auto rounded-lg shadow-lg max-w-full h-auto"
                style="max-height: 70vh; max-width: 1200px;"
            >
        </div>        <div class="grid grid-cols-2 gap-4 mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Payment</p>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $payment->payment_code }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah</p>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $payment->paymentMethod->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pengirim</p>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $payment->account_holder_name ?? '-' }}</p>
            </div>
            @if($payment->transfer_from)
            <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transfer Dari</p>
                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $payment->transfer_from }}</p>
            </div>
            @endif
            @if($payment->notes)
            <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</p>
                <p class="text-base text-gray-900 dark:text-gray-100">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
        
        <div class="flex justify-center gap-2 mt-4">
            <a
                href="{{ Storage::disk('public')->url($payment->proof_image) }}"
                target="_blank"
                class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                Buka di Tab Baru
            </a>
            <a
                href="{{ Storage::disk('public')->url($payment->proof_image) }}"
                download="bukti-pembayaran-{{ $payment->payment_code }}.jpg"
                class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download
            </a>
        </div>
    @else
        <div class="text-center py-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-400 mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Belum ada bukti pembayaran yang diupload</p>
        </div>
    @endif
</div>
