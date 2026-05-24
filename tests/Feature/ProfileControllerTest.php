<?php

use App\Models\User;
use Illuminate\Support\Str;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('user can mark a notification as read and redirect back if no destination_slug is provided', function () {
    $user = User::factory()->create();
    $notificationId = (string) Str::uuid();
    $user->notifications()->create([
        'id' => $notificationId,
        'type' => 'App\Notifications\TestNotification',
        'data' => ['message' => 'Test Notification'],
        'read_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/dashboard')
        ->get("/notifications/{$notificationId}/read");

    $response->assertRedirect('/dashboard');
    $this->assertNotNull($user->notifications()->first()->read_at);
});

test('user can mark a notification as read and redirect to destination if destination_slug is provided', function () {
    $user = User::factory()->create();
    $notificationId = (string) Str::uuid();
    $user->notifications()->create([
        'id' => $notificationId,
        'type' => 'App\Notifications\TestNotification',
        'data' => [
            'message' => 'Test Notification',
            'destination_slug' => 'test-destination'
        ],
        'read_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/notifications/{$notificationId}/read");

    $response->assertRedirect(route('destinations.show', 'test-destination'));
    $this->assertNotNull($user->notifications()->first()->read_at);
});

test('user gets 404 when marking another users notification as read', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $notificationId = (string) Str::uuid();
    $user1->notifications()->create([
        'id' => $notificationId,
        'type' => 'App\Notifications\TestNotification',
        'data' => ['message' => 'Test Notification'],
        'read_at' => null,
    ]);

    $response = $this
        ->actingAs($user2)
        ->get("/notifications/{$notificationId}/read");

    $response->assertNotFound();
    $this->assertNull($user1->notifications()->first()->read_at);
});

test('user can mark all notifications as read', function () {
    $user = User::factory()->create();

    // Create multiple unread notifications
    for ($i = 0; $i < 3; $i++) {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => "Test Notification {$i}"],
            'read_at' => null,
        ]);
    }

    $this->assertEquals(3, $user->unreadNotifications()->count());

    $response = $this
        ->actingAs($user)
        ->from('/dashboard')
        ->post("/notifications/mark-all-read");

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    $this->assertEquals(0, $user->unreadNotifications()->count());
});
