<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;

test('user can edit their own review', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();
    $review = Review::create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'rating' => 3,
        'comment' => 'Original comment',
        'is_verified' => true,
    ]);

    $this->actingAs($user)
        ->patch(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => 'Updated comment',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'rating' => 5,
        'comment' => 'Updated comment',
        'is_verified' => false, // Should reset after edit
    ]);
});

test('user cannot edit other users review', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $destination = Destination::factory()->create();
    $review = Review::create([
        'user_id' => $user1->id,
        'destination_id' => $destination->id,
        'rating' => 3,
        'comment' => 'Original comment',
        'is_verified' => true,
    ]);

    $this->actingAs($user2)
        ->patch(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => 'Hacking attempt',
        ])
        ->assertForbidden();
});

test('destination show page filters reviews by rating', function () {
    $destination = Destination::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create reviews with different ratings
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
    $response = $this->get(route('destinations.show', ['destination' => $destination->slug, 'rating' => 5]));
    $response->assertSee('5 star review');
    $response->assertDontSee('3 star review');

    // Test filter by rating 3
    $response = $this->get(route('destinations.show', ['destination' => $destination->slug, 'rating' => 3]));
    $response->assertSee('3 star review');
    $response->assertDontSee('5 star review');
});

test('destination show page displays rating breakdown', function () {
    $destination = Destination::factory()->create();

    // Create multiple reviews with different users
    for ($i = 1; $i <= 5; $i++) {
        $user = User::factory()->create();
        Review::create([
            'user_id' => $user->id,
            'destination_id' => $destination->id,
            'rating' => $i,
            'comment' => "Review with rating {$i}",
            'is_verified' => true,
        ]);
    }

    $response = $this->get(route('destinations.show', $destination->slug));

    // Should see rating breakdown with all ratings
    $response->assertSee('1⭐');
    $response->assertSee('2⭐');
    $response->assertSee('3⭐');
    $response->assertSee('4⭐');
    $response->assertSee('5⭐');
});
