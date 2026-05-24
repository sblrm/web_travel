<?php

use App\Models\User;

test('unauthenticated users are redirected from analytics dashboard', function () {
    $response = $this->get(route('analytics.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can access analytics dashboard with default dates', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('analytics.index'));

    $response->assertStatus(200);
    $response->assertViewIs('analytics.index');
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

    $this->assertEquals(now()->subDays(30)->format('Y-m-d'), $response->viewData('startDate'));
    $this->assertEquals(now()->format('Y-m-d'), $response->viewData('endDate'));
});

test('authenticated users can access analytics dashboard with custom dates', function () {
    $user = User::factory()->create();

    $startDate = '2023-01-01';
    $endDate = '2023-12-31';

    $response = $this->actingAs($user)->get(route('analytics.index', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('analytics.index');
    $response->assertViewHasAll([
        'startDate',
        'endDate',
    ]);

    $this->assertEquals($startDate, $response->viewData('startDate'));
    $this->assertEquals($endDate, $response->viewData('endDate'));
});
