<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

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
            'payment_code' => Payment::generatePaymentCode(),
            'amount' => $this->faker->numberBetween(50000, 500000),
            'status' => 'pending',
        ];
    }
}
