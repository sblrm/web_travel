<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use App\Models\Review;
use App\Models\User;

test('show displays the destination details and verified reviews', function () {
    $province = Province::factory()->create();
    $category = Category::factory()->create();

    $destination = Destination::factory()->create([
        'province_id' => $province->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    // Create a verified review
    $verifiedReview = Review::factory()->verified()->create([
        'destination_id' => $destination->id,
    ]);

    // Create an unverified review
    $unverifiedReview = Review::factory()->unverified()->create([
        'destination_id' => $destination->id,
    ]);

    // Create related destination
    $relatedDestination = Destination::factory()->create([
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->get(route('destinations.show', $destination->slug));

    $response->assertStatus(200);
    $response->assertViewIs('destinations.show');

    $response->assertViewHas('destination', function ($viewDestination) use ($destination) {
        return $viewDestination->id === $destination->id;
    });

    $response->assertViewHas('reviews', function ($reviews) use ($verifiedReview, $unverifiedReview) {
        return $reviews->contains($verifiedReview) && !$reviews->contains($unverifiedReview);
    });

    $response->assertViewHas('relatedDestinations', function ($relatedDestinations) use ($relatedDestination) {
        return $relatedDestinations->contains($relatedDestination);
    });

    $response->assertViewHas('userReview', null);
});

test('show filters reviews by rating when requested', function () {
    $destination = Destination::factory()->create();

    // Create a 5-star verified review
    $fiveStarReview = Review::factory()->verified()->create([
        'destination_id' => $destination->id,
        'rating' => 5,
    ]);

    // Create a 3-star verified review
    $threeStarReview = Review::factory()->verified()->create([
        'destination_id' => $destination->id,
        'rating' => 3,
    ]);

    $response = $this->get(route('destinations.show', ['destination' => $destination->slug, 'rating' => 5]));

    $response->assertStatus(200);
    $response->assertViewHas('reviews', function ($reviews) use ($fiveStarReview, $threeStarReview) {
        return $reviews->contains($fiveStarReview) && !$reviews->contains($threeStarReview);
    });
});

test('show includes the current users review if authenticated', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $userReview = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
    ]);

    $response = $this->actingAs($user)->get(route('destinations.show', $destination->slug));

    $response->assertStatus(200);
    $response->assertViewHas('userReview', function ($viewUserReview) use ($userReview) {
        return $viewUserReview->id === $userReview->id;
    });
});
