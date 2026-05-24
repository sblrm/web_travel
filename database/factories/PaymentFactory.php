<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'payment_code' => 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'amount' => fake()->numberBetween(10000, 500000),
            'status' => 'pending',
        ];
    }
}
