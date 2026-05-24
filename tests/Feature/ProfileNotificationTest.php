<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewApprovedNotification;
use Illuminate\Support\Facades\Notification;

test('authenticated user can mark all notifications as read', function () {
    $user = User::factory()->create();

    // Create a destination and review to trigger a notification
    $destination = Destination::factory()->create();
    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'is_verified' => false,
    ]);

    // Send notification
    $user->notify(new ReviewApprovedNotification($review));

    // Verify notification is unread
    $this->assertCount(1, $user->unreadNotifications);

    $response = $this
        ->actingAs($user)
        ->post('/notifications/mark-all-read');

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Semua notifikasi telah ditandai sebagai dibaca.');

    // Verify all notifications are now read
    $this->assertCount(0, $user->fresh()->unreadNotifications);
});

test('unauthenticated user cannot mark notifications as read', function () {
    $response = $this->post('/notifications/mark-all-read');

    $response->assertRedirect('/login');
});

test('authenticated user can mark specific notification as read', function () {
    $user = User::factory()->create();

    // Create a destination and review to trigger a notification
    $destination = Destination::factory()->create();
    $review = Review::factory()->create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'is_verified' => false,
    ]);

    // Send notification
    $user->notify(new ReviewApprovedNotification($review));

    // Verify notification is unread
    $this->assertCount(1, $user->unreadNotifications);

    $notification = $user->unreadNotifications->first();

    $response = $this
        ->actingAs($user)
        ->get("/notifications/{$notification->id}/read");

    $response->assertRedirect(); // The method redirects either back or to destination.slug

    // Verify specific notification is now read
    $this->assertCount(0, $user->fresh()->unreadNotifications);
});
