<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;

test('authenticated user can view their own booking', function () {
    $user = User::factory()->create();
    $destination = Destination::factory()->create();
    $paymentMethod = PaymentMethod::create([
        'name' => 'Bank Transfer',
        'type' => 'bank_transfer',
        'code' => 'BCA',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $booking = Booking::withoutEvents(function () use ($user, $destination) {
        return Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'user_id' => $user->id,
            'destination_id' => $destination->id,
            'visit_date' => now()->addDays(2),
            'quantity' => 2,
            'unit_price' => 50000,
            'total_amount' => 100000,
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'visitor_phone' => '081234567890',
            'status' => 'awaiting_payment',
            'expires_at' => now()->addHours(24),
        ]);
    });

    Payment::create([
        'booking_id' => $booking->id,
        'payment_method_id' => $paymentMethod->id,
        'amount' => $booking->total_amount,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('bookings.show', $booking));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.show');
    $response->assertSee($booking->booking_code);
    $response->assertSee($destination->name);
});

test('authenticated user cannot view another users booking', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::withoutEvents(function () use ($owner, $destination) {
        return Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'user_id' => $owner->id,
            'destination_id' => $destination->id,
            'visit_date' => now()->addDays(2),
            'quantity' => 1,
            'unit_price' => 50000,
            'total_amount' => 50000,
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'visitor_phone' => '081234567890',
            'status' => 'awaiting_payment',
            'expires_at' => now()->addHours(24),
        ]);
    });

    $response = $this->actingAs($otherUser)->get(route('bookings.show', $booking));

    $response->assertStatus(403);
});

test('unauthenticated user is redirected to login when trying to view a booking', function () {
    $owner = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::withoutEvents(function () use ($owner, $destination) {
        return Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'user_id' => $owner->id,
            'destination_id' => $destination->id,
            'visit_date' => now()->addDays(2),
            'quantity' => 1,
            'unit_price' => 50000,
            'total_amount' => 50000,
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'visitor_phone' => '081234567890',
            'status' => 'awaiting_payment',
            'expires_at' => now()->addHours(24),
        ]);
    });

    $response = $this->get(route('bookings.show', $booking));

    $response->assertRedirect('/login');
});
