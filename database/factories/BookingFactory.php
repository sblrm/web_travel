<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomElement([25000, 50000, 75000]);

        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'visit_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $quantity * $unitPrice,
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->safeEmail(),
            'visitor_phone' => fake()->phoneNumber(),
            'notes' => fake()->sentence(),
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ];
    }
}
