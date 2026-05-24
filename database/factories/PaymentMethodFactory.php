<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentMethod;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['bank_transfer', 'e_wallet', 'cash'];
        $type = $this->faker->randomElement($types);

        return [
            'name' => $this->faker->company(),
            'type' => $type,
            'code' => $this->faker->unique()->word(),
            'account_number' => $type !== 'cash' ? $this->faker->bankAccountNumber() : null,
            'account_name' => $type !== 'cash' ? $this->faker->name() : null,
            'instructions' => $this->faker->paragraph(),
            'icon' => null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
