<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\User;

beforeEach(function () {
    $this->destination = Destination::factory()->create();
    $this->reviewer = User::factory()->create();
    $this->voter = User::factory()->create();

    $this->review = Review::factory()->create([
        'destination_id' => $this->destination->id,
        'user_id' => $this->reviewer->id,
        'is_verified' => true,
        'helpful_count' => 0,
        'unhelpful_count' => 0,
    ]);
});

test('authenticated user can vote helpful on review', function () {
    $this->actingAs($this->voter)
        ->post(route('reviews.vote', $this->review), [
            'is_helpful' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('review_votes', [
        'review_id' => $this->review->id,
        'user_id' => $this->voter->id,
        'is_helpful' => true,
    ]);

    $this->assertEquals(1, $this->review->fresh()->helpful_count);
});

test('authenticated user can vote unhelpful on review', function () {
    $this->actingAs($this->voter)
        ->post(route('reviews.vote', $this->review), [
            'is_helpful' => false,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('review_votes', [
        'review_id' => $this->review->id,
        'user_id' => $this->voter->id,
        'is_helpful' => false,
    ]);

    $this->assertEquals(1, $this->review->fresh()->unhelpful_count);
});

test('user can toggle vote from helpful to unhelpful', function () {
    // First vote helpful
    ReviewVote::create([
        'review_id' => $this->review->id,
        'user_id' => $this->voter->id,
        'is_helpful' => true,
    ]);
    $this->review->update(['helpful_count' => 1]);

    // Vote unhelpful
    $this->actingAs($this->voter)
        ->post(route('reviews.vote', $this->review), [
            'is_helpful' => false,
        ]);

    $vote = ReviewVote::where('review_id', $this->review->id)
        ->where('user_id', $this->voter->id)
        ->first();

    expect($vote->is_helpful)->toBeFalse();
    expect($this->review->fresh()->helpful_count)->toBe(0);
    expect($this->review->fresh()->unhelpful_count)->toBe(1);
});

test('user can remove vote by clicking same button', function () {
    // First vote helpful
    ReviewVote::create([
        'review_id' => $this->review->id,
        'user_id' => $this->voter->id,
        'is_helpful' => true,
    ]);
    $this->review->update(['helpful_count' => 1]);

    // Click helpful again (toggle off)
    $this->actingAs($this->voter)
        ->post(route('reviews.vote', $this->review), [
            'is_helpful' => true,
        ]);

    $this->assertDatabaseMissing('review_votes', [
        'review_id' => $this->review->id,
        'user_id' => $this->voter->id,
    ]);

    expect($this->review->fresh()->helpful_count)->toBe(0);
});

test('user cannot vote on their own review', function () {
    $this->actingAs($this->reviewer)
        ->post(route('reviews.vote', $this->review), [
            'is_helpful' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('review_votes', [
        'review_id' => $this->review->id,
        'user_id' => $this->reviewer->id,
    ]);
});

test('guest cannot vote on review', function () {
    $this->post(route('reviews.vote', $this->review), [
        'is_helpful' => true,
    ])
        ->assertRedirect(route('login'));

    $this->assertDatabaseMissing('review_votes', [
        'review_id' => $this->review->id,
    ]);
});
