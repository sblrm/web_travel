<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional(0.8)->paragraph(),
            'is_verified' => fake()->boolean(70),
            'images' => fake()->optional(0.3)->randomElements([
                'reviews/sample1.jpg',
                'reviews/sample2.jpg',
                'reviews/sample3.jpg',
            ], fake()->numberBetween(1, 3)),
            'helpful_count' => fake()->numberBetween(0, 20),
            'unhelpful_count' => fake()->numberBetween(0, 5),
        ];
    }

    /**
     * Indicate that the review is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the review is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
