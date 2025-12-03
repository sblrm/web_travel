<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            // Bank Transfer Methods
            [
                'name' => 'Transfer BCA',
                'type' => 'bank_transfer',
                'code' => 'bca',
                'account_number' => '1234567890',
                'account_name' => 'PT Cultural Trip Indonesia',
                'instructions' => "1. Transfer ke rekening BCA 1234567890 a.n. PT Cultural Trip Indonesia\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer Mandiri',
                'type' => 'bank_transfer',
                'code' => 'mandiri',
                'account_number' => '9876543210',
                'account_name' => 'PT Cultural Trip Indonesia',
                'instructions' => "1. Transfer ke rekening Mandiri 9876543210 a.n. PT Cultural Trip Indonesia\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer BNI',
                'type' => 'bank_transfer',
                'code' => 'bni',
                'account_number' => '0123456789',
                'account_name' => 'PT Cultural Trip Indonesia',
                'instructions' => "1. Transfer ke rekening BNI 0123456789 a.n. PT Cultural Trip Indonesia\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 3,
                'is_active' => true,
            ],
            // E-Wallet Methods
            [
                'name' => 'GoPay',
                'type' => 'ewallet',
                'code' => 'gopay',
                'account_number' => '081234567890',
                'account_name' => 'Cultural Trip',
                'instructions' => "1. Transfer ke nomor GoPay 081234567890 a.n. Cultural Trip\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'OVO',
                'type' => 'ewallet',
                'code' => 'ovo',
                'account_number' => '081234567891',
                'account_name' => 'Cultural Trip',
                'instructions' => "1. Transfer ke nomor OVO 081234567891 a.n. Cultural Trip\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'DANA',
                'type' => 'ewallet',
                'code' => 'dana',
                'account_number' => '081234567892',
                'account_name' => 'Cultural Trip',
                'instructions' => "1. Transfer ke nomor DANA 081234567892 a.n. Cultural Trip\n2. Upload bukti transfer\n3. Tunggu verifikasi admin (maks. 2x24 jam)",
                'sort_order' => 6,
                'is_active' => true,
            ],
            // Cash Payment
            [
                'name' => 'Bayar di Lokasi (Cash)',
                'type' => 'cash',
                'code' => 'cash',
                'account_number' => null,
                'account_name' => null,
                'instructions' => "1. Tunjukkan kode booking saat tiba di lokasi\n2. Bayar langsung ke loket\n3. Booking akan otomatis diverifikasi",
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
