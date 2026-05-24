<?php

use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentMethod;

test('authenticated user can view invoice for paid booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'status' => 'paid',
    ]);

    $paymentMethod = PaymentMethod::factory()->create();
    Payment::factory()->create([
        'booking_id' => $booking->id,
        'payment_method_id' => $paymentMethod->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('payments.invoice', $booking));

    $response->assertStatus(200);
    $response->assertViewIs('payments.invoice');
    $response->assertViewHas('booking');
});

test('authenticated user cannot view invoice for unpaid booking', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'status' => 'awaiting_payment',
    ]);

    $response = $this->actingAs($user)
        ->get(route('payments.invoice', $booking));

    $response->assertStatus(403);
    $response->assertSee('Invoice hanya tersedia untuk booking yang sudah dibayar');
});

test('authenticated user cannot view invoice for other users booking', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $booking = Booking::factory()->create([
        'user_id' => $user1->id,
        'status' => 'paid',
    ]);

    $response = $this->actingAs($user2)
        ->get(route('payments.invoice', $booking));

    $response->assertStatus(403);
});
