<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;

test('api returns reviews for destination', function () {
    $destination = Destination::factory()->create();
    $user = User::factory()->create();

    Review::create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'rating' => 5,
        'comment' => 'Great place',
        'is_verified' => true,
    ]);

    $response = $this->getJson(route('api.reviews.index', $destination->slug));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'rating', 'comment', 'user_name', 'formatted_date', 'created_at'],
            ],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

test('api filters reviews by rating', function () {
    $destination = Destination::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Review::create([
        'user_id' => $user1->id,
        'destination_id' => $destination->id,
        'rating' => 5,
        'comment' => '5 star review',
        'is_verified' => true,
    ]);

    Review::create([
        'user_id' => $user2->id,
        'destination_id' => $destination->id,
        'rating' => 3,
        'comment' => '3 star review',
        'is_verified' => true,
    ]);

    // Test filter by rating 5
    $response = $this->getJson(route('api.reviews.index', ['destination' => $destination->slug, 'rating' => 5]));
    $response->assertSuccessful();
    $data = $response->json('data');

    expect($data)->toHaveCount(1);
    expect($data[0]['rating'])->toBe(5);
    expect($data[0]['comment'])->toBe('5 star review');
});

test('api only returns verified reviews', function () {
    $destination = Destination::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Verified review
    Review::create([
        'user_id' => $user1->id,
        'destination_id' => $destination->id,
        'rating' => 5,
        'comment' => 'Verified review',
        'is_verified' => true,
    ]);

    // Unverified review
    Review::create([
        'user_id' => $user2->id,
        'destination_id' => $destination->id,
        'rating' => 4,
        'comment' => 'Unverified review',
        'is_verified' => false,
    ]);

    $response = $this->getJson(route('api.reviews.index', $destination->slug));
    $data = $response->json('data');

    expect($data)->toHaveCount(1);
    expect($data[0]['comment'])->toBe('Verified review');
});
