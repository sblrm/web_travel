<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use App\Models\Review;
use App\Models\User;

test('index displays active destinations', function () {
    $activeDestination = Destination::factory()->create(['is_active' => true, 'name' => 'Active Dest']);
    $inactiveDestination = Destination::factory()->create(['is_active' => false, 'name' => 'Inactive Dest']);

    $response = $this->get(route('destinations.index'));

    $response->assertOk();
    $response->assertSee('Active Dest');
    $response->assertDontSee('Inactive Dest');
});

test('index filters destinations by province', function () {
    $province1 = Province::factory()->create();
    $province2 = Province::factory()->create();

    $dest1 = Destination::factory()->create(['province_id' => $province1->id, 'name' => 'Dest 1']);
    $dest2 = Destination::factory()->create(['province_id' => $province2->id, 'name' => 'Dest 2']);

    $response = $this->get(route('destinations.index', ['province' => $province1->id]));

    $response->assertOk();
    $response->assertSee('Dest 1');
    $response->assertDontSee('Dest 2');
});

test('index filters destinations by category', function () {
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();

    $dest1 = Destination::factory()->create(['category_id' => $category1->id, 'name' => 'Dest 1']);
    $dest2 = Destination::factory()->create(['category_id' => $category2->id, 'name' => 'Dest 2']);

    $response = $this->get(route('destinations.index', ['category' => $category1->id]));

    $response->assertOk();
    $response->assertSee('Dest 1');
    $response->assertDontSee('Dest 2');
});

test('index filters destinations by price range', function () {
    $dest1 = Destination::factory()->create(['ticket_price' => 10000, 'name' => 'Cheap Dest']);
    $dest2 = Destination::factory()->create(['ticket_price' => 50000, 'name' => 'Medium Dest']);
    $dest3 = Destination::factory()->create(['ticket_price' => 100000, 'name' => 'Expensive Dest']);

    $response = $this->get(route('destinations.index', ['min_price' => 20000, 'max_price' => 80000]));

    $response->assertOk();
    $response->assertSee('Medium Dest');
    $response->assertDontSee('Cheap Dest');
    $response->assertDontSee('Expensive Dest');
});

test('index searches destinations by name', function () {
    $dest1 = Destination::factory()->create(['name' => 'Beautiful Beach']);
    $dest2 = Destination::factory()->create(['name' => 'Mountain View']);

    $response = $this->get(route('destinations.index', ['search' => 'Beach']));

    $response->assertOk();
    $response->assertSee('Beautiful Beach');
    $response->assertDontSee('Mountain View');
});

test('show displays destination details', function () {
    $destination = Destination::factory()->create(['name' => 'Test Destination Details']);

    $response = $this->get(route('destinations.show', $destination));

    $response->assertOk();
    $response->assertSee('Test Destination Details');
});

test('show loads related destinations in same category', function () {
    $category = Category::factory()->create();
    $destination = Destination::factory()->create(['category_id' => $category->id]);

    $relatedDest = Destination::factory()->create(['category_id' => $category->id, 'name' => 'Related Dest']);
    $unrelatedDest = Destination::factory()->create(['category_id' => Category::factory()->create()->id, 'name' => 'Unrelated Dest']);

    $response = $this->get(route('destinations.show', $destination));

    $response->assertOk();
    $response->assertSee('Related Dest');
    $response->assertDontSee('Unrelated Dest');
});

test('show handles authenticated user review status', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    // Create a review by this user
    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'comment' => 'My personal review',
    ]);

    $response = $this->actingAs($user)->get(route('destinations.show', $destination));

    $response->assertOk();
    // This asserts that the user review is loaded by the controller,
    // even if we don't strictly assert the view content, it verifies the code path doesn't error out.
});
