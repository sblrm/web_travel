<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewApprovedNotification;
use Illuminate\Support\Facades\Notification;

test('notification sent when review is approved', function () {
    Notification::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'is_verified' => false,
    ]);

    // Approve review
    $review->update(['is_verified' => true]);

    Notification::assertSentTo($user, ReviewApprovedNotification::class);
});

test('notification not sent when review is created as unverified', function () {
    Notification::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'is_verified' => false,
    ]);

    Notification::assertNothingSent();
});

test('notification not sent when review is updated but not verified', function () {
    Notification::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'is_verified' => false,
    ]);

    // Update comment but keep unverified
    $review->update(['comment' => 'Updated comment']);

    Notification::assertNothingSent();
});

test('notification contains correct review information', function () {
    Notification::fake();

    $user = User::factory()->create(['name' => 'John Doe']);
    $destination = Destination::factory()->create(['name' => 'Borobudur Temple']);

    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'rating' => 5,
        'is_verified' => false,
    ]);

    // Approve review
    $review->update(['is_verified' => true]);

    Notification::assertSentTo($user, ReviewApprovedNotification::class, function ($notification) use ($review) {
        $mailMessage = $notification->toMail($review->user);

        return $mailMessage->subject === 'Review Anda Disetujui! 🎉';
    });
});
