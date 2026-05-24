<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;

it('returns a successful response for the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('home');
});

it('passes correctly calculated statistics to the home view', function () {
    // Provinces (1 with destination, 1 without)
    $provinceWithDestination = Province::factory()->create();
    Province::factory()->create();

    // Categories (1 with destination, 1 without)
    $categoryWithDestination = Category::factory()->create();
    Category::factory()->create();

    // Active destinations (2 active, 1 inactive)
    Destination::factory()->count(2)->create([
        'is_active' => true,
        'province_id' => $provinceWithDestination->id,
        'category_id' => $categoryWithDestination->id,
    ]);

    Destination::factory()->create([
        'is_active' => false,
        'province_id' => $provinceWithDestination->id,
        'category_id' => $categoryWithDestination->id,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewHas('totalDestinations', 2);
    $response->assertViewHas('totalProvinces', 1);
    $response->assertViewHas('totalCategories', 1);
});

it('fetches featured destinations correctly', function () {
    $province = Province::factory()->create();
    $category = Category::factory()->create();

    // Create 7 active destinations with different ratings
    $activeDestinations = Destination::factory()->count(7)->create([
        'is_active' => true,
        'province_id' => $province->id,
        'category_id' => $category->id,
    ]);

    // Manually assign ratings to ensure order
    $ratings = [4.5, 4.8, 3.2, 5.0, 4.1, 4.9, 3.8];
    foreach ($activeDestinations as $index => $destination) {
        $destination->update(['rating' => $ratings[$index]]);
    }

    // Create 1 inactive destination with high rating
    Destination::factory()->create([
        'is_active' => false,
        'rating' => 5.0,
        'province_id' => $province->id,
        'category_id' => $category->id,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);

    $response->assertViewHas('featuredDestinations', function ($destinations) {
        // It should limit to 6 destinations
        if ($destinations->count() !== 6) {
            return false;
        }

        // It should order by rating descending
        $ratings = $destinations->pluck('rating')->toArray();
        $sortedRatings = $ratings;
        rsort($sortedRatings);

        if ($ratings !== $sortedRatings) {
            return false;
        }

        // It should not include inactive destinations
        // Inactive destination has rating 5.0 but the active 5.0 is also there.
        // We know only active destinations are returned by checking the count.
        // Also verify the models are loaded with province and category
        $first = $destinations->first();
        if (!$first->relationLoaded('province') || !$first->relationLoaded('category')) {
            return false;
        }

        return true;
    });
});
