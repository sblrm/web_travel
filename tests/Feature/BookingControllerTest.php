<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('authenticated user can cancel their own pending booking', function () {
    Event::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    // Explicitly set booking_code and expires_at since they might not be generated in some mock contexts
    // Wait, the boot creating method might be skipped if events are faked.
    // Let's manually provide them or use the model's static generateBookingCode method
    $booking = Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 1,
        'unit_price' => $destination->ticket_price,
        'total_amount' => $destination->ticket_price,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'status' => 'pending',
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this
        ->actingAs($user)
        ->post("/bookings/{$booking->id}/cancel");

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Booking berhasil dibatalkan.');

    $booking->refresh();

    $this->assertTrue($booking->isCancelled());
    $this->assertEquals('Dibatalkan oleh user', $booking->cancellation_reason);
    $this->assertNotNull($booking->cancelled_at);
});

test('authenticated user can cancel their own awaiting_payment booking', function () {
    Event::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 1,
        'unit_price' => $destination->ticket_price,
        'total_amount' => $destination->ticket_price,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'status' => 'awaiting_payment',
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this
        ->actingAs($user)
        ->post("/bookings/{$booking->id}/cancel");

    $response
        ->assertRedirect()
        ->assertSessionHas('success', 'Booking berhasil dibatalkan.');

    $booking->refresh();

    $this->assertTrue($booking->isCancelled());
});

test('authenticated user cannot cancel booking with unallowed status', function () {
    Event::fake();

    $user = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 1,
        'unit_price' => $destination->ticket_price,
        'total_amount' => $destination->ticket_price,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'status' => 'paid',
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this
        ->actingAs($user)
        ->from("/bookings/{$booking->id}")
        ->post("/bookings/{$booking->id}/cancel");

    $response
        ->assertRedirect("/bookings/{$booking->id}")
        ->assertSessionHas('error', 'Booking tidak dapat dibatalkan.');

    $booking->refresh();
    $this->assertFalse($booking->isCancelled());
    $this->assertEquals('paid', $booking->status);
});

test('user cannot cancel someone elses booking', function () {
    Event::fake();

    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'user_id' => $owner->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 1,
        'unit_price' => $destination->ticket_price,
        'total_amount' => $destination->ticket_price,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'status' => 'pending',
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this
        ->actingAs($otherUser)
        ->post("/bookings/{$booking->id}/cancel");

    $response->assertStatus(403);

    $booking->refresh();
    $this->assertFalse($booking->isCancelled());
});

test('unauthenticated user cannot cancel booking', function () {
    Event::fake();

    $owner = User::factory()->create();
    $destination = Destination::factory()->create();

    $booking = Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'user_id' => $owner->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 1,
        'unit_price' => $destination->ticket_price,
        'total_amount' => $destination->ticket_price,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'status' => 'pending',
        'expires_at' => now()->addHours(24),
    ]);

    $response = $this->post("/bookings/{$booking->id}/cancel");

    $response->assertRedirect('/login');

    $booking->refresh();
    $this->assertFalse($booking->isCancelled());
});
