<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Bank Transfer '.fake()->company(),
            'type' => 'bank_transfer',
            'code' => fake()->unique()->word(),
            'account_number' => fake()->numerify('##########'),
            'account_name' => fake()->name(),
            'instructions' => 'Please transfer to the account number.',
            'is_active' => true,
            'sort_order' => 1,
        ];
    }
}
