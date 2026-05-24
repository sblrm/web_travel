<?php

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Str;

test('authenticated user can mark all notifications as read', function () {
    $user = User::factory()->create();

    // Create a destination
    $destination = Destination::factory()->create();

    // Create an unread notification directly
    $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => 'App\Notifications\ReviewApprovedNotification',
        'data' => [
            'destination_slug' => $destination->slug,
            'message' => 'Review Anda disetujui',
        ],
        'read_at' => null,
    ]);

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

    // Create a destination
    $destination = Destination::factory()->create();

    // Create an unread notification directly
    $user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => 'App\Notifications\ReviewApprovedNotification',
        'data' => [
            'destination_slug' => $destination->slug,
            'message' => 'Review Anda disetujui',
        ],
        'read_at' => null,
    ]);

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
