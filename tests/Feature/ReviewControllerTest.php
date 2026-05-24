<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

test('can store a new review', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('reviews.store', $destination), [
            'rating' => 4,
            'comment' => 'This is a great place!',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Review Anda berhasil dikirim dan menunggu verifikasi admin.');

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'rating' => 4,
        'comment' => 'This is a great place!',
        'is_verified' => false,
    ]);
});

test('can store a review with images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $image1 = UploadedFile::fake()->image('photo1.jpg');
    $image2 = UploadedFile::fake()->image('photo2.png');

    $response = $this->actingAs($user)
        ->post(route('reviews.store', $destination), [
            'rating' => 5,
            'comment' => 'Amazing views!',
            'images' => [$image1, $image2],
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Review Anda berhasil dikirim dan menunggu verifikasi admin.');

    $review = Review::where('user_id', $user->id)
        ->where('destination_id', $destination->id)
        ->first();

    expect($review)->not->toBeNull();
    expect($review->images)->toHaveCount(2);

    Storage::disk('public')->assertExists($review->images[0]);
    Storage::disk('public')->assertExists($review->images[1]);
});

test('respects rate limiting when storing reviews', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    // The limit is 5 per hour
    for ($i = 0; $i < 5; $i++) {
        // Need to create different destinations to avoid StoreReviewRequest unique review constraint
        $loopDestination = Destination::factory()->create();

        $this->actingAs($user)
            ->post(route('reviews.store', $loopDestination), [
                'rating' => 3,
                'comment' => "Review number $i",
            ])
            ->assertSessionHas('success');
    }

    // The 6th request should be rate limited
    $extraDestination = Destination::factory()->create();
    $response = $this->actingAs($user)
        ->post(route('reviews.store', $extraDestination), [
            'rating' => 3,
            'comment' => 'This one should be rate limited',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(session('error'))->toContain('Terlalu banyak review');
});

test('handles exceptions gracefully', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    Log::shouldReceive('error')->once();

    // Create the review first, so attempting to create it again normally fails
    // due to StoreReviewRequest (already reviewed), BUT we want to hit the exception inside the controller.
    // Instead of mocking DB, we can just mock the UploadedFile store method to throw an exception if we upload a file.

    Storage::fake('public');

    $image = UploadedFile::fake()->image('photo.jpg');

    // We can use a partial mock on the uploaded file to throw an exception on store
    $mockImage = Mockery::mock($image)->makePartial();
    $mockImage->shouldReceive('store')->andThrow(new \Exception('Upload failed'));

    $response = $this->actingAs($user)
        ->post(route('reviews.store', $destination), [
            'rating' => 4,
            'comment' => 'Test comment',
            'images' => [$mockImage],
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Gagal mengirim review. Silakan coba lagi.');
});
