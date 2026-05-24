<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(fake()->lexify('????')),
            'amount' => 50000,
            'status' => 'pending',
        ];
    }
}
