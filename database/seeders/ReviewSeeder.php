<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = Destination::all();
        $users = User::all();

        if ($destinations->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Tidak ada destinasi atau user untuk membuat review.');

            return;
        }

        $comments = [
            'Tempat yang sangat menarik dan penuh sejarah!',
            'Pengalaman budaya yang luar biasa.',
            'Sangat direkomendasikan untuk wisata keluarga.',
            'Arsitekturnya menakjubkan dan terawat dengan baik.',
            'Pemandu wisatanya sangat informatif.',
            'Tempatnya bersih dan nyaman dikunjungi.',
            'Harga tiket masuknya sangat terjangkau.',
            'Cocok untuk belajar sejarah Indonesia.',
            'View-nya bagus, instagramable!',
            'Fasilitasnya lengkap dan modern.',
            'Makanan di sekitar lokasi juga enak-enak.',
            'Aksesnya mudah dan dekat dari pusat kota.',
            'Suasananya tenang dan damai.',
            'Banyak spot foto yang menarik.',
            'Nilai budayanya sangat kaya.',
        ];

        // Create reviews for random destinations
        $destinations->random(min(30, $destinations->count()))->each(function ($destination) use ($users, $comments) {
            // Each destination gets 1-5 reviews
            $reviewCount = rand(1, 5);
            $selectedUsers = $users->random(min($reviewCount, $users->count()));

            foreach ($selectedUsers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'destination_id' => $destination->id,
                    'rating' => rand(3, 5), // Random rating 3-5
                    'comment' => $comments[array_rand($comments)],
                    'is_verified' => (bool) rand(0, 1), // 50% chance verified
                ]);
            }
        });

        $this->command->info('Review seeder berhasil dijalankan!');
    }
}
