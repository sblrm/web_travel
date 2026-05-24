<?php

use App\Models\Destination;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
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

test('mark notification as read redirects to destination if destination_slug exists', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $notification = DatabaseNotification::forceCreate([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\SomeNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['destination_slug' => $destination->slug],
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/notifications/{$notification->id}/read");

    $response->assertRedirect("/destinasi/{$destination->slug}");
    $this->assertNotNull($notification->fresh()->read_at);
});

test('mark notification as read redirects back if destination_slug does not exist', function () {
    $user = User::factory()->create();

    $notification = DatabaseNotification::forceCreate([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\SomeNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['some_other_data' => 'value'],
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->get("/notifications/{$notification->id}/read");

    $response->assertRedirect('/profile');
    $this->assertNotNull($notification->fresh()->read_at);
});

test('mark notification as read returns 404 if notification belongs to another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $notification = DatabaseNotification::forceCreate([
        'id' => Str::uuid(),
        'type' => 'App\Notifications\SomeNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $otherUser->id,
        'data' => [],
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/notifications/{$notification->id}/read");

    $response->assertNotFound();
    $this->assertNull($notification->fresh()->read_at);
});
