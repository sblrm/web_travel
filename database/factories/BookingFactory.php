<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_code' => 'BK-' . now()->format('Ymd') . '-' . strtoupper(fake()->lexify('????')),
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'visit_date' => fake()->dateTimeBetween('+1 days', '+1 month'),
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => 50000,
            'total_amount' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_price'];
            },
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->safeEmail(),
            'visitor_phone' => fake()->phoneNumber(),
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ];
    }
}
