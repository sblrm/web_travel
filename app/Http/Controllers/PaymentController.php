<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load('payment.paymentMethod');

        if (! $booking->payment) {
            abort(404, 'Payment not found');
        }

        return view('payments.show', compact('booking'));
    }

    public function upload(UploadPaymentProofRequest $request, Booking $booking)
    {
        $this->authorize('uploadPayment', $booking);

        $payment = $booking->payment;

        if (! $payment->canUploadProof()) {
            return back()->with('error', 'Bukti pembayaran tidak dapat diupload untuk status ini.');
        }

        $validated = $request->validated();

        if ($payment->proof_image) {
            Storage::disk('public')->delete($payment->proof_image);
        }

        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');

        $payment->update([
            'proof_image' => $proofPath,
            'account_holder_name' => $validated['account_holder_name'],
            'transfer_from' => $validated['transfer_from'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $payment->markAsUploaded();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    public function invoice(Booking $booking)
    {
        $this->authorize('view', $booking);

        if (! $booking->isPaid()) {
            abort(403, 'Invoice hanya tersedia untuk booking yang sudah dibayar');
        }

        $booking->load(['destination', 'payment.paymentMethod', 'user']);

        return view('payments.invoice', compact('booking'));
    }
}
