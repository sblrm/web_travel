<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use App\Models\Destination;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->numberBetween(25000, 100000);

        return [
            'booking_code' => Booking::generateBookingCode(),
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'visit_date' => $this->faker->dateTimeBetween('+1 days', '+1 month')->format('Y-m-d'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $quantity * $unitPrice,
            'visitor_name' => $this->faker->name(),
            'visitor_email' => $this->faker->safeEmail(),
            'visitor_phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->sentence(),
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
            'confirmed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }
}
