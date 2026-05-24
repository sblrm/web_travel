<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;

test('unauthenticated users cannot access bookings index', function () {
    $response = $this->get(route('bookings.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view their bookings index', function () {
    $user = User::factory()->create();

    $booking1 = Booking::factory()->create(['user_id' => $user->id]);
    $booking2 = Booking::factory()->create(['user_id' => $user->id]);
    Payment::factory()->create(['booking_id' => $booking1->id]);
    Payment::factory()->create(['booking_id' => $booking2->id]);

    $response = $this->actingAs($user)->get(route('bookings.index'));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.index');

    // Check that the bookings are passed to the view
    $response->assertViewHas('bookings');

    $viewBookings = $response->original->getData()['bookings'];

    $this->assertCount(2, $viewBookings);

    // Test that the bookings contain the newly created models
    $this->assertTrue($viewBookings->contains($booking1));
    $this->assertTrue($viewBookings->contains($booking2));
});

test('users cannot view other users bookings', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $bookingUser1 = Booking::factory()->create(['user_id' => $user1->id]);
    Payment::factory()->create(['booking_id' => $bookingUser1->id]);

    $bookingUser2 = Booking::factory()->create(['user_id' => $user2->id]);
    Payment::factory()->create(['booking_id' => $bookingUser2->id]);

    $response = $this->actingAs($user1)->get(route('bookings.index'));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.index');

    $viewBookings = $response->original->getData()['bookings'];

    $this->assertCount(1, $viewBookings);
    $this->assertTrue($viewBookings->contains($bookingUser1));
    $this->assertFalse($viewBookings->contains($bookingUser2));
});

test('bookings index displays paginated results', function () {
    $user = User::factory()->create();

    // Create 15 bookings (paginate is 10)
    for ($i = 0; $i < 15; $i++) {
        $booking = Booking::factory()->create(['user_id' => $user->id]);
        Payment::factory()->create(['booking_id' => $booking->id]);
    }

    $response = $this->actingAs($user)->get(route('bookings.index'));

    $response->assertStatus(200);
    $response->assertViewIs('bookings.index');

    $viewBookings = $response->original->getData()['bookings'];

    $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $viewBookings);
    $this->assertCount(10, $viewBookings->items()); // First page should have 10 items
    $this->assertEquals(15, $viewBookings->total());
});
