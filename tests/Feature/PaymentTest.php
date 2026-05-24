<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createTestBookingAndPayment($user, $status = 'pending')
{
    $destination = Destination::factory()->create();

    $booking = Booking::create([
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'visit_date' => now()->addDays(2)->format('Y-m-d'),
        'quantity' => 2,
        'unit_price' => 50000,
        'total_amount' => 100000,
        'visitor_name' => $user->name,
        'visitor_email' => $user->email,
        'visitor_phone' => '081234567890',
        'status' => 'awaiting_payment',
    ]);

    $paymentMethod = PaymentMethod::create([
        'name' => 'Bank Transfer BCA',
        'type' => 'bank_transfer',
        'code' => 'BCA',
        'is_active' => true,
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'payment_method_id' => $paymentMethod->id,
        'amount' => 100000,
        'status' => $status,
    ]);

    return [$booking, $payment];
}

test('user can upload payment proof for their booking', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    [$booking, $payment] = createTestBookingAndPayment($user);

    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
            'notes' => 'Payment for booking',
        ]);

    $response->assertRedirect(route('bookings.show', $booking))
        ->assertSessionHas('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');

    $payment->refresh();

    $this->assertEquals('uploaded', $payment->status);
    $this->assertEquals('John Doe', $payment->account_holder_name);
    $this->assertEquals('BCA', $payment->transfer_from);
    $this->assertEquals('Payment for booking', $payment->notes);
    $this->assertNotNull($payment->paid_at);
    $this->assertNotNull($payment->proof_image);

    Storage::disk('public')->assertExists($payment->proof_image);
});

test('upload fails if validation fails', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    [$booking, $payment] = createTestBookingAndPayment($user);

    // Missing required fields
    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), []);

    $response->assertSessionHasErrors(['proof_image', 'account_holder_name', 'transfer_from']);

    // Invalid image type
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ]);

    $response->assertSessionHasErrors(['proof_image']);
});

test('user cannot upload payment proof for another users booking', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    [$booking, $payment] = createTestBookingAndPayment($user1);

    Storage::fake('public');
    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->actingAs($user2)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ]);

    $response->assertForbidden();
});

test('upload fails if payment status is not pending or rejected', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    // Create with 'uploaded' status
    [$booking, $payment] = createTestBookingAndPayment($user, 'uploaded');

    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $file,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Bukti pembayaran tidak dapat diupload untuk status ini.');
});

test('existing proof image is deleted when re-uploading', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    [$booking, $payment] = createTestBookingAndPayment($user, 'rejected');

    // Create an existing fake image
    $oldFile = UploadedFile::fake()->image('old_proof.jpg');
    $oldPath = $oldFile->store('payment-proofs', 'public');

    $payment->update(['proof_image' => $oldPath]);
    Storage::disk('public')->assertExists($oldPath);

    // Upload a new image
    $newFile = UploadedFile::fake()->image('new_proof.jpg');

    $response = $this->actingAs($user)
        ->post(route('payments.upload', $booking), [
            'proof_image' => $newFile,
            'account_holder_name' => 'John Doe',
            'transfer_from' => 'BCA',
        ]);

    $response->assertRedirect();

    $payment->refresh();

    // Check that old image is deleted
    Storage::disk('public')->assertMissing($oldPath);
    // Check that new image exists
    Storage::disk('public')->assertExists($payment->proof_image);
});
