<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use App\Models\Review;
use App\Models\User;

test('unauthenticated user cannot access analytics dashboard', function () {
    $response = $this->get(route('analytics.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can view analytics dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('analytics.index'));

    $response->assertStatus(200);
    $response->assertViewIs('analytics.index');
});

test('analytics dashboard processes custom date parameters', function () {
    $user = User::factory()->create();

    $startDate = '2023-01-01';
    $endDate = '2023-12-31';

    $response = $this->actingAs($user)->get(route('analytics.index', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));

    $response->assertStatus(200);
    $response->assertViewHas('startDate', $startDate);
    $response->assertViewHas('endDate', $endDate);
});

test('analytics dashboard view receives correct data structures', function () {
    $user = User::factory()->create();

    // Create some data so the queries have results
    $province = Province::factory()->create();
    $category = Category::factory()->create();

    $destination = Destination::factory()->create([
        'province_id' => $province->id,
        'category_id' => $category->id,
    ]);

    // A user can only review a destination once due to unique constraint,
    // so we create multiple users to leave reviews on the same destination
    $users = User::factory()->count(3)->create();

    foreach ($users as $reviewer) {
        Review::factory()->create([
            'destination_id' => $destination->id,
            'user_id' => $reviewer->id,
            'is_verified' => true,
            'rating' => 4,
        ]);
    }

    $response = $this->actingAs($user)->get(route('analytics.index'));

    $response->assertStatus(200);

    $response->assertViewHasAll([
        'stats',
        'ratingDistribution',
        'categoryPerformance',
        'topDestinations',
        'topReviewers',
        'reviewsTrend',
        'provinceDistribution',
        'startDate',
        'endDate',
    ]);

    $viewData = $response->original->gatherData();

    // Check stats structure
    $this->assertArrayHasKey('total_destinations', $viewData['stats']);
    $this->assertArrayHasKey('total_reviews', $viewData['stats']);
    $this->assertArrayHasKey('verified_reviews', $viewData['stats']);
    $this->assertArrayHasKey('average_rating', $viewData['stats']);
    $this->assertArrayHasKey('total_users', $viewData['stats']);
    $this->assertArrayHasKey('active_reviewers', $viewData['stats']);

    // Rating distribution should have keys 1 to 5
    $this->assertCount(5, $viewData['ratingDistribution']);

    // Data is present
    $this->assertTrue($viewData['categoryPerformance']->count() > 0);
    $this->assertTrue($viewData['topDestinations']->count() > 0);
    $this->assertTrue($viewData['topReviewers']->count() > 0);
    $this->assertTrue($viewData['provinceDistribution']->count() > 0);
});
