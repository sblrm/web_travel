<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@culturaltrip.com'],
            [
                'name' => 'Admin CulturalTrip',
                'password' => bcrypt('password'),
            ]
        );

        // Make sure admin exists for Filament
        if (! $admin->wasRecentlyCreated) {
            $this->command->info('Admin user already exists: admin@culturaltrip.com');
        } else {
            $this->command->info('Admin user created: admin@culturaltrip.com / password');
        }

        $this->call([
            ProvinceSeeder::class,
            CategorySeeder::class,
            PaymentMethodSeeder::class,
            DestinationSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
