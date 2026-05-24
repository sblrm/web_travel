<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createBookingForUser(User $user, string $status = 'awaiting_payment'): Booking
{
    $destination = Destination::factory()->create();

    return Booking::create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2),
        'quantity' => 2,
        'unit_price' => 50000,
        'total_amount' => 100000,
        'visitor_name' => $user->name,
        'visitor_email' => $user->email,
        'visitor_phone' => '081234567890',
        'status' => $status,
        'expires_at' => now()->addHours(24),
    ]);
}

function createPaymentForBooking(Booking $booking, string $status = 'pending'): Payment
{
    $paymentMethod = PaymentMethod::create([
        'name' => 'Bank Transfer',
        'type' => 'bank_transfer',
        'code' => 'BCA',
        'account_number' => '1234567890',
        'account_name' => 'Test Admin',
        'is_active' => true,
    ]);

    return Payment::create([
        'booking_id' => $booking->id,
        'payment_method_id' => $paymentMethod->id,
        'amount' => $booking->total_amount,
        'status' => $status,
    ]);
}

// Tests for show method
test('user can view payment page for their booking', function () {
    $user = User::factory()->create();
    $booking = createBookingForUser($user);
    $payment = createPaymentForBooking($booking);

    $this->actingAs($user)
        ->get(route('payments.show', $booking))
        ->assertOk()
        ->assertViewIs('payments.show')
        ->assertViewHas('booking');
});

test('user cannot view payment page if booking has no payment', function () {
    $user = User::factory()->create();
    $booking = createBookingForUser($user);

    $this->actingAs($user)
        ->get(route('payments.show', $booking))
        ->assertNotFound();
});

test('user cannot view payment page of another users booking', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $booking = createBookingForUser($user1);
    createPaymentForBooking($booking);

    $this->actingAs($user2)
        ->get(route('payments.show', $booking))
        ->assertForbidden();
});

// Tests for upload method
test('user can upload payment proof successfully', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $booking = createBookingForUser($user);
    $payment = createPaymentForBooking($booking);

    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
            'notes' => 'Test notes',
        ]);

    $response->assertRedirect(route('bookings.show', $booking))
        ->assertSessionHas('success');

    $payment->refresh();

    $this->assertEquals('uploaded', $payment->status);
    $this->assertEquals('John Doe', $payment->account_holder_name);
    $this->assertEquals('BCA', $payment->transfer_from);
    $this->assertEquals('Test notes', $payment->notes);
    $this->assertNotNull($payment->proof_image);

    Storage::disk('public')->assertExists($payment->proof_image);
});

test('user cannot upload payment proof if booking status is not awaiting payment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $booking = createBookingForUser($user, 'paid');
    $payment = createPaymentForBooking($booking);

    $file = UploadedFile::fake()->image('proof.jpg');

    $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ])
        ->assertForbidden();
});

test('user cannot upload payment proof if payment status does not allow it', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $booking = createBookingForUser($user);
    $payment = createPaymentForBooking($booking, 'uploaded');

    $file = UploadedFile::fake()->image('proof.jpg');

    $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('user cannot upload invalid payment proof', function () {
    $user = User::factory()->create();
    $booking = createBookingForUser($user);
    $payment = createPaymentForBooking($booking);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ])
        ->assertSessionHasErrors(['proof_image']);
});

test('upload deletes old proof image if it exists', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $booking = createBookingForUser($user);
    $payment = createPaymentForBooking($booking, 'rejected');

    $oldFile = UploadedFile::fake()->image('old_proof.jpg');
    $oldPath = $oldFile->store('payment-proofs', 'public');
    $payment->update(['proof_image' => $oldPath]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new_proof.jpg');

    $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $newFile,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ]);

    Storage::disk('public')->assertMissing($oldPath);

    $payment->refresh();
    Storage::disk('public')->assertExists($payment->proof_image);
});

// Tests for invoice method
test('user can view invoice for paid booking', function () {
    $user = User::factory()->create();
    $booking = createBookingForUser($user, 'paid');
    $payment = createPaymentForBooking($booking, 'verified');

    $this->actingAs($user)
        ->get(route('payments.invoice', $booking))
        ->assertOk()
        ->assertViewIs('payments.invoice')
        ->assertViewHas('booking');
});

test('user cannot view invoice for unpaid booking', function () {
    $user = User::factory()->create();
    $booking = createBookingForUser($user, 'awaiting_payment');
    $payment = createPaymentForBooking($booking, 'pending');

    $this->actingAs($user)
        ->get(route('payments.invoice', $booking))
        ->assertForbidden();
});

test('user cannot view invoice of another users booking', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $booking = createBookingForUser($user1, 'paid');
    createPaymentForBooking($booking, 'verified');

    $this->actingAs($user2)
        ->get(route('payments.invoice', $booking))
        ->assertForbidden();
});
