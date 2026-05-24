<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('it redirects to dashboard if user email is already verified', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('it verifies email, dispatches event, and redirects to dashboard if not verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('it returns 403 if id does not match authenticated user', function () {
    $user = User::factory()->unverified()->create();
    $otherUser = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $otherUser->id, 'hash' => sha1($otherUser->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertStatus(403);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('it returns 403 if hash is invalid', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('invalid-email@example.com')]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertStatus(403);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
