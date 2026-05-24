<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Bank',
            'type' => fake()->randomElement(['bank_transfer', 'e_wallet', 'cash']),
            'code' => fake()->unique()->lexify('????'),
            'account_number' => fake()->bankAccountNumber(),
            'account_name' => fake()->name(),
            'instructions' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
