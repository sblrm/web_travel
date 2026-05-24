<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\PaymentMethod;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->destination = Destination::factory()->create([
        'ticket_price' => 50000,
    ]);
});

it('can view booking creation page with active payment methods', function () {
    PaymentMethod::factory()->create(['is_active' => true, 'name' => 'Active Method']);
    PaymentMethod::factory()->create(['is_active' => false, 'name' => 'Inactive Method']);

    $response = $this->actingAs($this->user)
        ->get(route('bookings.create', $this->destination));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.create');
    $response->assertSee($this->destination->name);
    $response->assertSee('Active Method');
    $response->assertDontSee('Inactive Method');
});

it('cannot view booking creation page if unauthenticated', function () {
    $response = $this->get(route('bookings.create', $this->destination));

    $response->assertRedirect(route('login'));
});

it('can store a new booking successfully', function () {
    $paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);

    $data = [
        'visit_date' => now()->addDays(5)->format('Y-m-d'),
        'quantity' => 2,
        'payment_method_id' => $paymentMethod->id,
        'visitor_name' => 'John Doe',
        'visitor_email' => 'john@example.com',
        'visitor_phone' => '081234567890',
        'notes' => 'Test notes',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('bookings.store', $this->destination), $data);

    $booking = Booking::first();

    $response->assertRedirect(route('bookings.show', $booking));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'user_id' => $this->user->id,
        'destination_id' => $this->destination->id,
        'quantity' => 2,
        'unit_price' => 50000,
        'total_amount' => 100000, // 2 * 50000
        'visitor_name' => 'John Doe',
        'status' => 'awaiting_payment',
    ]);

    $this->assertDatabaseHas('payments', [
        'booking_id' => $booking->id,
        'payment_method_id' => $paymentMethod->id,
        'amount' => 100000,
        'status' => 'pending',
    ]);
});

it('fails to store booking with validation errors', function () {
    $response = $this->actingAs($this->user)
        ->post(route('bookings.store', $this->destination), [
            'visit_date' => now()->subDay()->format('Y-m-d'), // Past date
            'quantity' => 0, // Invalid quantity
        ]);

    $response->assertSessionHasErrors(['visit_date', 'quantity', 'payment_method_id', 'visitor_name', 'visitor_email', 'visitor_phone']);
    $this->assertDatabaseCount('bookings', 0);
});

it('can view own booking details', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'destination_id' => $this->destination->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('bookings.show', $booking));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.show');
    $response->assertSee($booking->booking_code);
});

it('cannot view someone else\'s booking details', function () {
    $otherUser = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('bookings.show', $booking));

    $response->assertForbidden();
});

it('can view list of own bookings', function () {
    Booking::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);
    Booking::factory()->create(); // Someone else's booking

    $response = $this->actingAs($this->user)
        ->get(route('bookings.index'));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.index');
    $response->assertViewHas('bookings');
    $this->assertCount(3, $response->viewData('bookings'));
});

it('can cancel an awaiting_payment booking', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'awaiting_payment',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled',
    ]);
});

it('cannot cancel a booking that is already paid', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'paid',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertForbidden();

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'paid',
    ]);
});

it('cannot cancel someone else\'s booking', function () {
    $otherUser = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $otherUser->id,
        'status' => 'awaiting_payment',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertForbidden();

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'awaiting_payment',
    ]);
});
