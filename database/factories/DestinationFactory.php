<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'province_id' => Province::factory(),
            'category_id' => Category::factory(),
            'city' => fake()->city(),
            'description' => fake()->paragraph(),
            'latitude' => fake()->latitude(-10, 6),
            'longitude' => fake()->longitude(95, 141),
            'opening_hours' => '08:00',
            'closing_hours' => '17:00',
            'est_visit_duration' => fake()->numberBetween(60, 240),
            'ticket_price' => fake()->randomElement([25000, 30000, 35000, 40000, 45000, 50000]),
            'rating' => fake()->randomFloat(1, 3.5, 5.0),
            'images' => [],
            'is_active' => true,
        ];
    }
}
